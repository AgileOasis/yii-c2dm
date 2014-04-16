<?php

/**
 * Yii extension for sending messages through Android's C2DM.
 *
 * Yes, its trivial.. but.. well.. *shrug*
 *
 * @package C2DM
 * @author Navarr
 */

class C2DM extends CApplicationComponent
{
	/**
	 * The username for logging in to the C2DM Service
	 * @var string
	 */
	public $username;
	
	/**
	 * The password for logging in to the C2DM Service
	 * @var string
	 */
	public $password;
	
	/**
	 * Application (i.e. com.example.app)
	 * @var string
	 */
	public $applicationIdentifier;
	
	/**
	 * Initializes the application component.
	 * This method is required by {@link IApplicationComponent} and is invoked by application.
	 */
	public function init()
	{
		if(empty($this->username) || empty($this->password) || empty($this->applicationIdentifier))
			throw new CException('C2DM Username, Password, and Application are required variables.');
		
		parent::init();
	}
	
	/**
	 * Login to the Google Auth Service 
	 * @return string $authenticationToken
	 */
	public function _login()
	{
		$token = explode("Auth=", $this->_curl("https://www.google.com/accounts/ClientLogin",array(
			"accountType" => "HOSTED_OR_GOOGLE",
			"Email" => $this->username,
			"Passwd" => $this->password,
			"service" => "ac2dm",
			"source" => $this->applicationIdentifier,
		)));
		return $token[1];
	}
	
	/**
	 * Push a message to a single (or an array of) device(s)
	 * 
	 * @param mixed $registrationIDs
	 * @param mixed $message
	 * @param string $collapse_key
	 * @return array $errors An array of arrays of registration IDs, whether or not they errored, and what their return message was
	 */
	public function push($pushID, $data, $delay_while_idle = false, $collapse_key = null)
	{
		$auth = $this->_login();
		
		$errors = array();
		$post = array(
			"collapse_key" => ($collapse_key ? $collapse_key : md5(implode(";",$data))),
		);
		
		foreach($data as $k => $v) $post["data.{$k}"] = $v; 
		if($delay_while_idle) $post["delay_while_idle"] = 'true';
		
		if(!is_array($pushID)) $pushID = array($pushID);
		
		foreach($pushID as $id)
		{
			$post["registration_id"] = $id;
			$response = $this->_curl("https://android.apis.google.com/c2dm/send",$post,array("Authorization: GoogleLogin auth={$auth};"));
			
			if(stripos($response, "Error") === false) $errors[] = array($id,false,$response);
			else $errors[] = array($id,true,$response);
		}
		
		return $errors;
	}
	
	/**
	 * Simple curl interface for the extension.
	 *
	 * @param string $uri
	 * @param array $postVariables
	 * @param array $headers
	 * @return string $response
	 */
	public function _curl($address,$data,$header = null)
	{
		$curl = curl_init($address);
		curl_setopt($curl, CURLOPT_POST, true);
		if($header) curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		else curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		$response = curl_exec($curl);
		curl_getinfo($curl);
		curl_close($curl);
		return $response;
	}
}
