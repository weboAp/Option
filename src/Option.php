<?php namespace Weboap\Option;

use ArrayAccess;
use Carbon\Carbon as c;
use Illuminate\Cache\CacheManager as Cache;
use Illuminate\Config\Repository  as Config;
use Serializable;
use Weboap\Option\Exceptions\InvalidArgumentException;
use Weboap\Option\Interfaces\OptionClassInterface;
use Weboap\Option\Storage\OptionInterface as OptionInterface;



class Option implements ArrayAccess, Serializable, OptionClassInterface
{

    /**
     * The Config array
     *
     * @var array
     */
    protected $options = array();

    /**
     * The Config array
     *
     * @var string
     */
    protected $tableName = null;

    /**
     * The Option Repository Interface Instance
     *
     * @var OpenInterface
     */
    protected $storage;

    /**
     * The Cache Manager Instance
     *
     * @var Cache
     */
    protected $cache;
    
   
    

    /**
     * Initialize the Option Class
     * build the Config array.
     *
     * @param OptionInterface $storage The Database Interface
     * @param Cache           $cache
     */
    public function __construct(OptionInterface $storage , Cache $cache, Config $config)
    {
        $this->storage      = $storage;
        
        $this->cache        = $cache;
        
        $this->config       = $config;
        
        $this->options      = $this->storage->all()->lists('value', 'key');
    
    }
    

    public function set($key, $value)
    {
        $this->offsetSet($key, $value); 
    }

    public function batchSet(Array $array)
    {
        
        foreach ($array as $key => $value)
        {
            
            $this->offsetSet($key, $value);
            
        }
        
    }

    public function offsetSet($key, $value)
    {
        $key = $this->verify($key);
        
        if ($this->has($key))
        {
            $this->storage->update($key, $value);
        }
        else
        {
            $this->storage->create($key, $value);
        }
        
        $this->options[$key] = $value;
        
        // Clear the database cache
        $this->cache->flush();
        
    }

    /**
     * syntactic sugar for offsetGet($key)
     *
     */
    public function get($key)
    {
        return $this->offsetGet($key);
    }
    
    

    public function getGroup($group, $withPrefix = true)
    {
        $all    = $this->all();
        
        $prefix = $group . '.';
        
        if ($withPrefix) {
            
            foreach ($all as $key => $valueue)
            {
                if ( ! starts_with( $key, $prefix ))
                {
                    unset( $all[$key] );
                }
            }
        }
        else
        {
            
            $newAll = [];
            
            foreach ($all as $key => $valueue)
            {
                if (starts_with($key, $prefix))
                {
                    $newKey          = preg_replace("#^$prefix#", '', $key, 1);
                    $newAll[$newKey] = $valueue;
                }
            }
            
            $all = $newAll;
        }
        
        return count( $all ) > 0 ? $all : null;
    }
    
    

    public function offsetGet($key)
    {
        
        $key = $this->verify($key);
        
        return $this->offsetExists($key) ? $this->options[$key] : null;
    
    }

    /**
     * syntactic sugar for offsetUnset($key)
     *
     */
    public function forget($key)
    {
        $this->offsetUnset($key);
        
    }

    public function offsetUnset($key)
    {
        $key = $this->verify($key);
        
        //unset the key in array
        unset($this->options[$key]);
        
        //delete the key from db
        $this->storage->delete($key);
        
        // Clear the database cache
        $this->cache->flush();
    }

    /**
     * syntactic sugar for offsetExists($key)
     *
     */
    public function has($key)
    {
        return $this->offsetExists($key);
    }
    

    public function offsetExists($key)
    {
        $key = $this->verify($key);
       
        return array_key_exists( $key, $this->options );
    }

    public function all()
    {
        if ( count( $this->options ) == 0 )
        {
            return null;
        }
        
        return $this->options;
    }

    public function clear()
    {
        //clear database
        $this->storage->clear();
        
        // Clear the database cache
        $this->cache->flush();
    }

    public function serialize()
    {
        return serialize($this->options);
    }

    public function unserialize($serialized)
    {
        $config = unserialize($serialized);
        
        foreach ($config as $key => $value)
        {
            $this[$key] = $value;
        }
    }

    public function toJson()
    {
        return json_encode($this->options);
    }

    private function verify($key)
    {
        if ( ! is_string( $key ) || empty( $key ) )
        {
            throw new InvalidArgumentException('Invalid Option Key!');
        }
        
        $key = e(trim($key));
        
        $prefix = $this->config->get('option.default_prefix');
        
        if (empty($prefix))
        {
            throw new InvalidArgumentException('default_prefix can not be empty');
        }
        
        if ( ! str_is('*.*', $key) )
        {
            $key = sprintf('%s.%s', $prefix, $key);
        }
        
        return $key;
    }

    
}


