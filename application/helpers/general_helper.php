<?php
defined('BASEPATH') or exit('No direct script access allowed');
header('Content-Type: text/html; charset=utf-8');


if (!function_exists('referrer')) {
	function referrer()
	{
			return empty($_SERVER['HTTP_REFERER']) ? '' : trim($_SERVER['HTTP_REFERER']);
	}
}


if (!function_exists('old')) {
	function old($field)
	{
		$ci =& get_instance();
		if (isset($ci->session->flashdata('form_data')[$field])) {
			return html_escape($ci->session->flashdata('form_data')[$field]);
		}
	}
}


if (!function_exists('auth_check')) {
	function auth_check()
	{
		$ci =& get_instance();
		return $ci->auth_model->is_logged_in();
	}
}


if (!function_exists('clean_number')) {
	function clean_number($num)
	{
		$ci =& get_instance();
		$num = $ci->security->xss_clean($num);
		$num = str_slug($num);
		$num = intval($num);
		$num = mysqli_real_escape_string($ci->db->conn_id, $num);
		return $num;
	}
}


if (!function_exists('str_slug')) {
	function str_slug($str)
	{
		$str = trim($str);
		return url_title(convert_accented_characters($str), "-", true);
	}
}


if (!function_exists('clean_str')) {
    function clean_str($str)
    {
        $ci =& get_instance();
        $str = $ci->security->xss_clean($str);
        $str = remove_special_characters($str, false);
        return $str;
    }
}


//remove special characters
if (!function_exists('remove_special_characters')) {
    function remove_special_characters($str, $is_slug = false)
    {
        $str = trim($str);
        $str = str_replace('#', '', $str);
        $str = str_replace(';', '', $str);
        $str = str_replace('!', '', $str);
        $str = str_replace('"', '', $str);
        $str = str_replace('$', '', $str);
        $str = str_replace('%', '', $str);
        $str = str_replace('(', '', $str);
        $str = str_replace(')', '', $str);
        $str = str_replace('*', '', $str);
        $str = str_replace('+', '', $str);
        $str = str_replace('/', '', $str);
        $str = str_replace('\'', '', $str);
        $str = str_replace('<', '', $str);
        $str = str_replace('>', '', $str);
        $str = str_replace('=', '', $str);
        $str = str_replace('?', '', $str);
        $str = str_replace('[', '', $str);
        $str = str_replace(']', '', $str);
        $str = str_replace('\\', '', $str);
        $str = str_replace('^', '', $str);
        $str = str_replace('`', '', $str);
        $str = str_replace('{', '', $str);
        $str = str_replace('}', '', $str);
        $str = str_replace('|', '', $str);
        $str = str_replace('~', '', $str);
        if ($is_slug == true) {
            $str = str_replace(" ", '-', $str);
            $str = str_replace("'", '', $str);
        }
        return $str;
    }
}


if (!function_exists('user')) {
	function user()
	{
		$ci =& get_instance();
		$user = $ci->auth_model->get_logged_user();
		if (empty($user)) {
			$ci->auth_model->logout();
		} else {
			return $user;
		}
	}
}



if (!function_exists('alphabets_to_empty')) {
	function alphabets_to_empty($text)
	{
		$text		= strtolower($text);
		$find 		= array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		$replace 	= array('','','','','','','','','','','','','','','','','','','','','','','','','','');
		return $text = trim(str_replace($find,$replace,$text));
	}
}

					


if ( ! function_exists('convert_accented_characters'))
{
	/**
	 * Convert Accented Foreign Characters to ASCII
	 *
	 * @param	string	$str	Input string
	 * @return	string
	 */
	function convert_accented_characters($str)
	{
		static $array_from, $array_to;

		if ( ! is_array($array_from))
		{
			if (file_exists(APPPATH.'config/foreign_chars.php'))
			{
				include(APPPATH.'config/foreign_chars.php');
			}

			if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/foreign_chars.php'))
			{
				include(APPPATH.'config/'.ENVIRONMENT.'/foreign_chars.php');
			}

			if (empty($foreign_characters) OR ! is_array($foreign_characters))
			{
				$array_from = array();
				$array_to = array();

				return $str;
			}

			$array_from = array_keys($foreign_characters);
			$array_to = array_values($foreign_characters);
		}

		return preg_replace($array_from, $array_to, $str);
	}
}







if (!function_exists('generate_token')) {
	function generate_token()
	{
		$token = uniqid("", TRUE);
		$token = str_replace(".", "-", $token);
		return $token . "-" . rand(10000000, 99999999);
	}
}



if (!function_exists('get_user')) {
	function get_user($user_id)
	{
		$ci =& get_instance();
		return $ci->auth_model->get_user($user_id);
	}
}




if (!function_exists('get_tables')) {
	function get_tables($table,$order,$order_by)
	{
		$ci =& get_instance();
		return $ci->admin_model->get_tables($table,$order,$order_by);
	}
}


if (!function_exists('get_tables_limit')) {
	function get_tables_limit($table,$order,$order_by,$limit)
	{
		$ci =& get_instance();
		return $ci->admin_model->get_tables_limit($table,$order,$order_by,$limit);
	}
}

if (!function_exists('get_tables_where_array')) {
	function get_tables_where_array($table,$where,$order,$order_by)
	{
		$ci =& get_instance();
		return $ci->admin_model->get_tables_where_array($table,$where,$order,$order_by);
	}
}




if (!function_exists('get_table_where_array')) {
	function get_table_where_array($table,$where,$order,$order_by)
	{
		$ci =& get_instance();
		return $ci->admin_model->get_table_where_array($table,$where,$order,$order_by);
	}
}

