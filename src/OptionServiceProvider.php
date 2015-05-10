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
        $this->RegisterOption();
        $this->RegisterBooting();
        
    }

    public function RegisterOption()
    {
       
       
       $this->app->singleton('option',  function ($app)
                             {
               
                                          return new Option(
                                                            $app['Weboap\Option\Contracts\Storage'],
                                                            $app['Illuminate\Contracts\Config\Repository'],
                                                            $app['Illuminate\Contracts\Cache\Repository']
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

    protected function RegisterStorage()
    {
       
       
       $this->app->singleton(
                           'Weboap\Option\Contracts\Storage', function ($app)
                           {
                             return new \Weboap\Option\Storage\EloquentRepository(
                                       $app->make('Weboap\Option\Storage\EloquentModel')                                           
                                                                                  );  
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
