<?php

class Honeypot_Session extends Honeypot_Driver
{
	/**
	 * Stores a token data.
	 *
	 * @param  array $token_data
	 * @return boolean
	 */
	public function save($key, $token_data)
	{
		return Session::instance()->set($key, $token_data);
	}


	/**
	 * Fetches and deletes the Session token data if saved.
	 *
	 * @return array
	 */
	public function fetch($key)
	{
		return Session::instance()->get_once($key, array());
	}

}
