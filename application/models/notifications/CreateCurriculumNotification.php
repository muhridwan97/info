<?php

/**
 * Class CreateCurriculumNotification
 */
class CreateCurriculumNotification extends Notify
{
    private $curriculum;
	private $message;
	private $url;

    public function __construct($curriculum = null)
    {
        $this->curriculum = $curriculum;
		$this->message = "Curriculum {$curriculum['curriculum_title']} is created, check it out now";
		$this->url = site_url("syllabus/curriculum/view/{$curriculum['id']}");
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
            'channel' => NotificationModel::SUBSCRIBE_SYLLABUS,
            'event' => NotificationModel::EVENT_CURRICULUM_MUTATION,
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
            'id_related' => $this->curriculum['id'],
            'channel' => NotificationModel::SUBSCRIBE_SYLLABUS,
            'event' => NotificationModel::EVENT_CURRICULUM_MUTATION,
            'data' => json_encode([
                'message' => $this->message,
                'url' => $this->url,
                'time' => format_date('now', 'Y-m-d H:i:s'),
                'description' => 'New curriculum data'
            ])
        ];
    }
}
