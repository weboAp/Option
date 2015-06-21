<?php namespace Weboap\Option\Storage;


use Illuminate\Database\Eloquent\Model; 

class EloquentModel extends Model
{

    protected $table = 'options';

    protected $fillable = ['key', 'value'];
    
    public $timestamps = false;
    
    protected $casts = [
                        'key' => 'string',
                        'value' => 'json'
                        ];
    
    public function __construct()
    {
        $table = app('config')->get('option.table', 'options');
        
        $this->setTable($table);
    }
    
    
    
    
    

   
}