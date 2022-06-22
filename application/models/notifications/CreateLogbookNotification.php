<?php

/**
 * Class CreateLogbookNotification
 * @property UserModel $user
 * @property StudentModel $student
 */
class CreateLogbookNotification extends Notify
{
    private $logbook;
	private $message;
	private $url;

    public function __construct($logbook = null)
    {
    	get_instance()->load->model('StudentModel', 'student');
    	get_instance()->load->model('LecturerModel', 'lecturer');

        if($logbook == null){
            $logbook['judul'] = 'judul not found';
            $logbook['nama_mahasiswa'] = 'nama not found';
        }
        $this->logbook = $logbook;
		$this->message = "Logbook {$logbook['judul']} has been created by {$logbook['nama_mahasiswa']}, wait validate from your lecturer";
		$this->url = site_url("skripsi/logbook/view/{$logbook['id']}");
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
            'channel' => NotificationModel::SUBSCRIBE_SKRIPSI,
            'event' => NotificationModel::EVENT_LOGBOOK_CREATED,
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
            'id_related' => $this->logbook['id'],
            'channel' => NotificationModel::SUBSCRIBE_SKRIPSI,
            'event' => NotificationModel::EVENT_LOGBOOK_CREATED,
            'data' => json_encode([
                'message' => $this->message,
                'url' => $this->url,
                'time' => format_date('now', 'Y-m-d H:i:s'),
                'description' => 'New logbook created'
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
			'subject' => "Logbook {$this->logbook['judul']} has been created by {$this->logbook['nama_mahasiswa']}",
			'template' => 'emails/basic',
			'data' => [
				'name' => $notifiable['name'],
				'email' => $notifiable['email'],
				'content' => "
					Logbook from skripsi <b>{$this->logbook['judul']}</b> has been created by <b>{$this->logbook['nama_mahasiswa']}</b>,
                    <br><br>
                    Rincian: " . if_empty($this->logbook['description'], 'no additional message')
			]
		];
	}
}
