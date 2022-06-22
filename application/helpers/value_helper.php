<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('numerical')) {
    /**
     * Helper get decimal value if needed.
     * @param $number
     * @param int $precision
     * @param bool $trimmed
     * @param string $dec_point
     * @param string $thousands_sep
     * @return int|string
     */
    function numerical($number, $precision = 3, $trimmed = true, $dec_point = ',', $thousands_sep = '.')
    {
        if (empty($number)) {
            return 0;
        }
        $formatted = number_format($number, $precision, $dec_point, $thousands_sep);

        if (!$trimmed) {
            return $formatted;
        }

        // Trim unnecessary zero after comma: (2,000 -> 2) or (3,200 -> 3,2)
        return strpos($formatted, $dec_point) !== false ? rtrim(rtrim($formatted, '0'), $dec_point) : $formatted;;

        /* Trim only zero after comma: (2,000 -> 2) but (3,200 -> 3,200)
        $decimalString = '';
        for ($i = 0; $i < $precision; $i++) {
            $decimalString .= '0';
        }
        $trimmedNumber = str_replace($dec_point . $decimalString, "", (string)$formatted);
        return $trimmedNumber;
        */
    }
}

if (!function_exists('if_empty')) {
    /**
     * Helper get decimal value if needed.
     * @param $value
     * @param string $default
     * @param string $prefix
     * @param string $suffix
     * @param bool $strict
     * @return array|string
     */
    function if_empty($value, $default = '', $prefix = '', $suffix = '', $strict = false)
    {
        if (is_null($value) || empty($value)) {
            return $default;
        }

        if ($strict) {
            if ($value == '0' || $value == '-' || $value == '0000-00-00' || $value == '0000-00-00 00:00:00') {
                return $default;
            }
        }

        if (is_array($value)) {
            return $value;
        }

        return is_null($value) ? $value : $prefix . $value . $suffix;
    }
}


if (!function_exists('get_if_exist')) {
    /**
     * Helper get decimal value if needed.
     * @param $array
     * @param string $key
     * @param string $default
     * @return array|string
     */
    function get_if_exist($array, $key = '', $default = '')
    {
        if (is_array($array) && key_exists($key, if_empty($array, []))) {
            if (!empty($array[$key])) {
                return $array[$key];
            }
        }

        return $default;
    }
}

if (!function_exists('format_date')) {
    /**
     * Helper get date with formatted value.
     * @param $value
     * @param string $format
     * @return string
     */
    function format_date($value, $format = 'Y-m-d')
    {
        if (empty($value) || $value == '0000-00-00' || $value == '0000-00-00 00:00:00') {
            return '';
        }
        $dateParts = explode('/', $value);
        if (count($dateParts) == 3) {
            $value = $dateParts['1'] . '/' . $dateParts['0'] . '/' . $dateParts['2'];
        }
        try {
            return (new DateTime($value))->format($format);
        } catch (Exception $e) {
            return '';
        }
    }
}

if (!function_exists('relative_time')) {

    /**
     * Convert string to relative time format.
     *
     * @param $ts
     * @return false|string
     */
    function relative_time($ts)
    {
        if (!ctype_digit($ts)) {
            $ts = strtotime($ts);
        }
        $diff = time() - $ts;
        if ($diff == 0) {
            return 'now';
        } elseif ($diff > 0) {
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 60) return 'just now';
                if ($diff < 120) return '1 minute ago';
                if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
                if ($diff < 7200) return '1 hour ago';
                if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
            }
            if ($day_diff == 1) {
                return 'Yesterday';
            }
            if ($day_diff < 7) {
                return $day_diff . ' days ago';
            }
            if ($day_diff < 31) {
                return ceil($day_diff / 7) . ' weeks ago';
            }
            if ($day_diff < 60) {
                return 'last month';
            }
            return date('F Y', $ts);
        } else {
            $diff = abs($diff);
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 120) {
                    return 'in a minute';
                }
                if ($diff < 3600) {
                    return 'in ' . floor($diff / 60) . ' minutes';
                }
                if ($diff < 7200) {
                    return 'in an hour';
                }
                if ($diff < 86400) {
                    return 'in ' . floor($diff / 3600) . ' hours';
                }
            }
            if ($day_diff == 1) {
                return 'Tomorrow';
            }
            if ($day_diff < 4) {
                return date('l', $ts);
            }
            if ($day_diff < 7 + (7 - date('w'))) {
                return 'next week';
            }
            if (ceil($day_diff / 7) < 4) {
                return 'in ' . ceil($day_diff / 7) . ' weeks';
            }
            if (date('n', $ts) == date('n') + 1) {
                return 'next month';
            }
            return date('F Y', $ts);
        }
    }
}

