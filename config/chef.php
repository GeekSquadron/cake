<?php

return array(

	'username_column' => 'email',

	'password_column' => 'password',

	'user_model' => 'Cake\Models\User',

	'activation_separator' => '||',

	'roles' => array(
		'superadmin' => '10',
		'administrator' => '9',
		'user' => '1',
	),

	'permissions' => array(
		'add_user' => 'Create new user',
		'delete_user' => 'Delete existing user',
		'block_user' => 'Disable user account',
		'add_content' => 'Add new content',
		'delete_content' => 'Delete existing content',
		'view_content' => 'View content',
	),

	'permission_roles' => array(
		1 => 1,
		2 => 1,
		3 => 1,
		4 => 1,
		5 => 1,
		6 => 1
	),

	'default_superadmin_email' => 'ahmadshahhafizan@gmail.com',
	'default_superadmin_pass' => 'default',

);