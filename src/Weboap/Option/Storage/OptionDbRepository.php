<?php namespace Weboap\Option\Storage;

use Config;
use Illuminate\Support\Facades\DB;

class OptionDbRepository implements OptionInterface{

protected $tableName = null;

public function __construct()
{
    $this->tableName = Config::get('option::table');
}

public function all()
{
    // Query the database and cache it forever
   return DB::table( $this->tableName )->rememberForever( $this->tableName );
    
}
   
public function update($key, array $data)
{
    return DB::table( $this->tableName )->whereKey( $key )->update($data);
}    
    

public function create( array $data)
{
    return DB::table( $this->tableName )->insert($data);
    
}
    
public function delete($key)
{
    return DB::table( $this->tableName )->whereKey( $key )->delete();   
    
}


}