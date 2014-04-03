<?php namespace Weboap\Option;

use Weboap\Option\Storage\OptionInterface as OptionInterface;
use Weboap\Option\Interfaces\OptionClassInterface;
use Illuminate\Cache\CacheManager as Cache;

use Illuminate\Exception\Handler as Exception;
use Weboap\Option\Exceptions\SaveException;
use Weboap\Option\Exceptions\InvalidArgumentException;

use Carbon\Carbon as c;
use Illuminate\Config\Repository as Config;

use App, ArrayAccess, Serializable;





class Option implements ArrayAccess, Serializable, OptionClassInterface{
	
	/**
	* The Config array
	* @var array
	*/
	protected $config = array();
	
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
	public function __construct(
				    OptionInterface $storage = null,
				    Cache $cache = null,
				    Config $setting = null
				    )
	{
		$this->storage = (isset($storage) ? $storage : App::make('Weboap\Option\Storage\OptionInterface') );
		$this->cache = (isset($cache) ? $cache : App::make('cache') );
		$this->setting = (isset($setting) ? $setting : App::make('config') );
		
		$this->tableName = $this->setting->get('option::table');
		
		$this->table = $this->storage->all();

		// Set the config array like a typical config file is structured
		$this->config = $this->table->lists('value', 'key');
	}
	
	
	
	public function set( Array $array)
	{
		foreach($array as $key => $value)
		{
			$this->offsetSet($key, $value);
		}
	}
	
	
	
	public function offsetSet($key, $value)
	{
		
		$key = $this->checkKey($key);
		
		$value = serialize( $value );
		
			
			if($this->has($key)){

					$this->storage->update($key, array(
						'value' => $value,
						'updated_at'	=> c::now()
					));
				
				}
				else
				{
					$this->storage->create(array(
						'key' => $key,
						'value' => $value,
						'updated_at'	=> c::now(),
						'created_at'	=> c::now()
					));
				}
		
		$this->config[$key] = $value;

		// Clear the database cache
		$this->cache->forget( $this->tableName );
	}
	
	
	/**
	 * syntactic sugar for offsetGet($key)
	 *
	 */
	public function get($key)
	{
		$value = $this->offsetGet($key);
		
		return is_null( $value ) ? NULL : unserialize( $value );
	}
	
	
	public function offsetGet($key)
	{
		$key = $this->checkKey($key);
		
		return $this->offsetExists( $key ) ? $this->config[ $key ] : NULL;
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
		unset($this->config[$key]);
		
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
		
		return isset( $this->config[$key] );
	}
	
	public function all()
	{
		if(count($this->config) == 0 ) return null;
		
		foreach($this->config as $key => $value)
		{
			$value = unserialize($value);
		}
		
		return $this->config;
	
	}
	
	public function clear()
	{
    
		$this->storage->clear();
		// Clear the database cache
		$this->cache->forget( $this->tableName );
    
	}
	

	
	public function serialize()
	{
		return serialize($this->config);
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
		return json_encode($this->config);
	}
	
	
	private function checkKey($key)
	{
		if( '' === $key || !is_string( $key ))
		{
			throw new InvalidArgumentException('Invalid Option Key!');
		}
	
		$key = htmlentities( trim( $key ) );
		
		return $key;	
	}
    
    
}


