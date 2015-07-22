<?php
 /**
 * Recaptcha
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @package		Recaptcha
 * @author		Hansen Wong
 * @copyright	Copyright (c) 2015, Rockbeat.
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @link		http://rockbeat.web.id
 * @since		Version 1.0
 */
/** [img src="http://www.google.com/recaptcha/intro/assets/google_logo_41.png"][/img]
* reCaptcha google
*
* [div class="alert alert-danger"]make sure config php.ini for activated extension=php_pdo_mysql.dll[/div]
*
* website: https://developers.google.com/recaptcha/
example : [code]
$recaptcha = get_library('recaptcha');

echo $recaptcha->script();
echo $recaptcha->form_input();	
[/code]

example : [code]
if($_POST){
	$key = array(
		'site_key' => 'YOUR_SITE_KEY',
		'secret_key' => 'YOUR_SECRET_KEY',
	);
	if(get_library('recaptcha')->set_key($key)->verify()){
		// match
	}
	else{
		// not match
	}
}
[/code]
**/
namespace Sky\component;


class Recaptcha{

static $key = array(
	'site_key' 		=> '6LdM0c0SAAAAAIgG9uCe-rZHz_GVhNc5x-IvSZdU',
	'secret_key'	=> '6LdM0c0SAAAAACUEGXPxe7RYu_MqPNHuSJJckpeO'
);
	public function set_key($config){
		self::$key = array_replace(self::$key,$config);
		return $this;
	}
	/*
	* Verify reChaptcha
	*
	* @param boolean
	* @return boolean
	**/
	public function verify($remote_ip = false){
		if(isset($_POST['g-recaptcha-response'])){
			$ip = '';
			if($remote_ip){
				$ip = '&remoteip='.$_SERVER['REMOTE_ADDR'];
			}
			$captcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.self::$key['secret_key'].'&response='.$_POST['g-recaptcha-response'].$ip);

			if(!empty($captcha)){
				$data = json_decode($captcha);
				$status = (boolean)$data->success;
				return $status;
			}
		}
	}
	/**
	* Render for load reCaptcha javascript
	*
	* @return html
	**/
	static public function script(){
		return '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
	}
	/**
	* render for form input
	*
	* @return html
	**/
	static public function form_input(){
		return '<div class="g-recaptcha" data-theme="light" data-sitekey="'.self::$key['site_key'].'"></div>';
	}
}