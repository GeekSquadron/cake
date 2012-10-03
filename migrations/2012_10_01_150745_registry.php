<?php

class Cake_Registry {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		//Create registry table
		Schema::create('registries', function($table)
		{
			$table->string('name', 100);
			$table->string('value', 255);

			$table->primary('name');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		//Drop registry table
		Schema::drop('registries');
	}

}