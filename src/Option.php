<?php namespace Weboap\Option;

use ArrayAccess;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Weboap\Option\Exceptions\InvalidArgumentException;
use Weboap\Option\Contracts\Repository as OptionContract;
use Weboap\Option\Contracts\Storage as StorageContract;



class Option implements ArrayAccess, OptionContract
{

        /**
     * The Items array
     *
     * @var array
     */
    protected $items = [];

    /**
     * The Option Storage Repository Interface Instance
     *
     * @var OpenInterface
     */
    protected $storage;
    

    /**
     * Illuminate\Config\Repository instance
     *
     * @var CacheContract
     */
    protected $config;
    
   
     /**
     * Illuminate\Cache\Repository instance
     *
     * @var CacheContract
     */
     protected $cache;   

    /**
     * Initialize the Option Class
     * build the Config array.
     *
     * @param StorageContract $storage The Database Interface
     * @param CacheContract   $cache Laravel CacheContract
     */
    public function __construct(StorageContract $storage , ConfigContract $config, CacheContract $cache)
    {
        $this->storage      = $storage;
        
        $this->cache        = $cache;
        
        $this->config        = $config;
        
        
        
        $options = $cache->rememberForever('weboap.options' ,function()
                                {
                                    return $this->storage->all();
                                });
        
        $this->items      =  $options->lists('value', 'key');
       
    
    }
    
    
    /**
     * Determine if the given option value exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
            return array_has($this->items, $key);
    }   
    
    
    /**
     * Get the specified option value.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
            return array_get($this->items, $key, $default);
    }
    
    
    
    /**
     * Set a given option value.
     *
     * @param  array|string  $key
     * @param  mixed   $value
     * @return void
     */
    public function set($key, $value = null)
    {
             
            if (is_array( $key ))
            {
                    foreach ($key as $innerKey => $innerValue)
                    {
                            //verify key
                            $innerKey = $this->verify($innerKey);

                            //create or update db option
                            $this->store($innerKey, $innerValue);

                            //create option entry
                            array_set($this->items, $innerKey, $innerValue);
                    }
            }
            else
            {
                    //verify key
                    $key = $this->verify($key);
                    
                    //create or update db option
                    $this->store($key, $value);
                    
                    //create option entry
                    array_set($this->items, $key, $value);
            }
            
        // Clear the database cache
        $this->cache->forget('weboap.options');
    }
    
    /**
     * Create or update option db entry.
     *
     * @param  array|string  $key
     * @param  mixed   $value
     * @return void
     */    
    private function store($key, $value = null)
    {
        if ($this->has($key))
        {
            $this->storage->update($key, $value);
        }
        else
        {
            $this->storage->create($key, $value);
        }
         
    }
    
    
    /**
     * Prepend a value onto an array option value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function prepend($key, $value)
    {
            $array = $this->get($key);

            array_unshift($array, $value);

            $this->set($key, $array);
    }
    
    /**
     * Push a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function push($key, $value)
    {
            $array = $this->get($key);

            $array[] = $value;

            $this->set($key, $array);
    }
    
    
    public function forget($key)
    {
        array_forget($this->items, $key);
        
        //delete the key from db
        $this->storage->delete($key);
        
        // Clear the database cache
        $this->cache->forget('weboap.options');
    }
    
    
    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all()
    {
            return $this->items;
    }

   
    /**
     * Determine if the given configuration option exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
            return $this->has($key);
    }


    /**
     * Get a configuration option.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
            return $this->get($key);
    }
    
    
    /**
     * Set a configuration option.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
            $this->set($key, $value);
    }
    
    /**
     * Unset a configuration option.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->forget($key);
    }    
    

    public function group($group, $prefix = true)
    {
        if( empty($group) ) return null;
        
        $all    = $this->all();
        
        $group = $group . '.';
        
        if ( $prefix ) {
            
            foreach ($all as $key => $val)
            {
                $this->verify($key);
                
                if ( ! starts_with( $key, $group ))
                {
                    unset( $all[$key] );
                }
            }
        }
        else
        {
            
            $newAll = [];
            
            foreach ($all as $key => $val)
            {
                $this->verify($key);
                
                if (starts_with($key, $group))
                {
                    $newKey          = preg_replace("#^$group#", '', $key, 1);
                    $newAll[$newKey] = $val;
                }
            }
            
            $all = $newAll;
        }
        
        return count( $all ) > 0 ? $all : null;
    }
    
    



    public function clear()
    {
        //clear database
        $this->storage->clear();
        // clear cached options
        $this->cache->forget('weboap.options');
        
    }

  


    private function verify($key)
    {
        if ( ! isset( $key ) || ! is_string( $key ) )
        {
            throw new InvalidArgumentException('Invalid Option Key!');
        }
        
        
        $group = $this->config->get('option.group');
        
        if ( empty( $group ) )
        {
            throw new InvalidArgumentException('default prefix can not be empty');
        }
        
        if ( ! str_is('*.*', $key) )
        {
            $key = sprintf('%s.%s', $group, $key);
        }
        
        return $key;
    }

    
}


