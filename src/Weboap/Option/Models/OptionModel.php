<?php namespace Weboap\Option\Models;



use Illuminate\Support\Facades\Config as Config;
use Illuminate\Database\Eloquent\Model as Eloquent;


class OptionModel extends Eloquent {
    
        protected $table = null;
        
        protected $fillable = array( 'key', 'val');
      
      
      
        public function __construct()
        {
                 $table =  Config::get('option::table', 'options');
                 
                 $this->setTable($table);
                
        }
        
       
        public function setKeyAttribute($value)
	{
		$this->attributes['key'] = strtolower( $value );
	}
	
       
        public function setValAttribute($value)
	{
		if(is_string($value ) )
		{
			$value = e($value);
		}
		
		$this->attributes['val'] = serialize($value);
	}
        
        public function getValAttribute($value)
	{
		return @unserialize( $value );
	}
	
	public function getDates()
	{
		return array('created_at', 'updated_at');
	}
        
    
        
    
    
}