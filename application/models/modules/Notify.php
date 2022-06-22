<?php
/**
 * Created by PhpStorm.
 * User: angga
 * Date: 15/06/20
 * Time: 11:42
 */

class Notify
{
    const CHAT_PUSH = 'CHAT_PUSH';
    const WEB_PUSH = 'WEB_PUSH';
    const MAIL_PUSH = 'MAIL_PUSH';
    const DATABASE_PUSH = 'DATABASE_PUSH';
    const ARRAY_PUSH = 'ARRAY_PUSH';

	/**
	 * Mail notification.
	 * @param $notifiable
	 * @return array
	 */
    public function toMail($notifiable)
    {
        return [];
    }

	/**
	 * Chat notification.
	 * @param $notifiable
	 * @return array
	 */
    public function toChat($notifiable)
    {
        return [];
    }

	/**
	 * Web notification.
	 * @param $notifiable
	 * @return array
	 */
    public function toWeb($notifiable)
    {
        return [];
    }

	/**
	 * Array notification.
	 * @param $notifiable
	 * @return array
	 */
    public function toDatabase($notifiable)
    {
        return [];
    }

    /**
     * Array notification.
     */
    public function toArray()
    {
        return [];
    }

}
