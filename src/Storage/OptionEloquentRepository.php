<?php namespace Weboap\Option\Storage;

use Weboap\Option\Models\OptionModel as o;
use Cache;
use Config;

class OptionEloquentRepository implements OptionInterface
{

    protected $table;
    


    public function __construct()
    {
        $this->table = Config::get('option.table', 'options');
        
    }

    public function all()
    {
       
        return Cache::remember($this->table , 60, function()
                                {
                                    return o::all();
                                });

    }

    public function update($key, $value)
    {
       $option = o::whereKey($key)->first();
       
       $option->value = $value;
       
       $option->save();
       
    }

    public function create($key, $value)
    {
        $option = new o;
        
        $option->key = $key;
        
        $option->value = $value;
        
        $option->save();
        
    }

    public function delete($key)
    {
        return o::whereKey($key)->delete();
    }

    public function clear()
    {
        o::truncate();
    }
}