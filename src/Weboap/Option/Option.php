<?php namespace Weboap\Option;

use ArrayAccess;
use Carbon\Carbon as c;
use Illuminate\Cache\CacheManager as Cache;
use Serializable;
use Weboap\Option\Exceptions\InvalidArgumentException;
use Weboap\Option\Interfaces\OptionClassInterface;
use Weboap\Option\Storage\OptionInterface as OptionInterface;




class Option implements ArrayAccess, Serializable, OptionClassInterface{
	
    /**
    * The Config array
    * @var array
    */
    protected $options = array();

    /**
    * The Config array
    * @var string
    */	
    protected $tableName = null;

    /**
    * The Option Repository Interface Instance
    * @var OpenInterface
    */
    protected $storage;

    /**
    * The Cache Manager Instance
    * @var Cache
    */
    protected $cache;

    /**
    * The Config Instance
    * @var Config
    */
    protected $setting;

    /**
    * Initialize the Option Class
    * build the Config array.
    * @param OptionInterface $storage The Database Interface
    * @param Cache $cache
    */
    public function __construct( OptionInterface $storage, Cache $cache, $cachekey = 'options' )
    {
            
	    $this->storage = $storage;
            $this->cache = $cache;

            $this->cachekey = $cachekey;

            $this->table = $this->storage->all();

            // Set the config array like a typical config file is structured
            $this->options = $this->table->lists('val', 'key');
    }
    

    public function set($key, $val)
    {
        $this->offsetSet($key, $val);
    }
    

    public function batchSet( Array $array)
    {
            foreach($array as $key => $val)
            {
                    $this->offsetSet($key, $val);
            }
    }
    
    
    public function offsetSet($key, $val)
    {

            $key = $this->verify($key);
	    
		if($this->has($key)){
	
			    $this->storage->update($key,  $val);
	
			}
			else
			{
			    $this->storage->create($key, $val );
			       
			}

            $this->options[$key] = $val;

            // Clear the database cache
            $this->cache->forget( $this->cachekey );
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
            foreach ($all as $key => $value) {
                if (!starts_with($key, $prefix)) {
                    unset($all[$key]);
                }
            }
        } else {
            $newAll = [];
            foreach ($all as $key => $value) {
                if (starts_with($key, $prefix)) {
                    $newKey          = preg_replace("#^$prefix#", '', $key, 1);
                    $newAll[$newKey] = $value;
                }
            }
            $all = $newAll;
        }
        return count($all) > 0 ? $all : null;
    }


    public function offsetGet($key)
    {
            $key = $this->verify($key);
		
            return $this->offsetExists( $key ) ? $this->options[$key] : NULL;
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
            $this->cache->forget( $this->cachekey );
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

            return isset( $this->options[$key] );
    }

    public function all()
    {
            if( count( $this->options ) == 0 )
            {
                return null;
            }    
	    
            return $this->options;

    }

    public function clear()
    {

            $this->storage->clear();
            // Clear the database cache
            $this->cache->forget( $this->cachekey );

    }

    public function serialize()
    {
            return serialize($this->options);
    }

    public function unserialize($serialized)
    {
            $config = unserialize($serialized);
            foreach($config as $key => $val){
                    $this[$key] = $val;
            }
    }

    public function toJson()
    {
            return json_encode($this->options);
    }


    private function verify($key = '')
    {
        if (empty($key) || !is_string($key)) {
            throw new InvalidArgumentException('Invalid Option Key!');
        }
        $key = e(trim($key));
        $key = strtolower($key);
        $key = $this->toPrefix($key);
        if (strlen($key) > 50) {
            throw new InvalidArgumentException('Invalid Option Key Length, max=50!');
        }
        return $key;
    }

    private function toPrefix($key)
    {
        $prefix = \Config::get('option::default_prefix', 'global');
        if (empty($prefix)) {
            throw new Exception('default_prefix can not be empty');
        }
        if (!str_is('*.*', $key)) {
            $key = sprintf('%s.%s', $prefix, $key);
        }
        return $key;
    }
    
    


}


