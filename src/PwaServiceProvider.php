<?php

namespace Sitedigitalweb\Pwa;

use Illuminate\Support\ServiceProvider;

class PwaServiceProvider extends ServiceProvider{
	
 public function register(){
 $this->app->bind('pwa', function($app){
 return new Pwa;
 });
 }

 public function boot(){
 require __DIR__ . '/Http/routes.php';
 $this->loadViewsFrom(__DIR__ . '/../views', 'pwa');
 $this->publishes([
 __DIR__ . '/migrations/2015_07_25_000000_create_usuario_table.php' => base_path('database/migrations/2015_07_25_000000_create_usuario_table.php'),
 ]);
 }

}
