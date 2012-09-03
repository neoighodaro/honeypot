<?php

class Honeypot_SessionNative extends Honeypot_Driver
{
	/**
	 * Stores a token data.
	 *
	 * @param  array $token_data
	 * @return boolean
	 */
	public function save($key, $token_data)
	{
		@session_start();
		return $_SESSION[$key] = @serialize($token_data);
	}


	/**
	 * Fetches and deletes the Session token data if saved.
	 *
	 * @return array
	 */
	public function fetch($key)
	{
		@session_start();
		return isset($_SESSION[$key]) ? @unserialize($_SESSION[$key]) : array();
	}

}
