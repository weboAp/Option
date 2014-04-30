<?php namespace Weboap\Option\Storage;


use Weboap\Option\Models\OptionModel as o;


class OptionEloquentRepository implements OptionInterface{


protected $table;

public function __construct( $table )
{
    $this->table = $table;
}

public function all()
{
    // Query the database and cache it forever
   return o::rememberForever( $this->table )->get();
    
}
   
public function update($key, $value)
{
    $array = array('key' => $key, 'val'=>$value );
    
    return o::whereKey( $key )->update($array);
}    
    

public function create( $key, $value)
{
    $option = new o;
    
    $option->key = $key;
    $option->val = $value;
    
    $option->save();
    
}
    
public function delete($key)
{
    return o::whereKey( $key )->delete();   
    
}


public function clear()
{
     o::truncate();
}


}