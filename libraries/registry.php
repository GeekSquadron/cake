<?php 

namespace Cake;

use \Exception, \Config, \Cache, Cake\Models\Registry as Registry_Model;

class Registry {

	/**
	 * Registry items
	 *
	 * @var array
	 */
	protected static $items;

	/**
	 * Temporary items
	 *
	 * @var array
	 */
	protected static $temp;

	/**
	 * Registry class instantiation
	 *
	 * @return bool
	 */
	public static function bake()
	{
		$registries = Registry_Model::all();

		if (empty($registries)) return false;

		foreach ($registries as $registry)
		{
			static::$items[$registry->name] = $registry->value;
		}

		Cache::forever('registry', static::$items);

		return true;
	}

	/**
	 * Get registry items. Items can be retrieve either by group or by itself.
	 *
	 * @param string
	 * @param string
	 * @param bool
	 * @return string|array
	 */
	public static function get($name, $group = false)
	{
		if ( ! Cache::has('registry')) return false;

		$cache = Cache::get('registry');

		if ((bool)$group)
		{
			foreach ($cache as $key => $value)
			{
				if (strpos($key, $name.'_') !== false)
				{
					$items[$key] = (isset(static::$temp[$key])) ? static::$temp[$key] : $value;
				}
			}

			return $items;
		}

		return (isset(static::$temp[$name])) ?
		static::$temp[$name] : (isset($cache[$name]) ? $cache[$name] : null);
	}

	/**
	 * Set new registry items.
	 *
	 * @param string
	 * @param string
	 * @param bool
	 * @return bool
	 */
	public static function set($name, $value, $autosave = false)
	{
		if ((bool)$autosave)
		{
			unset(static::$temp[$name]);

			$old = Registry_Model::find($name);
			if ( !empty($old))
			{
				$old->value = $value;
				$old->save();
			}
			else
			{
				$new = Registry_Model::create(array('name' => $name, 'value' => $value));
				$new->save();
			}

			return static::make();
		}
		else
		{
			static::$temp[$name] = $value;
			return true;
		}

	}

	/**
	 * Save all temporary items into database
	 *
	 * @return bool
	 */
	public static function save()
	{
		foreach (static::$temp as $key => $value)
		{
			static::set($key, $value, true);
		}

		return true;
	}

	/**
	 * Delete items from registry
	 *
	 * @param string
	 * @return bool
	 */
	public static function forget($name)
	{
		unset(static::$temp['name']);

		$record = Registry_Model::find($name);

		if ( !empty($record))
		{
			$record->delete();
			return static::make();
		}
		else
		{
			throw new InvalidArgumentException("Property [{$name}] does not exists");
		}
	}

	/**
	 * Reset all temporary items
	 *
	 * @param string
	 * @return bool
	 */
	public static function reset($name = null)
	{
		if ( ! is_null($name))
		{
			unset(static::$temp[$name]);
		}
		else
		{
			unset(static::$temp);
		}

		return true;
	}

	/**
	 * Reload registry cache
	 *
	 * @return bool
	 */
	public static function reload()
	{
		return static::make();
	}

}