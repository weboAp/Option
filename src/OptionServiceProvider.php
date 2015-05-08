<?php namespace Weboap\Option;

use Illuminate\Support\ServiceProvider;

use Config;

class OptionServiceProvider extends ServiceProvider
{

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
       $this->publishes([
                        realpath(__DIR__.'/migrations') => base_path('/database/migrations') ],
                       'migrations');
      
      $this->publishes([
                      __DIR__.'/config/option.php' => config_path('option.php'),
                  ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $tableName = Config::get('option.table');
        
        $this->RegisterStorage($tableName);
        $this->RegisterOption($tableName);
        $this->RegisterBooting();
        
    }

    public function RegisterOption( $tableName )
    {
       
       
       $this->app->singleton('option',  function ($app) use ($tableName)
                             {
               
                                          return new Option(
                                                            $app->make( 'Weboap\Option\Storage\OptionEloquentRepository' ),
                                                            $app['cache'],
                                                            $app['config']
                                                            );
                                                         }
                           );
       
    }

    public function RegisterBooting()
    {
        $this->app->booting(
            function () {
                $loader = \Illuminate\Foundation\AliasLoader::getInstance();
                $loader->alias('Option', 'Weboap\Option\Facades\Option');
            }
        );
    }

    protected function RegisterStorage( $tableName )
    {
       
       
       $this->app->singleton(
                           'Weboap\Option\Storage\OptionInterface', function ($app) use ($tableName)
                           {
                             return new \Weboap\Option\Storage\OptionEloquentRepository( $tableName );  
                           }
                           
                       );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['option'];
    }
}
