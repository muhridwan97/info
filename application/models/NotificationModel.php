<?php

use GuzzleHttp\Exception\GuzzleException;
use Pusher\Pusher;
use Pusher\PusherException;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class NotificationModel
 * @property Mailer $mailer
 */
class NotificationModel extends App_Model
{
    protected $table = 'notifications';

    const SUBSCRIBE_SYLLABUS = 'syllabus';
    const SUBSCRIBE_TRAINING = 'training';
    const SUBSCRIBE_SKRIPSI = 'skripsi';
    const SUBSCRIBE_BLOG = 'blog';

    const EVENT_CURRICULUM_MUTATION = 'curriculum-mutation';
    const EVENT_COURSE_MUTATION = 'course-mutation';
    const EVENT_LESSON_MUTATION = 'lesson-mutation';
    const EVENT_TRAINING_ASSIGNED = 'training-assigned';
    const EVENT_EXAM_ASSIGNED = 'exam-assigned';
    const EVENT_EXAM_FINISHED = 'exam-finished';
    const EVENT_LOGBOOK_CREATED = 'logbook-created';
	const EVENT_BLOG_VALIDATED = 'blog-validated';
	const EVENT_BLOG_REJECTED = 'blog-rejected';
	const EVENT_BLOG_CREATED = 'blog-created';

	private $type = [Notify::WEB_PUSH];
	private $users = [];

	/**
	 * Set type of notification.
	 *
	 * @param $notificationType
	 * @return $this
	 */
	public function via($notificationType)
	{
		if (!key_exists(0, $notificationType)) $notificationType = [$notificationType];

		$this->type = $notificationType;

		if (in_array(Notify::MAIL_PUSH, $this->type)) {
			$this->load->model('modules/Mailer', 'mailer');
		}

		return $this;
	}
	/**
	 * Send notification to users.
	 *
	 * @param Notify $notification
	 * @param $users
	 */
	public function send(Notify $notification, $users = null)
	{
		if (!empty($users)) {
			$this->users = $users;
		}

		if (!empty($this->users)) {
			if (!key_exists(0, $this->users)) $this->users = [$this->users];

			foreach ($this->users as $user) {
				if (in_array(Notify::DATABASE_PUSH, $this->type)) {
					$data = $notification->toDatabase($user);
					$this->create($data);
				}
				if (in_array(Notify::WEB_PUSH, $this->type)) {
					$data = $notification->toWeb($user);
					try {
						$pusher = new Pusher(
							env('PUSHER_APP_KEY'),
							env('PUSHER_APP_SECRET'),
							env('PUSHER_APP_ID'),
							['cluster' => env('PUSHER_APP_CLUSTER'), 'encrypted' => false]
						);
						$pusher->trigger($data['channel'] . '-' . $user['id'], $data['event'], $data['payload']);
					} catch (PusherException $e) {
						$e->getMessage();
					}
				}
				if (in_array(Notify::MAIL_PUSH, $this->type)) {
					$data = $notification->toMail($user);
					$emailTo = $data['to'];
					$subject = $data['subject'];
					$template = $data['template'];
					$data = $data['data'];
					$option = get_if_exist($data, 'option', []);
					$this->mailer->send($emailTo, $subject, $template, $data, $option);
				}
				if (in_array(Notify::CHAT_PUSH, $this->type)) {
					$data = $notification->toChat($user);
					$payload = $data['payload'];
					$baseUri = get_if_exist($data, 'base_uri', env('CHAT_API_URL'));
					$url = get_if_exist($data, 'url', '/');
					$method = get_if_exist($data, 'method', 'GET');
					if (!key_exists('token', $payload)) {
						$payload['token'] = env('CHAT_API_TOKEN');
					}

					try {
						$client = new GuzzleHttp\Client([
							'base_uri' => $baseUri,
							'verify' => boolval(env('CHAT_API_SECURE'))
						]);
						$response = $client->request($method, $url, [
							'query' => ['token' => $payload['token']],
							'form_params' => $payload
						]);
						$result = json_decode($response->getBody(), true);
						$resultResponse = get_if_exist($result, 'sent', 1);
						if (empty($resultResponse) || $resultResponse == '0' || $resultResponse == false) {
							log_message('error', Notify::CHAT_PUSH . ': ' . json_encode($result));
						}
					} catch (GuzzleException $e) {
						log_message('error', Notify::CHAT_PUSH . ': http request error - ' . $e->getMessage());
					}
				}
				if (in_array(Notify::ARRAY_PUSH, $this->type)) {
					$notification->toArray();
				}
			}
		}
	}

	/**
	 * Set user of notification.
	 *
	 * @param $users
	 * @return $this
	 */
	public function to($users)
	{
		if (!empty($users)) {
			if (!key_exists(0, $users)) $users = [$users];

			$this->users = $users;
		}

		return $this;
	}

	/**
	 * Broadcast notification to users.
	 *
	 * @param $data
	 * @return string
	 */
	public function broadcast($data)
	{
		$this->create([
			'id_user' => if_empty($data['id_user'], 1),
			'id_related' => $data['id_related'],
			'channel' => $data['channel'],
			'event' => $data['event'],
			'data' => json_encode($data['payload'])
		]);

		try {
			$pusher = new Pusher(
				env('PUSHER_APP_KEY'),
				env('PUSHER_APP_SECRET'),
				env('PUSHER_APP_ID'),
				['cluster' => env('PUSHER_APP_CLUSTER'), 'encrypted' => false]
			);
			$pusher->trigger($data['channel'] . '-' . $data['id_user'], $data['event'], $data['payload']);
		} catch (PusherException $e) {
			return $e->getMessage();
		}
		return true;
	}

	/**
	 * Get parsed data notifications by user.
	 *
	 * @param $userId
	 * @return array
	 */
	public function getByUser($userId)
	{
		$this->db->from($this->table)
			->where('id_user', $userId)
			->limit(100)
			->order_by('created_at', 'desc');

		$notifications = $this->db->get()->result_array();

		foreach ($notifications as &$notification) {
			$notification['data'] = (array)json_decode($notification['data']);
		}

		return $notifications;
	}

	/**
	 * Get sticky navbar notification.
	 *
	 * @param null $userId
	 * @return array
	 */
	public static function getUnreadNotification($userId = null)
	{
		if ($userId == null) {
			$userId = UserModel::loginData('id');
		}

		$CI = get_instance();
		$CI->db->from('notifications')
			->where([
				'id_user' => $userId,
				'is_read' => false,
				'created_at>=DATE(NOW()) - INTERVAL 7 DAY' => null
			])
			->order_by('created_at', 'desc');

		$notifications = $CI->db->get()->result_array();

		foreach ($notifications as &$notification) {
			$notification['data'] = (array)json_decode($notification['data']);
		}

		return $notifications;
	}

	/**
	 * Parse notification content.
	 *
	 * @param $payload
	 * @param string $url
	 * @return mixed
	 */
	public static function parseNotificationMessage($payload, $url = '')
	{
		$message = $payload->message;
		if (property_exists($payload, 'link_text')) {
			$links = $payload->link_text;
			foreach ($links as $link) {
				$message = str_replace($link->text, "<a class='font-weight-medium' href='{$link->url}'>{$link->text}</a>", $message);
			}
		} else if (!empty($url)) {
			$message = "<a href='{$url}'>{$message}</a>";
		}

		return $message;
	}
}
