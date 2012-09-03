<?php
/**
 * Honeypot module.
 *
 * Creates Honeypot validation which is a replacement for captcha security. While this method is not
 * foolproof, it certainly has its good points. It can also serve as a CSRF security library as it checks
 * for token validity.
 *
 * @author  Neo Ighodaro <jeeniors@gmail.com>
 * @package Honeypot
 */
class Honeypot
{
	/**
	 * Singleton of the Honeypot_Bee class.
	 *
	 * @var Honeypot_Bee
	 */
	public static $instance;

	/**
	 * Creates a singleton instance of the Honeypot_Bee class.
	 *
	 * @return Honeypot_Bee
	 */
	public static function instance()
	{
		if (static::$instance === null)
		{
			static::$instance = new Honeypot_Bee;
		}

		return static::$instance;
	}

	/**
	 * Magically call methods from the Honeypot_Bee class.
	 *
	 * @param  string $method
	 * @param  array $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::instance(), $method), $parameters);
	}

}


class Honeypot_Bee
{
	/**
	 * Protect against CSRF attacks by validating form tokens.
	 *
	 * @var string
	 */
	public $csrf_field = 'hpb_token';

	/**
	 * Name of the field that should always be empty on every honeypot request.
	 *
	 * @var string
	 */
	public $empty_field = 'hpb_full_name';

	/**
	 * Name of the token key used to store the tokens.
	 *
	 * @var string
	 */
	public $token_key = 'hpb_token';

	/**
	 * Seconds that must pass before Honeypot marks request as valid. Typically set this to a low
	 * value as only bots can fill forms fast.
	 *
	 * @var integer
	 */
	public $valid_until = 5;

	/**
	 * Information about current token.
	 *
	 * @var array
	 */
	public $token = array();

	/**
	 * Driver used to save and fetch the honeypot tokens.
	 *
	 * @var Honeypot_Driver
	 */
	protected $driver;

	public function __construct()
	{
		// Instantiate driver
		$this->driver = new Honeypot_SessionNative;

		if ( ! $this->driver instanceof Honeypot_Driver)
		{
			throw new Exception("Driver {$driver} must extend the Honeypot_Driver");
		}
	}

	/**
	 * Creates a Honeypot form and optionally creates a new Honeypot session if no previous token
	 * has been created.
	 *
	 * @param  boolean $force_new
	 * @return string
	 */
	public function make($force_new = false)
	{
		if (empty($this->token) or $force_new === true)
		{
			$this->token = array(
				'id' => base64_encode(md5(uniqid(rand(), true))),
				'expires' => (time() + (int) $this->valid_until),
			);

			// Save data
			$this->save($this->token);
		}

		$form  = sprintf('<input type="hidden" name="%s" value="%s" />', $this->csrf_field, htmlentities($this->token['id'])).PHP_EOL;
		$form .= sprintf('<input type="hidden" name="%s" value="" />', $this->empty_field).PHP_EOL;

		return $form;
	}


	/**
	 * Checks to see if a form protected with Honeypot is valid.
	 *
	 * @return boolean
	 */
	public function check()
	{
		// Form posted MUST not have the empty field filled.
		if ( ! isset($_POST[$this->empty_field]) or $_POST[$this->empty_field] !== '')
			return false;

		// CSRF token must be posted, and must be exactly 10 characters.
		if ( ! isset($_POST[$this->csrf_field]) or empty($_POST[$this->csrf_field]))
			return false;

		// Get stored token data
		$token_data = $this->fetch();

		// Token data MUST have id and expires key to continue, and the token id must match posted token.
		if ( ! isset($token_data['id']) or ! isset($token_data['expires']) or $token_data['id'] !== $_POST[$this->csrf_field])
			return false;

		// The form was filled waaay too fast!
		if (time() < $token_data['expires'])
			return false;

		// ...and were done here.
		return true;
	}


	/**
	 * Acts as a setter and getter method for the valid_until property.
	 *
	 * @param  integer $minutes
	 * @return int
	 */
	public function valid_until($minutes = 1)
	{
		if (is_numeric($minutes) and $minutes > 0)
		{
			$this->valid_until = (int) $minutes;
		}

		return $this->valid_until;
	}


	/**
	 * Stores a token data.
	 *
	 * @param  array $token_data
	 * @return boolean
	 */
	protected function save($token_data)
	{
		return (bool) $this->driver->save($this->token_key, $token_data);
	}


	/**
	 * Fetches and deletes the Session token data if saved.
	 *
	 * @return array
	 */
	protected function fetch()
	{
		return (array) $this->driver->fetch($this->token_key);
	}

}
