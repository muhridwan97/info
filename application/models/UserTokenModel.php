<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserTokenModel extends App_Model
{
    protected $table = 'prv_user_tokens';
    protected $id = 'token';

    const TOKEN_REMEMBER = 'REMEMBER';
    const TOKEN_PASSWORD = 'PASSWORD';
    const TOKEN_REGISTRATION = 'REGISTRATION';

    /**
     * Generate token for one authenticate of credential of several
     * actions such as registration or reset password.
     *
     * @param $email
     * @param string $tokenType
     * @param int $length
     * @param int $maxActivation
     * @param null $expired_at
     * @return bool|string
     */
    public function create($email, $tokenType = 'REGISTRATION', $length = 32, $maxActivation = 1, $expired_at = null)
    {
        $this->load->helper('string');
        $token = random_string('alnum', $length);

        $isTokenEmailExist = $this->db->get_where($this->table, [
            'email' => $email,
            'type' => $tokenType
        ])->num_rows();

        if ($isTokenEmailExist) {
            $result = $this->db->update($this->table, [
                'token' => $token,
                'max_activation' => $maxActivation,
                'expired_at' => $expired_at,
            ], [
                'email' => $email,
                'type' => $tokenType
            ]);
        } else {
            $result = $this->db->insert($this->table, [
                'email' => $email,
                'token' => $token,
                'type' => $tokenType,
                'max_activation' => $maxActivation,
                'expired_at' => $expired_at,
            ]);
        }

        if ($result) {
            return $token;
        }
        return false;
    }

    /**
     * Check if given token is valid.
     *
     * @param string $token
     * @param string $tokenType
     * @return bool|string
     */
    public function verifyToken($token, $tokenType)
    {
        $token = $this->db->get_where($this->table, [
            'token' => $token,
            'type' => $tokenType
        ]);

        if ($token->num_rows()) {
            $tokenData = $token->row_array();
            return $tokenData['email'];
        }

        return false;
    }

    /**
     * Get token data by it's token value.
     *
     * @param $token
     * @param bool $checkActivation
     * @param bool $checkExpiredDate
     * @return array
     */
    public function getByTokenKey($token, $checkActivation = false, $checkExpiredDate = false)
    {
        $userToken = $this->db->from($this->table)->where('token', $token);

        if ($checkActivation) {
            $userToken->where('max_activation >', 0);
        }

        if ($checkExpiredDate) {
            $userToken->where('expired_at >= DATE(NOW())');
        }

        return $userToken->get()->row_array();
    }

    /**
     * Activate token use, decrease token max activation value.
     *
     * @param $token
     * @return mixed
     */
    public function activateToken($token)
    {
        $tokenData = $this->getByTokenKey($token, true);

        if(empty($tokenData)) {
            return false;
        }

        $result = $this->db->update($this->table, [
            'max_activation' => $tokenData['max_activation'] - 1,
        ], ['token' => $token]);

        return $result;
    }

}