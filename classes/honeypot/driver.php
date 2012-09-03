<?php

abstract class Honeypot_Driver
{
	abstract public function save($key, $token_data);
	abstract public function fetch($key);
}
