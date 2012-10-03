<?php

use Laravel\Auth\Drivers\Driver, \Exception;

class ChefUserNotFoundException extends Exception {}
class ChefRegistrationException extends Exception {}
class ChefActivationException extends Exception {}

class Chef extends Driver {

	/**
	 * Get the a given application user by ID.
	 *
	 * @param  int    $id
	 * @return mixed
	 */
	public function retrieve($id)
	{
		if (filter_var($id, FILTER_VALIDATE_INT) !== false)
		{
			return $this->model('user')->find($id);
		}
	}

	/**
	 * Attempt to log a user into the application.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function attempt($arguments = array())
	{
		$email_column = Config::get('cake::chef.username_column');

		$user = $this->model('user')
		->where($email_column, '=', $arguments['username'])
		->first();

		if (is_null($user))
		{
			throw new ChefUserNotFoundException('User not found');
		}

		// This driver uses a basic username and password authentication scheme
		// so if the credentials match what is in the database we will just
		// log the user into the application and remember them if asked.
		$password_column = Config::get('cake::chef.password_column');

		if ( ! is_null($user) and Hash::check($arguments['password'], $user->$password_column))
		{
			return $this->login($user->id, array_get($arguments, 'remember'));
		}
		else
		{
			throw new ChefUserNotFoundException('Email and password combination does not match');
		}
	}

	/**
	 * Account activation
	 * 
	 * @param  string
	 * @return boolean
	 */
	public function activate($encrypted)
	{
		$decrypt = Crypter::decrypt($encrypted);
		$blocks = explode(Config::get('cake::chef.activation_separator'), $decrypt);

		$user = $this->model('user')
		->where('email', '=', $blocks[0])
		->first();

		if (is_null($user))
		{
			throw new ChefActivationException('User not found');
		}
		elseif ((bool)$this->status('verified', $user))
		{
			throw new ChefActivationException('This account is already activated');
		}
		elseif (Hash::check($blocks[1], $user->activation_key) === false)
		{
			throw new ChefActivationException('Invalid activation key');
		}

		$user->verified = 1;
		$user->save();

		return true;
	}

	/**
	 * New user registration
	 * 
	 * @param  array
	 * @return array
	 */
	public function register($post)
	{	
		//Verify if the email existed
		$exist = $this->model('user')
		->where('email', '=', $post['email'])
		->first();

		if ( ! is_null($exist))
		{
			throw new ChefRegistrationException('This email is already registered');
		}

		$username = (array_key_exists('username', $post) and ! empty($post['username'])) ?
		$post['username'] : '';
		
		$first_name = (array_key_exists('first_name', $post) and ! empty($post['first_name'])) ?
		$post['first_name'] : '';

		$last_name = (array_key_exists('last_name', $post) and ! empty($post['last_name'])) ?
		$post['last_name'] : '';

		$activation_key = Str::random(20);
		$user_model = Config::get('cake::chef.user_model');

		$user = $user_model::create(array(
			'email' => $post['email'],
			'password' => Hash::make($post['password']),
			'role_id' => $post['role_id'],
			'username' => $username,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'activation_key' => Hash::make($activation_key)
		));

		//Create activation link
		$encrypted = Crypter::encrypt($user->email . Config::get('cake::chef.activation_separator') . $activation_key);

		return array($user->id, $activation_key, $encrypted);
	}

	/**
	 * Forgot password
	 *  
	 * @return string
	 */
	public function recovery($email)
	{
		$user = $this->model('user')
		->where('email', '=', $email)
		->first();

		if (is_null($user))
		{
			throw new ChefUserNotFoundException('Email does not exists');
		}

		//Generate temporary password
		$temp_pass = Str::random(15);
		//Remove existing request
		$user->recovery()->delete();

		$recovery = array(
			'temp_pass' => Hash::make($temp_pass),
			'ip' => Request::ip(),
			'agent' => $_SERVER['HTTP_USER_AGENT'],
		);
		$user->recovery()->insert($recovery);

		$user->password = Hash::make(Str::random(20));
		$user->forgot = 1;
		$user->save();

		return $temp_pass;
	}

	/**
	 * Account status
	 * 
	 * @param  string $status
	 * @return boolean|array
	 */
	public function status($status = null, $user = null)
	{
		$user = (! is_null($this->user()) and is_null($user)) ? $this->user() : $user;

		if ( ! is_null($status))
		{
			$status = $user->$status;

			return ((bool)$status) ? true : false;
		}

		$all = array();
		$fields = array('verified', 'disabled', 'deleted', 'forgot');

		foreach ($fields as $field)
		{
			$all[$field] = ((bool)$user->$field) ? true : false;
		}

		return $all;
	}

	/**
	 * Get a fresh model instance.
	 *
	 * @return Eloquent
	 */
	protected function model()
	{
		$model = Config::get('cake::chef.user_model');

		return new $model;
	}

}