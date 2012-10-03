<?php

namespace Cake\Bootstrap;

use \Session;

class Alert {

	/**
	 * Set success alert
	 * 
	 * @param  string $message
	 * @param  array $params
	 * @param  array $args
	 * @return boolean
	 */
	public static function success($message, $params = null, $args = null)
	{
		$message = empty($message) ? 'Success message' : $message;
		Session::flash('message_type', 'success');

		return static::set_message($message, $params, $args);
	}

	/**
	 * Set error alert
	 * 
	 * @param  string $message
	 * @param  array $params
	 * @param  array $args
	 * @return boolean
	 */
	public static function error($message, $params = null, $args = null)
	{
		$message = empty($message) ? 'Error message' : $message;
		Session::flash('message_type', 'error');

		return static::set_message($message, $params, $args);
	}

	/**
	 * Set info alert
	 * 
	 * @param  string $message
	 * @param  array $params
	 * @param  array $args
	 * @return boolean
	 */
	public static function info($message, $params = null, $args = null)
	{
		$message = empty($message) ? 'Information' : $message;
		Session::flash('message_type', 'info');

		return static::set_message($message, $params, $args);
	}

	/**
	 * Set warning alert
	 * 
	 * @param  string $message
	 * @param  array $params
	 * @param  array $args
	 * @return boolean
	 */
	public static function warning($message, $params = null, $args = null)
	{
		$message = empty($message) ? 'Warning message' : $message;

		return static::set_message($message, $params, $args);
	}

	/**
	 * Render alert message
	 * 
	 * @param  array $args
	 * @return string
	 */
	public static function render($args = array())
	{
		if (Session::has('message') === false)
		{
			return false;
		}


		if (Session::has('id'))
		{
			if (! array_key_exists('id', $args) or $args['id'] != Session::get('id'))
			{
				return false;
			}
		}
		else
		{
			if (array_key_exists('id', $args))
			{
				return false;
			}
		}

		$message_type = Session::get('message_type');
		$alert = ( ! empty($message_type)) ?
		'alert alert-' . Session::get('message_type') : 'alert';

		$block = '';
		if (Session::has('block'))
		{
			$block = ' alert-block';
		}

		$class = (array_key_exists('class', $args) and ! empty($args['class'])) ?
		' ' . $args['class'] : '';

		$id = (array_key_exists('id', $args) and ! empty($args['id'])) ? ' id = ' . $args['id'] : '';

		$message = Session::get('message');
		$output[] = "<div class='{$alert}{$block}{$class}'{$id}>";
		$output[] = "<button type='button' class='close' data-dismiss='alert'>×</button>";
		$output[] = "{$message}";
		$output[] = "</div>";

		return implode($output, "\n");	
	}

	/**
	 * Create message
	 * 
	 * @param 	string $message
	 * @param 	array $params
	 * @param 	array $args
	 * @return  boolean
	 */
	protected static function set_message($message, $params = null, $args = null)
	{
		if (is_array($params))
		{
			foreach ($params as $key => $val)
			{
				$message = str_replace(':' . $key, $val, $message);
			}
		}

		if (is_array($args))
		{
			foreach ($args as $key => $val)
			{
				Session::flash($key, $val);
			}
		}

		Session::flash('message', $message);

		return true;
	}

}