<?php

namespace Cake\Models;

use \Eloquent;

class User extends Eloquent {

	public static $cache;

	public function role()
	{
		return $this->belongs_to('Cake\Models\Role');
	}

	public function recovery()
	{
		return $this->has_many('Cake\Models\Recovery');
	}

	/**
	 * Check if the user has the role or not
	 * 
	 * @param  array  $roles
	 * @return boolean
	 */
	public function is($roles)
	{
		$roles = ( ! is_array($roles)) ? (array) $roles : $roles;

		$verified = false;

		foreach ($roles as $role)
		{
			if ($this->role->name === $role)
			{
				$verified = true;
				break;
			}
		}

		return $verified;
	}

	public function can($permissions)
	{
		$permissions = ( ! is_array($permissions)) ? (array) $permissions : $permissions;

		$class = get_class();

		if (empty($this->cache))
		{
			$verify = new $class;

			$verify = $class::with(array('role', 'role.permission'))
					->where('id', '=', $this->get_attribute('id'))
					->first();

			$this->cache = $verify;
		}
		else
		{
			$verify = $this->cache;
		}

		if ($this->is('superadmin')) return true;

		$verified = false;

		foreach ($verify->role->permission as $permission)
		{
			if (in_array($permission->name, $permissions))
			{
				$verified = true;
				break;
			}
		}

		return $verified;
	}

}