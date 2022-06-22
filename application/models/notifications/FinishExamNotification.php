<?php

/**
 * Class FinishExamNotification
 */
class FinishExamNotification extends Notify
{
    private $examExercise;
	private $message;
	private $url;

    public function __construct($examExercise = null)
    {
        $this->examExercise = $examExercise;
		$this->message = "Exam {$examExercise['title']} is finished at " . $examExercise['updated_at'];
		$this->url = site_url("training/exam/view/{$examExercise['id_exam']}");
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
            'event' => NotificationModel::EVENT_EXAM_FINISHED,
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
            'id_related' => $this->examExercise['id'],
            'channel' => NotificationModel::SUBSCRIBE_TRAINING,
            'event' => NotificationModel::EVENT_EXAM_FINISHED,
            'data' => json_encode([
                'message' => $this->message,
                'url' => $this->url,
                'time' => format_date('now', 'Y-m-d H:i:s'),
                'description' => 'Curriculum exam is finished'
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
		return [
			'to' => $notifiable['email'],
			'subject' => "Exam {$this->examExercise['title']} is finished, ready to be assessed",
			'template' => 'emails/basic',
			'data' => [
				'name' => $notifiable['name'],
				'email' => $notifiable['email'],
				'content' => "
					Exam <b>{$this->examExercise['title']}</b> ({$this->examExercise['category']}) 
					by {$this->examExercise['employee_name']}
					is finished and ready to be assessed by you,  
                    please visit the assessment menu and evaluate the result of the exam.
				"
			],
		];
	}
}
