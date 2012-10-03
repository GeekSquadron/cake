<?php

namespace Cake\Models;

use \Eloquent;

class Role extends Eloquent {

	public static $timestamps = false;

	public function user()
	{
		return $this->has_many('Cake\Models\User');
	}

	public function permission()
	{
		return $this->has_many_and_belongs_to('Cake\Models\Permission', 'permission_roles');
	}

}