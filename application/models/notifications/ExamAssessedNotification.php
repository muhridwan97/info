<?php

/**
 * Class ExamAssessedNotification
 */
class ExamAssessedNotification extends Notify
{
	private $examExercise;
	private $message;
	private $url;

	public function __construct($examExercise = null)
	{
		$this->examExercise = $examExercise;;
		$this->message = "Your exam {$examExercise['title']} scores " . numerical($examExercise['score'], 1);
		$this->url = site_url("training/exam-exercise/view/{$examExercise['id']}");
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
				'description' => 'The exam has been assessed'
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
			'subject' => "Your exam {$this->examExercise['title']} scores " . numerical($this->examExercise['score'], 1),
			'template' => 'emails/basic',
			'data' => [
				'name' => $notifiable['name'],
				'email' => $notifiable['email'],
				'content' => "
					Exam <b>{$this->examExercise['title']}</b> ({$this->examExercise['category']}) 
					for {$this->examExercise['employee_name']} is already assessed by {$this->examExercise['evaluator_name']}
					and got scores " . numerical($this->examExercise['score']) . ".
					<br>
                    For further information contact your Trainer or Evaluator.
				"
			],
		];
	}
}