if (!function_exists('difference_date')) {
    /**
     * Helper get difference by two dates.
     * @param $firstDate
     * @param $secondDate
     * @param string $format
     * @return string
     */
    function difference_date($firstDate, $secondDate, $format = '%R%a')
    {
        $date1 = date_create($firstDate);
        $date2 = date_create($secondDate);
        $diff = date_diff($date1, $date2);
        $diffInFormat = $diff->format($format);

        return intval($diffInFormat);
    }
}

if (!function_exists('print_debug')) {
    /**
     * Print pre formatted data.
     * @param $data
     * @param bool $die_immediately
     */
    function print_debug($data, $die_immediately = true)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        if ($die_immediately) {
            die();
        }
    }
}

if (!function_exists('extract_number')) {
    /**
     * Extract number from value.
     * @param $value
     * @param string $default
     * @return null|string|string[]
     */
    function extract_number($value, $default = '')
    {
        $value = preg_replace("/[^0-9-,\/]/", "", $value);
        $value = preg_replace("/,/", ".", $value);
        return $value == '' ? $default : $value;
    }
}

if (!function_exists('month_list')) {

    /**
     * Get list of month names.
     *
     * @return array
     */
    function month_list()
    {
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July ',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        return $months;
    }
}

if (!function_exists('date_sub_period')) {
    /**
     * Helper get sub date by two dates.
     * @param $date
     * @param $period
     * @param string $format
     * @return string
     * @throws Exception
     */
    function date_sub_period($date, $period, $format = 'd F Y')
    {
        $dateObj = date_create($date);
        $dateObj->sub(new DateInterval($period));
        $formattedDate = $dateObj->format($format);

        return $formattedDate;
    }
}

if (!function_exists('date_add_period')) {
    /**
     * Helper add date by two dates.
     * @param $date
     * @param $period
     * @param string $format
     * @return string
     * @throws Exception
     */
    function date_add_period($date, $period, $format = 'd F Y')
    {
        $dateObj = date_create($date);
        $dateObj->add(new DateInterval($period));
        $formattedDate = $dateObj->format($format);

        return $formattedDate;
    }
}

if (!function_exists('power_set')) {
	function power_set($str_array)
	{
		$set_size = count($str_array);
		$pow_set_size = pow(2, $set_size);
		$return = array();
		for ($counter = 0; $counter < $pow_set_size; $counter++) {
			$tmp_str = [];
			for ($j = 0; $j < $set_size; $j++) {
				if ($counter & (1 << $j)) {
					$tmp_str[] = $str_array[$j];
				}
			}
			if (!empty($tmp_str)) {
				$return[] = $tmp_str;
			}
		}
		return $return;
	}
}

if (!function_exists('get_file_icon')) {

	/**
	 * Get file extension icon.
	 *
	 * @param $fileName
	 * @param null $fileMimeType
	 * @param string $type
	 * @return string
	 */
	function get_file_icon($fileName, $fileMimeType = null, $type = 'icon')
	{
		$ext = pathinfo($fileName, PATHINFO_EXTENSION);

		if (empty($ext) && !empty($fileMimeType)) {
			$mimes = get_mimes();
			foreach ($mimes as $mimeExt => $mime) {
				if (is_array($mime)) {
					if (in_array($fileMimeType, $mime)) {
						$ext = $mimeExt;
						break;
					}
				} else {
					if ($mime == $fileMimeType) {
						$ext = $mime;
						break;
					}
				}
			}
		}

		switch ($ext) {
			case 'pdf':
				$icon = 'mdi mdi-file-pdf-outline';
				$color = '#FF3500';
				break;
			case 'doc':
			case 'docx':
			case 'word':
			case 'odt':
				$icon = 'mdi mdi-file-word-outline';
				$color = '#01A6F0';
				break;
			case 'ppt':
			case 'pptx':
			case 'otp':
			case 'odp':
				$icon = 'mdi mdi-file-powerpoint-outline';
				$color = '#F34F1C';
				break;
			case 'xl':
			case 'xls':
			case 'xlsx':
			case 'ods':
			case 'ots':
				$icon = 'mdi mdi-file-excel-outline';
				$color = '#679900';
				break;
			case 'bmp':
			case 'gif':
			case 'jpg':
			case 'jpeg':
			case 'jpe':
			case 'png':
			case 'tiff':
			case 'tif':
			case 'ico':
				$icon = 'mdi mdi-file-image-outline';
				$color = '#BE2210';
				break;
			case 'text':
			case 'txt':
			case 'log':
				$icon = 'mdi mdi-file-document-outline';
				$color = '#AF998E';
				break;
			case 'mp3':
			case 'mp2':
			case 'mid':
			case 'midi':
			case 'wav':
			case 'flac':
			case 'ogg':
			case 'm4a':
				$icon = 'mdi mdi-file-music-outline';
				$color = '#165BBE';
				break;
			case 'mp4':
			case '3gp':
			case 'flv':
			case 'webm':
			case 'mkv':
			case 'wmv':
			case 'ogv':
				$icon = 'mdi mdi-file-video-outline';
				$color = '#165BBE';
				break;
			case 'rar':
			case 'zip':
			case '7z':
			case 'gtar':
			case 'gz':
			case 'gzip':
				$icon = 'mdi mdi-zip-box-outline';
				$color = '#531ABE';
				break;
			case 'scss':
			case 'sass':
			case 'java':
			case 'class':
			case 'html':
			case 'css':
			case 'js':
			case 'php':
			case 'exe':
			case 'bin':
				$icon = 'mdi mdi-file-code-outline';
				$color = '#1F0946';
				break;
			default:
				$icon = 'mdi mdi mdi-file-outline';
				$color = '#282828';
				break;
		}

		return $type == 'icon' ? $icon : $color;
	}
}

