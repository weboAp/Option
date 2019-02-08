<?php

namespace Weboap\Option\Storage;

use Weboap\Option\Contracts\Storage as StorageContract;

class EloquentRepository implements StorageContract
{
    /**
     * returns all records from db.
     *
     * @param Instance of CacheContract $cache
     *
     * @return array/null
     */
    public function all()
    {
        return EloquentModel::pluck('value', 'key')->toArray();
    }

    /**
     * update option value in db.
     *
     * @param  $key  string
     * @param $value mixed
     */

    /**
     * update option value in db.
     *
     * @param  $key  string
     * @param $value mixed
     */
    public function update($key, $value)
    {
        $o = EloquentModel::whereKey($key)->first();
        if ($o) {
            $o->value = $value;

            $o->save();
        }
    }

    /**
     * create option in db.
     *
     * @param  $key  string
     * @param $value mixed
     */
    public function create($key, $value)
    {
        $o = new EloquentModel();

        $o->key = $key;
        $o->value = $value;

        $o->save();
    }

    /**
     * delete option key/value.
     *
     * @param  $key  string
     *
     * @return true/false
     */
    public function delete($key)
    {
        return EloquentModel::whereKey($key)->delete();
    }

    /**
     * delete all options from db.
     */
    public function clear()
    {
        EloquentModel::truncate();
    }
}
