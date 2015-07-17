<?php
use Sky\core\Config;

function _removeInvisibleCharacters($str, $url_encoded = TRUE){
	$non_displayables = array();
	// every control character except newline (dec 10)
	// carriage return (dec 13), and horizontal tab (dec 09)
	
	if ($url_encoded){
		$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
		$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
	}
	
	$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

	do{
		$str = preg_replace($non_displayables, '', $str, -1, $count);
	}
	while ($count);

	return $str;
}
/**
* get return public url
* 
* @param string | array
* @return url
*/
function _publicUrl($uri){
	return HOST_NAME.Config::read('App.Base.public_folder').'/'.ltrim(_uriString($uri),'/');
}
/**
* get return site url
* 
* @param string | array
* @return url
*/
function _siteUrl($uri){
	return HOST_NAME.ltrim(_uriString($uri),'/');
}
/**
* convert uri to string
* 
* @param string | array
* @return url
*/
function _uriString($uri){
	if (is_array($uri)){
		$i = 0;
		$str = '';
		foreach ($uri as $key => $val)
		{
			$prefix = ($i == 0) ? '' : '&';
			$str .= $prefix.$key.'='.$val;
			$i++;
		}
		$uri = $str;
	}
	return $uri;
}
/**
* Set Respond page Header Code
* 
* @param number
* @return void
*/
function _setStatusHeader($code = 200, $text = ''){
	$stati = [
		200	=> 'OK',
		201	=> 'Created',
		202	=> 'Accepted',
		203	=> 'Non-Authoritative Information',
		204	=> 'No Content',
		205	=> 'Reset Content',
		206	=> 'Partial Content',

		300	=> 'Multiple Choices',
		301	=> 'Moved Permanently',
		302	=> 'Found',
		304	=> 'Not Modified',
		305	=> 'Use Proxy',
		307	=> 'Temporary Redirect',

		400	=> 'Bad Request',
		401	=> 'Unauthorized',
		403	=> 'Forbidden',
		404	=> 'Not Found',
		405	=> 'Method Not Allowed',
		406	=> 'Not Acceptable',
		407	=> 'Proxy Authentication Required',
		408	=> 'Request Timeout',
		409	=> 'Conflict',
		410	=> 'Gone',
		411	=> 'Length Required',
		412	=> 'Precondition Failed',
		413	=> 'Request Entity Too Large',
		414	=> 'Request-URI Too Long',
		415	=> 'Unsupported Media Type',
		416	=> 'Requested Range Not Satisfiable',
		417	=> 'Expectation Failed',

		500	=> 'Internal Server Error',
		501	=> 'Not Implemented',
		502	=> 'Bad Gateway',
		503	=> 'Service Unavailable',
		504	=> 'Gateway Timeout',
		505	=> 'HTTP Version Not Supported'
	];

	if ($code == '' OR ! is_numeric($code)){
		
		user_error('Status codes must be numeric', 500);
	}

	if (isset($stati[$code]) AND $text == ''){
		$text = $stati[$code];
	}

	if ($text == ''){
		user_error('No status text available.  Please check your status code number or supply your own message text.', 500);
	}

	$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

	if (substr(php_sapi_name(), 0, 3) == 'cgi'){
		header("Status: {$code} {$text}", TRUE);
	}
	elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0'){
		header($server_protocol." {$code} {$text}", TRUE, $code);
	}
	else{
		header("HTTP/1.1 {$code} {$text}", TRUE, $code);
	}
}

function _camelize($str){
	$str = strtolower(trim($str));
	$str = ucwords(preg_replace('/[\s_]+/', ' ', $str));
	return str_replace(' ', '', $str);
}