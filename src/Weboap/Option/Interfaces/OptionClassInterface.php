<?php namespace Weboap\Option\Interfaces;






interface OptionClassInterface {
    
   public function set(Array $array);
   
   public function get($key);
   
   public function forget($key);
   
   public function has($key);
    
}