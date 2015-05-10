<?php namespace Weboap\Option\Storage;

use Weboap\Option\Storage\EloquentModel;
use Illuminate\Contracts\Cache\Repository as CacheContract;

use Weboap\Option\Contracts\Storage as StorageContract;


class EloquentRepository implements StorageContract
{

   protected $model;
   
   
   
    /**
     * Initialize the Eloquent Repository Class
     *
     * @param EloquentModel Eloquent Model
     */
   public function __construct(EloquentModel $model)
   {
    $this->model = $model;
   }
   
    /**
     * returns all records from db.
     *
     * @param  Instance of CacheContract  $cache
     * @return array/null
     */
    public function all()
    {
       return $this->model->all();
    }
    
    /**
     * update option value in db.
     *
     * @param  $key  string
     * @param $value mixed
     * @return void
     */
    public function update($key, $value)
    {
      //create is using eloquent save(), which
      // will create new record or update an existing on
      
      $this->create($key, $value);
       
    }

    /**
     * create option in db.
     *
     * @param  $key  string
     * @param $value mixed
     * @return void
     */
    public function create($key, $value)
    {
        $op = new EloquentModel;
        
        $op->key = $key;
        
        $op->value = $value;
        
        $op->save();
        
    }
    
    /**
     * delete option key/value.
     *
     * @param  $key  string
     * @return true/false
     */
    public function delete($key)
    {
        return $this->model->whereKey($key)->delete();
    }
    
    /**
     * delete all options from db.
     * @return void
     */
    public function clear()
    {
        $this->model->truncate();
    }
}