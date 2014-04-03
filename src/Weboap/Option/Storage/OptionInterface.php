<?php namespace Weboap\Option\Storage;



interface OptionInterface {

    public function all();
    
    public function update($key, array $data );
    
    public function create( array $data );
    
    public function delete( $key );
    
    public function clear();
    
    
}
