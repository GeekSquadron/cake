<?php

namespace Cake\Models;

use \Eloquent;

class Recovery extends Eloquent {

	public function user()
	{
		return $this->belongs_to('Cake\Models\User');
	}
	
}