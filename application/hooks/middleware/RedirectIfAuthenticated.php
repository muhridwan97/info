<?php
/**
 * Created by PhpStorm.
 * User: angga
 * Date: 24/06/18
 * Time: 1:39
 */

class RedirectIfAuthenticated
{
    private $homePage = 'dashboard';

    private $mustRedirectIfAuthenticated = [
        Login::class, Password::class
    ];

    /**
     * Check if user is authenticated.
     * @param string $homeUrl
     */
    public function isGuest($homeUrl)
    {
        $controller = get_class(get_instance());

        foreach ($this->mustRedirectIfAuthenticated as $guestController) {
            if($controller == $guestController) {
                if (UserModel::isLoggedIn()) {
                    if(empty($homeUrl)) {
                        redirect($this->homePage);
                    }
                    redirect($homeUrl);
                }
            }
        }
    }
}