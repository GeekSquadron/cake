<?php

namespace Cake\Models;

use \Eloquent;

class Permission extends Eloquent {

	public function role()
	{
		return $this->has_many_and_belongs_to('Cake\Models\Role', 'permission_roles');
	}

}