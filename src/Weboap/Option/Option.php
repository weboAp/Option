<?php namespace Weboap\Option;

use ArrayAccess;
use Carbon\Carbon as c;
use Illuminate\Cache\CacheManager as Cache;
use Illuminate\Config\Repository as Config;
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
    public function __construct( $tableName , OptionInterface $storage, Cache $cache, Config $setting )
    {
            $this->storage = $storage;
            $this->cache = $cache;
            $this->setting = $setting;

            $this->tableName = $tableName;

            $this->table = $this->storage->all();

            // Set the config array like a typical config file is structured
            $this->options = $this->table->lists('value', 'key');
    }

    public function set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    public function batchSet( Array $array)
    {
            foreach($array as $key => $value)
            {
                    $this->offsetSet($key, $value);
            }
    }

    /**
     * syntactic sugar for offsetGet($key)
     *
     */
    public function get($key)
    {
            $value = $this->offsetGet($key);

            return is_null( $value ) ? NULL : $value;
    }

    public function offsetSet($key, $value)
    {

            $key = $this->checkKey($key);

            $value = serialize( $value );
		

                    if($this->has($key)){

                                    $this->storage->update($key, array(
                                            'value'             => $value,
                                            'updated_at'	=> c::now()
                                    ));

                            }
                            else
                            {
                                    $this->storage->create(array(
                                            'key'               => $key,
                                            'value'             => $value,
                                            'updated_at'	=> c::now(),
                                            'created_at'	=> c::now()
                                    ));
                            }

            $this->options[$key] = $value;

            // Clear the database cache
            $this->cache->forget( $this->tableName );
    }





    public function offsetGet($key)
    {
            $key = $this->checkKey($key);

            return $this->offsetExists( $key ) ? unserialize( $this->options[$key]) : NULL;
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
            $key = $this->checkKey($key);

            //unset the key in config array
            unset($this->options[$key]);

            //delete the key from db
            $this->storage->delete($key);

            // Clear the database cache
            $this->cache->forget( $this->tableName );
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
            $key = $this->checkKey($key);

            return isset( $this->options[$key] );
    }

    public function all()
    {
            if( count( $this->options ) == 0 )
            {
                return null;
            }    

            foreach($this->options as $key => $value)
            {
		$this->options[$key] = @unserialize($value);
            }
	    
            return $this->options;

    }

    public function clear()
    {

            $this->storage->clear();
            // Clear the database cache
            $this->cache->forget( $this->tableName );

    }



    public function serialize()
    {
            return serialize($this->options);
    }

    public function unserialize($serialized)
    {
            $config = unserialize($serialized);
            foreach($config as $key => $value){
                    $this[$key] = $value;
            }
    }

    public function toJson()
    {
            return json_encode($this->options);
    }


    private function checkKey($key)
    {
            if( empty($key) || ! is_string( $key ))
            {
                    throw new InvalidArgumentException('Invalid Option Key!');
            }

            $key = strtolower( htmlentities( trim( $key ) ) );

            return $key;	
    }


}


