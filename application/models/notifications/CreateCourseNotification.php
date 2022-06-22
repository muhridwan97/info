<?php

/**
 * Class CreateCourseNotification
 */
class CreateCourseNotification extends Notify
{
    private $course;
	private $message;
	private $url;

    public function __construct($course = null)
    {
        if($course == null){
            $course['course_title'] = "kosong";
            $course['id'] = 0;
        }
        $this->course = $course;
		$this->message = "Course {$course['course_title']} is created, check it out now";
		$this->url = site_url("syllabus/course/view/{$course['id']}");
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
            'event' => NotificationModel::EVENT_COURSE_MUTATION,
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
            'id_related' => $this->course['id'],
            'channel' => NotificationModel::SUBSCRIBE_SYLLABUS,
            'event' => NotificationModel::EVENT_COURSE_MUTATION,
            'data' => json_encode([
                'message' => $this->message,
                'url' => $this->url,
                'time' => format_date('now', 'Y-m-d H:i:s'),
                'description' => 'New course data'
            ])
        ];
    }
}
