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



``` php
//set one key
Option::set('key', 'value');

// or set an array of key, values
Option::batchSet(array('key'=>'value', 'key1'=>'value1', .....));

Option::get('key');

Option::forget('key');

Option::has('key');

//retreive all keys, values at once
Option::all(); 

//retreive all values serialized
Option::serialize();

Option::toJson();


or


you can access config as array like


$config = \App::make('option');

$config['foo'] = 'bar';

echo $config['foo'];

unset($config['foo']);




```


Enjoy!
 



