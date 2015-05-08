<?php namespace Weboap\Option\Models;

use  Config;
use Illuminate\Database\Eloquent\Model as Eloquent; 

class OptionModel extends Eloquent
{

    protected $table = null;

    protected $fillable = ['key', 'value'];
    
    public $timestamps = false;
    
    
    public function __construct()
    {
        $table = Config::get('option.table', 'options');
        
        $this->setTable($table);
    }
    
    public function setKeyAttribute($value)
    {
        $this->attributes['key'] = $value;
    }
    
    public function setValueAttribute($value)
    {
       $this->attributes['value'] = serialize($value);
    }
    
    public function getValueAttribute($value)
    {
        return @unserialize($value);
    }
    
   
}