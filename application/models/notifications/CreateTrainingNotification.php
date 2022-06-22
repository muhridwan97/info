<?php

/**
 * Class CreateTrainingNotification
 */
class CreateTrainingNotification extends Notify
{
    private $training;
	private $message;
	private $url;

    public function __construct($training = null)
    {
        $this->training = $training;
		$this->message = "Training {$training['curriculum_title']} for {$training['employee_name']} is assigned, learn now";
		$this->url = site_url("training/class/view/{$training['id']}");
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
            'event' => NotificationModel::EVENT_TRAINING_ASSIGNED,
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
            'id_related' => $this->training['id'],
            'channel' => NotificationModel::SUBSCRIBE_TRAINING,
            'event' => NotificationModel::EVENT_TRAINING_ASSIGNED,
            'data' => json_encode([
                'message' => $this->message,
                'url' => $this->url,
                'time' => format_date('now', 'Y-m-d H:i:s'),
                'description' => 'New training class assigned'
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
			'subject' => "{$this->training['curriculum_title']} curriculum training is available for you",
			'template' => 'emails/basic',
			'data' => [
				'name' => $notifiable['name'],
				'email' => $notifiable['email'],
				'content' => "
					Ready to upgrade new skills? 
					Your are assigned to learn <b>{$this->training['curriculum_title']}</b>,  
                    read message detail below or contact your Trainer for further information.
                    <br><br>
                    Note: " . if_empty($this->training['description'], 'no additional message')
			],
		];
	}
}
