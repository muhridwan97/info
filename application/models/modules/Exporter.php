<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');

class Exporter extends CI_Model
{
    /**
     * Export data from array.
     *
     * @param $title
     * @param $data
     * @param bool $download
     * @param null $storeTo
     * @return null|string
     */
    public function exportFromArray($title, $data, $download = true, $storeTo = null)
    {
        foreach ($data as &$datum) {
            foreach ($datum as $key => $field) {
                if ($key == 'id' || $key == 'is_deleted' || $key == 'deleted_at' || $key == 'password' || $key == 'token' || preg_match('/^id_/', $key) || preg_match('/_by$/', $key)) {
                    unset($datum[$key]);
                }
            }
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator($this->config->item('app_name'))
            ->setLastModifiedBy($this->config->item('app_name'))
            ->setTitle($title)
            ->setSubject('Data export: ' . $title)
            ->setDescription('Data export generated by: ' . $this->config->item('app_name'));

        $excelWriter = new Xlsx($spreadsheet);

        try {
            $spreadsheet->setActiveSheetIndex(0);
            $activeSheet = $spreadsheet->getActiveSheet();

            $activeSheet->setCellValue('A1', $title)
                ->getStyle('A1')
                ->getFont()
                ->setBold(true);

            $header = [];
            if (!empty($data)) {
                $header = array_keys($data[0]);
                $header = array_map(function ($title) {
                    return strtoupper(str_replace(['_', '-'], ' ', $title));
                }, $header);
            }

            $activeSheet->fromArray($header, null, 'A1');
            $activeSheet->fromArray($data, null, 'A2');

            $columnIterator = $spreadsheet->getActiveSheet()->getColumnIterator();
            foreach ($columnIterator as $column) {
                $spreadsheet->getActiveSheet()
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);

                $spreadsheet->getActiveSheet()
                    ->getStyle($column->getColumnIndex() . '1')
                    ->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'color' => ['rgb' => '000000']
                            ],
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => 'FFFFFF']
                            ]
                        ]
                    );
            }

            if (!empty($data)) {
                $activeSheet->setAutoFilterByColumnAndRow(1, 1, count($header), 1);
            }

            if ($download) {
                $this->load->helper('download');
                $storeTo = './uploads/temp/' . $title . '-' . date('YmdHis') . '.xlsx';
                $excelWriter->save($storeTo);
                force_download($storeTo, null, true);
            } else {
                if (empty($storeTo)) {
                    $storeTo = './uploads/temp/' . $title . '-' . date('YmdHis') . '.xlsx';
                }
                $excelWriter->save($storeTo);
                return $storeTo;
            }

        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            return $e->getMessage();
        }

        return false;
    }

    /**
     * Export pdf from html.
     *
     * @param $title
     * @param $html
     * @param array $options
     * @return null|string
     */
    public function exportToPdf($title, $html, $options = [])
    {
        $pdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled' => true, 'isPhpEnabled' => true]);

        if(empty($html)) {
            $options['data']['pdf'] = $pdf;
            $pdf->loadHtml($this->load->view($options['view'], $options['data'], true));
        } else {
            $pdf->loadHtml($html);
        }

        if (key_exists('paper', $options) && !empty($options['paper'])) {
            if (key_exists('orientation', $options) && !empty($options['orientation'])) {
                $pdf->setPaper($options['paper'], $options['orientation']);
            } else {
                $pdf->setPaper($options['paper'], 'portrait');
            }
        } else {
            $pdf->setPaper('A4', 'portrait');
        }

        $buffer = get_if_exist($options, 'buffer', false);

        $pdf->render();

        if ($buffer) {
            return $pdf->output();
        }

        $pdf->stream($title . ".pdf", ["Attachment" => true]);//attachment false untuk preview
    }
}