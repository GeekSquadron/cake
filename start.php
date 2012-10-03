<?php

Autoloader::namespaces(array(
	'Cake\Models' => Bundle::path('cake') . 'models',
	'Cake' => Bundle::path('cake') . 'libraries',
));

Autoloader::alias('Cake\\Registry', 'Registry');
Autoloader::alias('Cake\\Bootstrap\\Alert', 'Cupcake\Alert');

Autoloader::map(array(
	'Chef' => __DIR__ . '/auth/chef.php',
));

Auth::extend('chef', function()
{
	return new Chef;
});

Asset::container('cake')->bundle('cake');
Asset::container('cake')->add('bootstrap', 'vendors/css/bootstrap.min.css');
Asset::container('cake')->add('bootstrap-responsive', 'vendors/css/bootstrap-responsive.min.css');
Asset::container('cake')->add('jquery', 'vendors/js/jquery-1.8.2.min.js');
Asset::container('cake')->add('bootstrap-js', 'vendors/js/bootstrap.min.js');

Event::listen('laravel.started: cake', function()
{
	Registry::bake();
});