<?php namespace Weboap\Option\Interfaces;



interface OptionClassInterface {
    
   public function set($key, $value);
   
   public function batchSet(array $array);
   
   public function get($key);
   
   public function forget($key);
   
   public function has($key);
   
   public function all();
    
}