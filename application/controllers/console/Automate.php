<?php

use GuzzleHttp\Exception\GuzzleException;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Automate
 * @property ReportModel $report
 * @property CustomerModel $customer
 * @property CustomerContactModel $customerContact
 * @property SppcPaymentModel $sppcPayment
 * @property SppcDetailModel $sppcDetail
 * @property AccountReceivableModel $accountReceivable
 * @property AccountPayablePaymentModel $accountPayablePayment
 * @property CashBondModel $cashBond
 * @property CashBondDetailModel $cashBondDetail
 * @property StatusHistoryModel $statusHistory
 * @property UserModel $user
 * @property EmployeeModel $employee
 * @property Mailer $mailer
 * @property Exporter $exporter
 * @property WarehouseAPI $apiClient
 */
class Automate extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (is_cli() || ENVIRONMENT == 'development') {
            echo 'Automate module is initiating...' . PHP_EOL;
        } else {
            echo "This module is CLI only!" . PHP_EOL;
            die();
        }
    }

    /**
     * Clean old temp upload files.
     * call in terminal: `php index.php automate clean-old-temp 14`
     *
     * @param int $age in days
     * @throws Exception
     */
    public function clean_old_temp($age = 7)
    {
        $this->load->helper('directory');

        $path = './uploads/temp/';
        $map = directory_map($path, 1);
        $totalOldFiles = 0;
        $totalOldDirs = 0;
        $today = new DateTime();

        foreach ($map as $file) {
            if (is_dir($path . $file)) {
                $stat = stat($path . $file);
                $dirTimestamp = new DateTime(date("F d Y H:i:s.", $stat['mtime']));
                $dirInterval = $today->diff($dirTimestamp)->format('%R%a');
                if (intval($dirInterval) <= -$age) {
                    if (@rmdir($path . $file)) {
                        echo 'Directory: ' . ($path . $file) . ' was deleted' . PHP_EOL;
                        $totalOldDirs++;
                    }
                }
            }

            $fileTimestamp = new DateTime(date("F d Y H:i:s.", filectime($path . $file)));
            $interval = $today->diff($fileTimestamp)->format('%R%a');
            if (intval($interval) <= -$age && $file != '.gitkeep') {
                if (file_exists($path . $file)) {
                    if (@unlink($path . $file)) {
                        echo 'File: ' . ($path . $file) . ' was deleted' . PHP_EOL;
                        $totalOldFiles++;
                    }
                }
            }
        }
        echo $totalOldFiles . ' files and ' . $totalOldDirs . ' directories were deleted (more than ' . $age . ' days old)' . PHP_EOL;
    }

    /**
     * Send email invoice summary to customer.
     */
    public function customer_report()
    {
        $this->load->model('ReportModel', 'report');
        $this->load->model('CustomerModel', 'customer');
        $this->load->model('CustomerContactModel', 'customerContact');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Exporter', 'exporter');

        $timestamp = strtotime('now');
        $day = date('w', $timestamp);
        $date = format_date('now', 'd F Y');

        try {
            $today = new DateTime();
            $today->sub(new DateInterval("P1D"));
            $yesterday = $today->format('d F Y');
        } catch (Exception $e) {
            $yesterday = 'Yesterday';
        }

        $customers = $this->customer->getBy(['email_reminder_day' => $day]);

        foreach ($customers as $customer) {
            $invoiceData = $this->report->getCustomerInvoice([
                'customer' => $customer['id'],
                'overdue_status' => ['OVERDUE', 'NOT YET']
            ]);

            if (!empty($invoiceData)) {
                $emailTitle = "{$customer['customer_name']} outstanding report on {$date}";
                $emailTemplate = 'emails/customer_invoice';
                $emailData = [
                    'email' => $customer['email'],
                    'date' => $date,
                    'yesterday' => $yesterday,
                    'customer' => $customer,
                    'invoices' => $invoiceData
                ];
                $externalContacts = $this->customerContact->getBy([
                    'id_customer' => $customer['id'],
                    'type' => 'EXTERNAL'
                ]);
                $internalContacts = $this->customerContact->getBy([
                    'id_customer' => $customer['id'],
                    'type' => 'INTERNAL'
                ]);
                $emailTo = array_column(if_empty($externalContacts, ['email' => get_setting('email_support')]), 'email');
                $contactCC = array_column(if_empty($internalContacts, []), 'email');
                $emailOptions = [
                    'cc' => $contactCC
                ];
                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
            }
        }
    }

    /**
     * Send email invoice summary to internal.
     */
    public function outstanding_control_report()
    {
        $this->load->model('ReportModel', 'report');
        $this->load->model('modules/Mailer', 'mailer');

        $date = format_date('now', 'd F Y');

        $reports = $this->report->getInvoiceControl();

        $emailTo = 'direktur@transcon-indonesia.com';
        $emailTitle = "Daily report invoice outstanding on {$date}";
        $emailTemplate = 'emails/invoice_control';
        $emailData = [
            'email' => $emailTo,
            'date' => $date,
            'reports' => $reports
        ];
        $emailOptions = [
            'cc' => ['fin2@transcon-indonesia.com', 'acc_mgr@transcon-indonesia.com', 'md@transcon-indonesia.com']
        ];
        $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
    }

    /**
     * Fetch data from api to merge cash bond 30 days back, to make sure we are not leave behind.
     */
    public function auto_import_cash_bond()
    {
        $this->load->model('EmployeeModel', 'employee');
        $this->load->model('CashBondModel', 'cashBond');
        $this->load->model('CashBondDetailModel', 'cashBondDetail');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('modules/WarehouseAPI', 'apiClient');

        $fromDate = date('Y-m-d', strtotime('-3 days'));

        try {
            $payments = $this->apiClient->get('cash-bond/payment', [
                'is_realized' => 1,
                'from_date' => format_date($fromDate),
            ]);
            $cashBonds = $this->cashBond->getBy([
                'cash_bond_type' => CashBondModel::TYPE_OTHER,
                'no_cash_bond' => array_column(if_empty($payments, []), 'no_payment')
            ]);

            foreach ($payments as &$payment) {
                $payment = (array)$payment;
                $isEdit = false;
                foreach ($cashBonds as $index => $cashBond) {
                    if ($payment['no_payment'] == $cashBond['no_cash_bond']) {
                        $isEdit = true;
                        $employeePayment = $this->employee->getBy(['ref_employees.id_user' => $payment['created_by']], true);
                        $this->cashBond->update([
                            'id_employee' => if_empty((!empty($employeePayment) ? $employeePayment['id'] : ''), null),
                            'id_bank_account' => get_if_exist($payment, 'id_bank_account', null),
                            'no_cash_bond' => $payment['no_payment'],
                            'cash_bond_type' => CashBondModel::TYPE_OTHER,
                            'cash_bond_date' => $payment['created_at'],
                            'settlement_date' => $payment['realized_at'],
                            'requisite_description' => if_empty($payment['description'], 'Payment ' . $payment['no_reference']),
                            'amount_request' => $payment['amount_request'],
                            'description' => $payment['customer_name'] . ' - ' . $payment['no_reference'],
                            'created_by' => $payment['created_by'],
                        ], $cashBond['id']);

                        $this->cashBondDetail->delete(['id_cash_bond' => $cashBond['id']]);
                        $this->cashBondDetail->create([
                            'id_cash_bond' => $cashBond['id'],
                            'invoice_date' => if_empty(format_date($payment['payment_date']), null),
                            'payment_date' => if_empty(format_date($payment['payment_date']), null),
                            'no_invoice' => $payment['no_invoice'],
                            'amount' => $payment['amount'],
                            'description' => $payment['no_reference'],
                            'created_by' => $payment['created_by'],
                        ]);

                        $cashBond = $this->cashBond->getById($cashBond['id']);
                        $this->statusHistory->create([
                            'type' => StatusHistoryModel::TYPE_CASH_BOND,
                            'id_reference' => $cashBond['id'],
                            'status' => CashBondModel::STATUS_VALIDATED,
                            'description' => 'Auto merge data ' . $cashBond['no_cash_bond'],
                            'data' => json_encode([
                                'cash_bond' => $cashBond,
                                'cash_bond_detail' => $this->cashBondDetail->getBy(['id_cash_bond' => $cashBond['id']]),
                                'payment' => $payment,
                                'creator' => UserModel::loginData('name')
                            ])
                        ]);

                        unset($cashBonds[$index]);
                        break;
                    }
                }

                if (!$isEdit) {
                    $employeePayment = $this->employee->getBy(['ref_employees.id_user' => $payment['created_by']], true);
                    $this->cashBond->create([
                        'id_employee' => if_empty((!empty($employeePayment) ? $employeePayment['id'] : ''), null),
                        'id_bank_account' => get_if_exist($payment, 'id_bank_account', null),
                        'no_cash_bond' => $payment['no_payment'],
                        'cash_bond_type' => CashBondModel::TYPE_OTHER,
                        'cash_bond_date' => $payment['created_at'],
                        'settlement_date' => $payment['realized_at'],
                        'source' => CashBondModel::SOURCE_IMPORT,
                        'requisite_description' => if_empty($payment['description'], 'Payment ' . $payment['no_reference']),
                        'amount_request' => $payment['amount_request'],
                        'description' => $payment['customer_name'] . ' - ' . $payment['no_reference'],
                        'created_by' => $payment['created_by'],
                        'status' => CashBondModel::STATUS_VALIDATED
                    ]);
                    $cashBondId = $this->db->insert_id();

                    $this->cashBondDetail->create([
                        'id_cash_bond' => $cashBondId,
                        'invoice_date' => if_empty(format_date($payment['payment_date']), null),
                        'payment_date' => if_empty(format_date($payment['payment_date']), null),
                        'no_invoice' => $payment['no_invoice'],
                        'amount' => $payment['amount'],
                        'description' => $payment['no_reference'],
                        'created_by' => $payment['created_by'],
                    ]);

                    $cashBond = $this->cashBond->getById($cashBondId);
                    $this->statusHistory->create([
                        'type' => StatusHistoryModel::TYPE_CASH_BOND,
                        'id_reference' => $cashBondId,
                        'status' => CashBondModel::STATUS_VALIDATED,
                        'description' => 'Auto import data ' . $payment['no_payment'],
                        'data' => json_encode([
                            'cash_bond' => $cashBond,
                            'payment' => $payment,
                            'creator' => UserModel::loginData('name')
                        ])
                    ]);
                }
            }
        } catch (GuzzleException $e) {
            log_message('error', $e->getMessage());
        }
    }

    /**
     * Send reminder payment plan - 2 days
     */
    public function reminder_payment_plan()
    {
        $this->load->model('ReportModel', 'report');
        $this->load->model('AccountPayablePaymentModel', 'accountPayablePayment');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Exporter', 'exporter');

        $filters = [
            'payment_statuses' => AccountPayablePaymentModel::STATUS_PENDING,
            'payment_date_from' => date('Y-m-d', strtotime('+2 day')),
            'payment_date_to' => date('Y-m-d', strtotime('+2 days'))
        ];
        $payments = $this->report->getPaymentControl($filters);
        $dataExport = $payments;

        if (!empty($payments)) {
            $emailTo = get_setting('email_support');
            $emailTitle = "Payment transfer reminder on " . format_date($filters['payment_date_from'], 'd F Y') . ' (batch transactions)';
            $emailTemplate = 'emails/payment_request_batch';
            $emailData = [
                'email' => $emailTo,
                'date' => $filters['payment_date_from'],
                'accountPayablePayments' => $payments,
                'isReminder' => true
            ];
            $emailAttachment = $this->exporter->exportFromArray('Control Data', $dataExport, false);
            $emailOptions = [
                'cc' => ['fin2@transcon-indonesia.com', 'acc_mgr@transcon-indonesia.com'],
                'attachment' => $emailAttachment
            ];
            $send = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
            if (!$send) {
                log_message('error', $this->email->print_debugger(['headers']));
            }
        }
    }

    /**
     * Send reminder payment plan - 1 day
     */
    public function reminder_payment_check()
    {
        $this->load->model('ReportModel', 'report');
        $this->load->model('AccountPayablePaymentModel', 'accountPayablePayment');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Exporter', 'exporter');

        $filters = [
            'payment_statuses' => AccountPayablePaymentModel::STATUS_PENDING,
            'payment_date_from' => date('Y-m-d', strtotime('+1 day')),
            'payment_date_to' => date('Y-m-d', strtotime('+1 days'))
        ];
        $payments = $this->report->getPaymentControl($filters);
        $dataExport = $payments;

        if (!empty($payments)) {
            $emailTo = 'direktur@transcon-indonesia.com';

            $this->db->trans_start();

            foreach ($payments as &$payment) {
                if ($payment['payment_check'] == AccountPayablePaymentModel::STATUS_PENDING) {
                    $this->accountPayablePayment->update([
                        'status_check' => AccountPayablePaymentModel::STATUS_ASK_APPROVAL
                    ], $payment['id_account_payable_payment']);
                }

                $this->load->helper('string');
                $token = random_string('alnum', 32);

                $payment['token'] = $token;

                $this->statusHistory->create([
                    'type' => StatusHistoryModel::TYPE_SUBMISSION_PAYMENT,
                    'id_reference' => $payment['id_account_payable_payment'],
                    'status' => AccountPayablePaymentModel::STATUS_ASK_APPROVAL,
                    'description' => 'Auto reminder transfer',
                    'data' => json_encode([
                        'token' => $token,
                        'email' => $emailTo,
                        'creator' => 0
                    ])
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                $emailTitle = "Payment transfer request on " . format_date($filters['payment_date_from'], 'd F Y') . ' (batch transactions)';
                $emailTemplate = 'emails/payment_request_batch';
                $emailData = [
                    'email' => $emailTo,
                    'date' => $filters['payment_date_from'],
                    'accountPayablePayments' => $payments,
                    'isReminder' => false
                ];
                $emailAttachment = $this->exporter->exportFromArray('Control Data', $dataExport, false);
                $emailOptions = [
                    'cc' => ['fin2@transcon-indonesia.com', 'acc_mgr@transcon-indonesia.com', 'md@transcon-indonesia.com'],
                    'attachment' => $emailAttachment
                ];
                $send = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
                if (!$send) {
                    log_message('error', $this->email->print_debugger(['headers']));
                }
            }
        }
    }

	/**
	 * Send report payment outstanding
	 */
    public function payment_outstanding_report()
	{
		$this->load->model('ReportModel', 'report');
		$this->load->model('AccountPayablePaymentModel', 'accountPayablePayment');
		$this->load->model('modules/Mailer', 'mailer');

		$outstandingPayments = $this->report->getOutstandingPayment();
		$holdPayments = $this->accountPayablePayment->getAll([
			'status' => AccountPayablePaymentModel::STATUS_HOLD
		]);

		if (!empty($outstandingPayments)) {
			$emailTo = 'direktur@transcon-indonesia.com';

			$emailTitle = "Outstanding payment transfer plan per " . format_date('now', 'd F Y');
			$emailTemplate = 'emails/outstanding_payment';
			$emailData = [
				'email' => $emailTo,
				'outstandingPayments' => $outstandingPayments,
				'holdPayments' => $holdPayments
			];
			$emailOptions = [
				'cc' => ['fin2@transcon-indonesia.com', 'acc_mgr@transcon-indonesia.com', 'md@transcon-indonesia.com'],
			];
			$send = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
			if (!$send) {
				log_message('error', $this->email->print_debugger(['headers']));
			}
		}
	}

	/**
	 * Reminder realization report
	 */
	public function payment_realization_report()
	{
		$this->load->model('ReportModel', 'report');
		$this->load->model('AccountPayablePaymentModel', 'accountPayablePayment');
		$this->load->model('modules/Mailer', 'mailer');

		$dateFrom = date('d F Y', strtotime('-14 day'));
		$dateTo = format_date('now', 'd F Y');
		$realizedPayments = $this->accountPayablePayment->getAll([
			'status' => AccountPayablePaymentModel::STATUS_PAID_OFF,
			'date_from' => $dateFrom,
			'date_to' => $dateTo,
			'sort_by' => 'ref_bill_categories.bill_category'
		]);

		if (!empty($realizedPayments)) {
			$emailTo = 'direktur@transcon-indonesia.com';

			$emailTitle = "Payment realization transfer since " . $dateFrom . ' until ' . $dateTo;
			$emailTemplate = 'emails/payment_realization';
			$emailData = [
				'email' => $emailTo,
				'realizedPayments' => $realizedPayments,
				'dateFrom' => $dateFrom,
				'dateTo' => $dateTo,
			];
			$emailOptions = [
				'cc' => ['fin2@transcon-indonesia.com', 'acc_mgr@transcon-indonesia.com'],
			];
			$send = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
			if (!$send) {
				log_message('error', $this->email->print_debugger(['headers']));
			}
		}
	}

	/**
	 * Get tax base amount summary.
	 * Expected to be executed at 16th each month and 6th each month
	 */
	public function tax_base_summary()
	{
		$this->load->model('AccountReceivableModel', 'accountReceivable');
		$this->load->model('modules/Exporter', 'exporter');
		$this->load->model('modules/Mailer', 'mailer');

		$currentDate = format_date('now', 'j');
		if ($currentDate > 15) {
			$dateFrom = date('Y-m-01');
			$dateTo = date('Y-m-d');
		} else {
			$dateFrom = date('Y-m-1', strtotime('-1 months'));
			$dateTo = date('Y-m-d', strtotime("last day of previous month"));
		}

		$invoicePLB = $this->accountReceivable->getAll([
			'date_type' => 'invoice_date',
			'date_from' => $dateFrom,
			'date_to' => $dateTo
		]);

		$invoiceTPP = $this->accountReceivable->getWarehouseInvoice([
			'date_type' => 'invoice_date',
			'date_from' => $dateFrom,
			'date_to' => $dateTo
		]);

		$invoices = [];
		$totalPLB = 0;
		$totalTPP = 0;

		foreach ($invoicePLB as $invoice) {
			$invoices[] = [
				'type' => 'PLB',
				'customer_name' => $invoice['customer_name'],
				'date' => $invoice['invoice_date'],
				'no_invoice' => $invoice['no_invoice'],
				'description' => $invoice['invoice_note'],
				'base_amount' => $invoice['base_amount'],
			];
			$totalPLB += $invoice['base_amount'];
		}

		foreach ($invoiceTPP as $invoice) {
			$invoices[] = [
				'type' => 'TPP',
				'customer_name' => $invoice['customer_name'],
				'date' => format_date($invoice['invoice_date']),
				'no_invoice' => $invoice['no_invoice'],
				'description' => $invoice['type'] . ' ' . $invoice['no_reference'],
				'base_amount' => $invoice['dpp'],
			];
			$totalTPP += $invoice['dpp'];
		}

		$attachments = [];
		if (!empty($invoices)) {
			$title = 'Invoice DPP Summary ' . $dateFrom . ' Until ' . $dateTo;
			$exportedData = $this->exporter->exportFromArray($title, $invoices, false);
			$attachments[] = [
				'source' => $exportedData,
				'disposition' => 'attachment',
				'file_name' => url_title($title) . ".xlsx",
			];
		}

		$emailTo = 'md@transcon-indonesia.com';
		$emailTitle = "Invoice DPP summary since " . $dateFrom . ' until ' . $dateTo;
		$emailTemplate = 'emails/tax_base_summary';
		$emailData = [
			'email' => $emailTo,
			'invoices' => $invoices,
			'totalPLB' => $totalPLB,
			'totalTPP' => $totalTPP,
			'dateFrom' => $dateFrom,
			'dateTo' => $dateTo,
		];
		$emailOptions = [
			'cc' => ['acc_mgr@transcon-indonesia.com'],
			'attachment' => $attachments,
		];
		$send = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
		if (!$send) {
			log_message('error', $this->email->print_debugger(['headers']));
		}
	}
}
