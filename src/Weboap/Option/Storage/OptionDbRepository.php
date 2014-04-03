<?php namespace Weboap\Option\Storage;


use Illuminate\Support\Facades\DB;

class OptionDbRepository implements OptionInterface{

protected $table = null;

public function __construct( $table )
{
    $this->table = $table;
}

public function all()
{
    // Query the database and cache it forever
   return DB::table( $this->table )->rememberForever( $this->table );
    
}
   
public function update($key, array $data)
{
    return DB::table( $this->table )->whereKey( $key )->update($data);
}    
    

public function create( array $data)
{
    return DB::table( $this->table )->insert($data);
    
}
    
public function delete($key)
{
    return DB::table( $this->table )->whereKey( $key )->delete();   
    
}


public function clear()
{
     DB::table( $this->table )->truncate();
}


}