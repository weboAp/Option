<?php namespace Weboap\Option\Contracts;




interface Repository {

	/**
	 * Determine if the given option value exists.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key);

        /**
	 * Retreive all  options/values.
	 *
	 * @return array
	 */
        public function all();
        
	/**
	 * Get the specified option value.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function get($key, $default = null);

	/**
	 * Set a given option value.
	 *
	 * @param  array|string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function set($key, $value = null);

	/**
	 * Prepend a value onto an array option value.
	 *
	 * @param  string  $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function prepend($key, $value);

	/**
	 * Push a value onto an array option value.
	 *
	 * @param  string  $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function push($key, $value);

	/**
	 * delete the specified option value.
	 *
	 * @param  string  $key
	 * @return true|false
	 */
        public function forget($key);




}