if (!function_exists('get_tables_where')) {
	function get_tables_where($table,$where,$where_value,$order,$order_by)
	{
		$ci =& get_instance();
		return $ci->admin_model->get_tables_where($table,$where,$where_value,$order,$order_by);
	}
}



if (!function_exists('get_table_sum')) {
	function get_table_sum($table,$sum,$where)
	{
		$ci =& get_instance();
		return $ci->admin_model->get_table_sum($table,$sum,$where);
	}
}


if (!function_exists('get_table_count')) {
	function get_table_count($table,$where)
	{
		$ci =& get_instance();
		return $ci->admin_model->get_table_count($table,$where);
	}
}


if (!function_exists('get_table')) {
	function get_table($table,$id)
	{
		$ci =& get_instance();
		return $ci->admin_model->get_table($table,$id);
	}
}







if (!function_exists('generate_uuid')) {
	function generate_uuid()
	{
	$randomString = bin2hex(random_bytes(16));
    $uuid = sprintf(
        '%s-%s-%s-%s-%s',
        substr($randomString, 0, 8),
        substr($randomString, 8, 4),
        substr($randomString, 12, 4),
        substr($randomString, 16, 4),
        substr($randomString, 20, 12)
    );

    return $uuid;
		
	}
}


if (!function_exists('encrypt_text')) {
	function encrypt_text($text)
	{

		$ciphering 		= "AES-128-CTR";
		$iv_length 		= openssl_cipher_iv_length($ciphering);
		$options 		= 0;
		$encryption_iv 	= '1234567891011121';
		$encryption_key = "RFG";
		return $encryption 	= openssl_encrypt($text, $ciphering,$encryption_key, $options, $encryption_iv);
  

		
	}
}

if (!function_exists('decrypt_text')) {
	function decrypt_text($uuid)
	{
		$ciphering 		= "AES-128-CTR";
		$iv_length 		= openssl_cipher_iv_length($ciphering);
		$options 		= 0;
		$decryption_iv 	= '1234567891011121';
		$decryption_key = "RFG";
		return $decryption = openssl_decrypt($text, $ciphering, $decryption_key, $options, $decryption_iv);
			
	}
}




if (!function_exists('callAPI')) {
  function callAPI($endpointURL, $apiKey, $postFields = [], $requestType = 'POST') {

      $curl = curl_init($endpointURL);
      curl_setopt_array($curl, array(
          CURLOPT_CUSTOMREQUEST  => $requestType,
          CURLOPT_POSTFIELDS     => json_encode($postFields),
          CURLOPT_HTTPHEADER     => array("Authorization: Bearer $apiKey", 'Content-Type: application/json'),
          CURLOPT_RETURNTRANSFER => true,
      ));

      $response = curl_exec($curl);
      $curlErr  = curl_error($curl);

      curl_close($curl);

      if ($curlErr) {
          //Curl is not working in your server
          die("Curl Error: $curlErr");
      }

      $error = handleError($response);
      if ($error) {
          die("Error: $error");
      }

      return json_decode($response);
  }
}



if (!function_exists('time_ago')) {
	function time_ago($date)
	{
			$strTimeAgo = ""; 
			if(!empty($date)) {
				return $strTimeAgo = timeago($date);
			}
			
	
	}
}

if (!function_exists('timeago')) {
	function timeago($date)
	{
			
			$timestamp = strtotime($date);	

			$strTime = array("second", "minute", "hour", "day", "month", "year");
			$length = array("60","60","24","30","12","10");

			$currentTime = time();
			if($currentTime >= $timestamp) {
			$diff     = time()- $timestamp;
				for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
				$diff = $diff / $length[$i];
			}

				$diff = round($diff);
				return $diff . " " . $strTime[$i] . "(s) ago ";
			}	
	
	}
}

	
	
if (!function_exists('handleError')) {
function handleError($response) {

    $json = json_decode($response);
    if (isset($json->IsSuccess) && $json->IsSuccess == true) {
        return null;
    }

    //Check for the errors
    if (isset($json->ValidationErrors) || isset($json->FieldsErrors)) {
        $errorsObj = isset($json->ValidationErrors) ? $json->ValidationErrors : $json->FieldsErrors;
        $blogDatas = array_column($errorsObj, 'Error', 'Name');

        $error = implode(', ', array_map(function ($k, $v) {
                    return "$k: $v";
                }, array_keys($blogDatas), array_values($blogDatas)));
    } else if (isset($json->Data->ErrorMessage)) {
        $error = $json->Data->ErrorMessage;
    }

    if (empty($error)) {
        $error = (isset($json->Message)) ? $json->Message : (!empty($response) ? $response : 'API key or API URL is not correct');
    }

    return $error;
}

if (!function_exists('uploadImage')) {
  function uploadImage($imagedata, $table, $id ) {
	  if (!file_exists('uploads/'.$table)) {
			mkdir('uploads/'.$table.'/', 0777, true);
		}
		$defaultPath = 'uploads/'.$table.'/';
			switch ($imagedata[0]) {
				case 'data:image/png':
					$output_file = $id . '.png';
					break;
				case 'data:image/jpeg':
					$output_file = $id . '.jpeg';
					break;
				case 'data:image/jpg':
					$output_file = $id . '.jpg';
					break;
				default:
					$output_file = null;
					break;
			}
			if($output_file!=null){
				$ifp = fopen($defaultPath . $output_file, 'wb');
				fwrite($ifp, base64_decode($imagedata[1]));
				fclose($ifp);
			}

      return ($output_file);
  }
}
if (!function_exists('getCreatedBy')) {
  function getCreatedBy() {
	$createdBy='';
    return ($createdBy);
  }
}
if (!function_exists('getPassword')) {
  function getPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 6; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
  }
}
  
}



