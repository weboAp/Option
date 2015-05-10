<?php namespace Weboap\Option\Storage;


use Illuminate\Database\Eloquent\Model as Eloquent; 

class EloquentModel extends Eloquent
{

    protected $table = null;

    protected $fillable = ['key', 'value'];
    
    public $timestamps = false;
    
    
    protected $config;
    
    
    public function __construct()
    {
        $table = app('config')->get('option.table', 'options');
        
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