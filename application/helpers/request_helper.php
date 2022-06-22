<?php

if (!function_exists('_csrf')) {

    /**
     * Generate csrf input.
     *
     * @return string
     */
    function _csrf()
    {
        $name = get_instance()->security->get_csrf_token_name();
        $token = get_instance()->security->get_csrf_hash();
        return "<input type='hidden' name='{$name}' value='{$token}'>";
    }
}

if (!function_exists('_method')) {

    /**
     * Generate method input.
     *
     * @param $method
     * @return string
     */
    function _method($method)
    {
        return "<input type='hidden' name='_method' value='{$method}'>";
    }
}

if (!function_exists('_is_method')) {

    /**
     * Generate method input.
     *
     * @param $method
     * @param bool $strict
     * @return string
     */
    function _is_method($method, $strict = false)
    {
        $requestMethod = get_instance()->input->server('REQUEST_METHOD');

        $inputMethod = get_instance()->input->post('_method');
        if (!empty($inputMethod)) {
            $requestMethod = strtolower($inputMethod);
        }

        // allow check as post if put, patch, delete
        if (!$strict) {
            if (in_array($requestMethod, ['put', 'patch', 'delete'])) {
                if (strtolower($method) == 'post') {
                    return true;
                }
            }
        }

        return strtolower($requestMethod) == strtolower($method);
    }
}

if (!function_exists('get_url_param')) {
    /**
     * Helper get query string value.
     * @param $key
     * @param string $default
     * @return string
     */
    function get_url_param($key, $default = '')
    {
        if (isset($_GET[$key]) && ($_GET[$key] != '' && $_GET[$key] != null)) {
            if($key == 'redirect') {
                return str_replace('redirect=', '', get_if_exist($_SERVER, 'REDIRECT_QUERY_STRING', ''));
            }
            return is_array($_GET[$key]) ? $_GET[$key] : urldecode($_GET[$key]);
        }
        return $default;
    }
}

if (!function_exists('set_url_param')) {

    /**
     * Update page value in query params.
     * @param array $setParams
     * @param null $query
     * @param bool $returnArray
     * @return string|array
     */
    function set_url_param($setParams = [], $query = null, $returnArray = false)
    {
        $queryString = empty($query) ? $_SERVER['QUERY_STRING'] : $query;
        $params = explode('&', $queryString);

        $builtParam = [];

        // mapping to key->value array
        for ($i = 0; $i < count($params); $i++) {
            $param = explode('=', $params[$i]);
            if (!empty($param[0])) {
                $builtParam[$param[0]] = key_exists(1, $param) ? $param[1] : '';
            }
        }

        // set params
        foreach ($setParams as $key => $value) {
            $builtParam[$key] = $value;
        }

        if ($returnArray) {
            return $builtParam;
        }

        // convert to string
        $baseQuery = '';
        foreach ($builtParam as $key => $value) {
            $baseQuery .= (empty($baseQuery) ? '' : '&') . ($key . '=' . $value);
        }

        return $baseQuery;
    }
}

if (!function_exists('sso_url')) {
    /**
     * Get single sign on url.
     *
     * @param string $uri
     * @return string
     */
    function sso_url($uri = '')
    {
        return rtrim(env('SSO_URL'), '/') . '/' . $uri;
    }
}

if (!function_exists('sso_storage')) {
    /**
     * Get uploaded in storage url.
     *
     * @param string $uri
     * @return string
     */
    function sso_storage($uri = '')
    {
        return rtrim(env('SSO_STORAGE'), '/') . '/' . $uri;
    }
}

if (!function_exists('get_current_url')) {
    /**
     * Helper get current url  string value.
     * @param bool $withQueryString
     * @return string
     */
    function get_current_url($withQueryString = true)
    {
        if ($withQueryString) {
            return site_url(uri_string()) . '?' . $_SERVER['QUERY_STRING'];
        }
        return site_url(uri_string());
    }
}

if (!function_exists('get_client_ip')) {
    /**
     * Get client ip.
     *
     * @return array|false|string
     */
    function get_client_ip()
    {
        if (getenv('HTTP_CLIENT_IP'))
            $ip = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ip = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ip = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ip = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ip = getenv('REMOTE_ADDR');
        else
            $ip = '';

        return $ip;
    }
}

if (!function_exists('asset_url')) {
    /**
     * Get uploaded in storage url.
     * asset_url('home/apple.jpg')  ->  local.com/uploads/home/apple.jpg
     * asset_url('home/apple.jpg', false)  ->  s3.server.com/bucket/uploads/home/apple.jpg
     * asset_url('http://server.com/images/apple.jpg')  ->  http://server.com/images/apple.jpg
     *
     * @param string $key
     * @param bool $getLocalFirst
     * @param mixed|string $bucket
     * @return string
     */
    function asset_url($key = '', $getLocalFirst = true, $bucket = null)
    {
        // check if full url
        $regex = "((https?|ftp)\:\/\/)?"; // SCHEME
        $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
        $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
        $regex .= "(\:[0-9]{2,5})?"; // Port
        $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
        $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
        $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor

        // `i` flag for case-insensitive
        if (preg_match("/^$regex$/i", $key)) {
            return $key;
        }

        // check if in local exist
        if ($getLocalFirst) {
            $file = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $key;
            if (file_exists($file) && is_readable($file)) {
                return base_url('uploads/' . $key);
            }
        }

        // get from s3
        if (empty($bucket)) {
            $bucket = env('S3_BUCKET');
        } else {
			if (!empty(env('S3_CDN'))) {
				return rtrim(env('S3_CDN'), '/') . '/' . $key;
			}
		}
        return rtrim(env('S3_ENDPOINT'), '/') . '/' . $bucket . '/' . $key;
    }
}
