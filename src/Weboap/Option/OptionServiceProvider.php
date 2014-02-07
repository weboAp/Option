<?php namespace Weboap\Option;

use Illuminate\Support\ServiceProvider;

class OptionServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('weboap/option');
		
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->RegisterStorage();

		$this->RegisterOption();
				
		$this->RegisterBooting();
	}
	
	
	public function RegisterOption()
	{
		
		$this->app['option'] = $this->app->share(function($app)
			{
			    return new Option(
					      $this->app->make('Weboap\Option\Storage\OptionInterface'),
					      $app['cache'],
					      $app['config']
					      );
			
			});
		
		
	}
	
	
	
	public function RegisterBooting()
	{
		
		 $this->app->booting(function()
				{
				   $loader = \Illuminate\Foundation\AliasLoader::getInstance();
				   $loader->alias('Option', 'Weboap\Option\Facades\Option');
			
				    
				});
		
		
	}
	
	
	protected function RegisterStorage()
	{
		$this->app->singleton(
			'Weboap\Option\Storage\OptionInterface',
			'Weboap\Option\Storage\OptionDbRepository'
                );
	}
	

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('option');
	}

}