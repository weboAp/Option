#Option 
==============

Save and retreive config from db for laravel 4 / Access Config as Array

### Installation


The recommended way to install Winput is through composer.

## Step 1

Just add to  `composer.json` file:

``` json
{
    "require": {
        "weboap/option": "dev-master"
    }
}
```

then run 
``` php
php composer.phar update
```

## Step 2

Add
``` php
'Weboap\Option\OptionServiceProvider'
``` 

to the list of service providers in app/config/app.php

## Step 3 

Migrate the Option Table
Run

``` php
php artisan migrate --package="weboap/option"
``` 

to migrate option table



###  Usage

Note : the keys are to be set group.key (eg: 'gallery.path', 'cache.timeout')
 that way the options are grouped per categories
keys that don't have a group the package will prepend 'global.' to
eg: seting a key : 'business' will be retreived like 'global.business'

``` php


//set one key
Option::set('group.key', 'value');

// or set an array of key, values
Option::batchSet(array('key0'=> 'value', 'group.key'=>'value', 'someothergroup.key1'=>'value1', .....));


Option::get('group.key');
Option::get('global.key');

Option::getGroup('group'); // will return an array of options prefixed with group


Option::forget('group.key');

Option::has('group.key');

//retreive all keys, values at once
Option::all(); 

//retreive all values serialized
Option::serialize();

Option::toJson();


or


you can access config as array like


$config = \App::make('option');

$config['mygroup.foo'] = 'bar';

echo $config['mygroup.foo'];

unset($config['mygroup.foo']);




```


Enjoy!
 



