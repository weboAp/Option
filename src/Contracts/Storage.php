<?php namespace Weboap\Option\Contracts;



interface Storage
{
    
    /**
    * Retreive all from db options/values.
    *
    * @return array
    */
    public function all();
    
    /**
    * update option value.
    *
    * @return void
    */
    public function update($key, $value);

    /**
    * create a new option and set value.
    *
    * @return void
    */
    public function create($key, $value);

    /**
    * delete option.
    *
    * @return void
    */
    public function delete($key);

    /**
    * delete all options entries from db.
    *
    * @return array
    */
    public function clear();
}