if (!function_exists('get_file_type')) {

	/**
	 * Get file type.
	 *
	 * @param $fileName
	 * @param null $fileMimeType
	 * @return string
	 */
	function get_file_type($fileName, $fileMimeType = null)
	{
		$ext = pathinfo($fileName, PATHINFO_EXTENSION);

		if (empty($ext) && !empty($fileMimeType)) {
			$mimes = get_mimes();
			foreach ($mimes as $mimeExt => $mime) {
				if (is_array($mime)) {
					if (in_array($fileMimeType, $mime)) {
						$ext = $mimeExt;
						break;
					}
				} else {
					if ($mime == $fileMimeType) {
						$ext = $mime;
						break;
					}
				}
			}
		}

		switch ($ext) {
			case 'pdf':
				$type = 'pdf';
				break;
			case 'doc':
			case 'docx':
			case 'word':
			case 'odt':
				$type = 'document';
				break;
			case 'ppt':
			case 'pptx':
			case 'otp':
			case 'odp':
				$type = 'presentation';
				break;
			case 'xl':
			case 'xls':
			case 'xlsx':
			case 'ods':
			case 'ots':
				$type = 'excel';
				break;
			case 'bmp':
			case 'gif':
			case 'jpg':
			case 'jpeg':
			case 'jpe':
			case 'png':
			case 'tiff':
			case 'tif':
			case 'ico':
				$type = 'image';
				break;
			case 'text':
			case 'txt':
			case 'log':
			case 'scss':
			case 'sass':
			case 'java':
			case 'html':
			case 'css':
			case 'js':
			case 'php':
				$type = 'text';
				break;
			case 'rar':
			case 'zip':
			case '7z':
			case 'gtar':
			case 'gz':
			case 'gzip':
			case 'bin':
			case 'exe':
			case 'apk':
				$type = 'archive';
				break;
			case 'mp4':
			case 'webm':
			case 'ogv':
				$type = 'video';
				break;
			default:
				$type = '';
				break;
		}

		return $type;
	}
}

if (!function_exists('base64_to_jpeg')) {
    function base64_to_jpeg($base64_string, $output_file, $extracted = true)
    {
        // open the output file for writing
        $ifp = fopen($output_file, 'wb');

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode(',', $base64_string);

        if($extracted) {
            $data[1] = $base64_string;
        }

        // we could add validation here with ensuring count( $data ) > 1
        fwrite($ifp, base64_decode($data[1]));

        // clean up the file resource
        fclose($ifp);

        return $output_file;
    }
}

if (!function_exists('slugify')) {
    function slugify($text, string $divider = '-')
    {
      // replace non letter or digits by divider
      $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
    
      // transliterate
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    
      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);
    
      // trim
      $text = trim($text, $divider);
    
      // remove duplicate divider
      $text = preg_replace('~-+~', $divider, $text);
    
      // lowercase
      $text = strtolower($text);
    
      if (empty($text)) {
        return 'n-a';
      }
    
      return $text;
    }
}