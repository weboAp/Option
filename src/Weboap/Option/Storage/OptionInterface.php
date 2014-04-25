<?php namespace Weboap\Option\Storage;

interface OptionInterface
{

    public function all();

    public function update($key, $value);

    public function create($key, $value);

    public function delete($key);

    public function clear();
}
