<?php

/**
 * Class CreateLessonNotification
 */
class CreateLessonNotification extends Notify
{
    private $lesson;
	private $message;
	private $url;

    public function __construct($lesson = null)
    {
        $this->lesson = $lesson;
		$this->message = "Lesson {$lesson['lesson_title']} is created, check it out now";
		$this->url = site_url("syllabus/lesson/view/{$lesson['id']}");
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
            'event' => NotificationModel::EVENT_LESSON_MUTATION,
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
            'id_related' => $this->lesson['id'],
            'channel' => NotificationModel::SUBSCRIBE_SYLLABUS,
            'event' => NotificationModel::EVENT_LESSON_MUTATION,
            'data' => json_encode([
                'message' => $this->message,
                'url' => $this->url,
                'time' => format_date('now', 'Y-m-d H:i:s'),
                'description' => 'New lesson data'
            ])
        ];
    }
}
