<?php

class Cake_Auth {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		//Roles table
		Schema::create('roles', function($table)
		{
			$table->increments('id');

			$table->string('name', 100)->index();
			$table->integer('level');
		});

		Schema::create('permissions', function($table)
		{
			$table->increments('id');

			$table->string('name', 100)->index();
			$table->string('description', 255);
			$table->timestamps();
		});

		//User table
		Schema::create('users', function($table)
		{
			$table->increments('id');

			$table->string('email', 150);
			$table->string('password', 60);
			$table->string('username', 150)->nullable();
			$table->string('first_name', 255);
			$table->string('last_name', 255)->nullable();
			$table->integer('role_id')->unsigned()->index();
			$table->string('activation_key', 255)->nullable();
			$table->boolean('verified')->default(0);
			$table->boolean('disabled')->default(0);
			$table->boolean('deleted')->default(0);
			$table->boolean('forgot')->default(0);
			$table->timestamps();

			$table->foreign('role_id')->references('id')->on('roles')->on_delete('restrict');
		});

		//Reset password
		Schema::create('recoveries', function($table)
		{
			$table->increments('id');

			$table->integer('user_id')->unsigned()->index();
			$table->string('temp_pass', 60);
			$table->string('ip', 100)->default('0.0.0.0');
			$table->string('agent', 255);
			$table->boolean('resolved')->default(0);
			$table->timestamps();

			$table->foreign('user_id')->references('id')->on('users')->on_delete('cascade');
		});

		//Role permissions
		Schema::create('permission_roles', function($table)
		{
			$table->increments('id');

			$table->integer('permission_id')->unsigned()->index();
			$table->integer('role_id')->unsigned()->index();
			$table->timestamps();

			$table->foreign('permission_id')->references('id')->on('permission_roles');
			$table->foreign('role_id')->references('id')->on('roles');
		});

		foreach(Config::get('cake::chef.roles') as $key => $val)
		{
			DB::table('roles')->insert(array('name' => $key, 'level' => $val));
		}

		foreach(Config::get('cake::chef.permissions') as $key => $val)
		{
			DB::table('permissions')->insert(array('name' => $key, 'description' => $val, 'created_at' => DB::raw('NOW()')));
		}

		foreach(Config::get('cake::chef.permission_roles') as $key => $val)
		{
			DB::table('permission_roles')->insert(array('permission_id' => $key, 'role_id' => $val, 'created_at' => DB::raw('NOW()')));
		}

		DB::table('users')->insert(array(
			'email' => Config::get('cake::chef.default_superadmin_email'),
			'password' => Hash::make(Config::get('cake::chef.default_superadmin_password')),
			'first_name' => 'Super',
			'last_name' => 'Admin',
			'username' => 'superadmin',
			'role_id' => 1,
			'verified' => 1,
			'created_at' => DB::raw('NOW()'),
		));

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		//Drop tables
		Schema::drop('recoveries');
		Schema::drop('users');
		Schema::drop('permission_roles');
		Schema::drop('permissions');
		Schema::drop('roles');
	}

}