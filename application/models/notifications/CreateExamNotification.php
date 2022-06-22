<?php

/**
 * Class CreateExamNotification
 * @property UserModel $user
 * @property EmployeeModel $employee
 */
class CreateExamNotification extends Notify
{
    private $exam;
	private $message;
	private $url;

    public function __construct($exam = null)
    {
    	get_instance()->load->model('EmployeeModel', 'employee');

        $this->exam = $exam;
		$this->message = "Training {$exam['curriculum_title']} for {$exam['employee_name']} is assigned, do it now";
		$this->url = site_url("training/exam/view/{$exam['id']}");
    }

    /**
     * Web push notification data.
     *
     * @param $notifiable
     * @return array
     */
    public function toWeb($notifiable)
    {
        return $data = [
            'channel' => NotificationModel::SUBSCRIBE_TRAINING,
            'event' => NotificationModel::EVENT_EXAM_ASSIGNED,
            'payload' => [
                'message' => $this->message,
                'url' => $this->url,
                'time' => format_date('now', 'Y-m-d H:i:s'),
            ]
        ];
    }

    /**
     * Database push notification.
     *
     * @param $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'id_user' => $notifiable['id'],
            'id_related' => $this->exam['id'],
            'channel' => NotificationModel::SUBSCRIBE_TRAINING,
            'event' => NotificationModel::EVENT_EXAM_ASSIGNED,
            'data' => json_encode([
                'message' => $this->message,
                'url' => $this->url,
                'time' => format_date('now', 'Y-m-d H:i:s'),
                'description' => 'New exam assigned'
            ])
        ];
    }

	/**
	 * Mail notification.
	 *
	 * @param $notifiable
	 * @return array
	 */
	public function toMail($notifiable)
	{
		$evaluatorEmployee = get_instance()->employee->getById($this->exam['id_evaluator']);
		return [
			'to' => $notifiable['email'],
			'subject' => "Exam {$this->exam['curriculum_title']} is assigned to {$this->exam['employee_name']}",
			'template' => 'emails/basic',
			'data' => [
				'name' => $notifiable['name'],
				'email' => $notifiable['email'],
				'content' => "
					Exam from curriculum <b>{$this->exam['curriculum_title']}</b> is assigned to <b>{$this->exam['employee_name']}</b>,  
                    if essay or practice exam available then will be evaluated by <b>{$this->exam['evaluator_name']}</b>.
                    <br><br>
                    Note: " . if_empty($this->exam['description'], 'no additional message')
			],
			'option' => [
				'cc' => !empty($evaluatorEmployee) ? $evaluatorEmployee['email'] : []
			]
		];
	}
}
