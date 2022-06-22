<?php

/**
 * Class DeactivateTrainingNotification
 */
class DeactivateTrainingNotification extends Notify
{
    private $training;
	private $message;
	private $messageDeactivated;
	private $url;

    public function __construct($training = null, $message = null)
    {
        $this->training = $training;
        $this->messageDeactivated = $message;
		$this->message = "Training {$training['curriculum_title']} is deactivated" . if_empty($message, '', ', ');
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
                'description' => 'Training class deactivated'
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
			'subject' => "Access {$this->training['curriculum_title']} training is DEACTIVATED for you",
			'template' => 'emails/basic',
			'data' => [
				'name' => $notifiable['name'],
				'email' => $notifiable['email'],
				'content' => "
					Your access to training curriculum <b>{$this->training['curriculum_title']}</b>,  
                    is deactivated. You can no longer visit the classroom. 
                    <br><br>
                    Note: " . if_empty($this->messageDeactivated, 'no additional message')
			],
		];
	}
}
