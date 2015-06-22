<?php namespace Weboap\Option\Storage;


use Illuminate\Database\Eloquent\Model; 

class EloquentModel extends Model
{

    protected $table = null;

    protected $fillable = ['key', 'value'];
    
    public $timestamps = false;
    
   
    
    public function __construct()
    {
        $table = app('config')->get('option.table', 'options');
        
        $this->setTable($table);
    }
    
    
    public function setKeyAttribute($value)
    {
        $this->attributes['key'] = strtolower( $value );
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