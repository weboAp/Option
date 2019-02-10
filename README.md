#Option 
==============

Save and retreive config from db for laravel / Access Config as Array

Laravel 4 users use "weboap/option": "v1.0.5"

### Installation


The recommended way to install Winput is through composer.

## Step 1
```php
php composer require weboap/option
```

## Step 2 (optional)

Add config/app.php if you are using laravel <5.5

``` php
'Weboap\Option\OptionServiceProvider'
``` 



## Step 3 

Migrate the Option Table
Run

``` php
php artisan vendor:publish
php artisan migrate
``` 

you can check app/option.php to costumize Option config file.


###  Usage

Note : the keys are to be set group.key (eg: 'gallery.path', 'cache.timeout')
 that way the options are grouped per categories
keys that don't have a group the package will prepend 'global.' to
eg: seting a key : 'business' will be retreived like 'global.business'

``` php


//set one key
Option::set('group.key', 'value');

// or set an array of key, values
Option::set(['key0'=> 'value', 'group.key'=>'value', 'someothergroup.key1'=>'value1', .....]);


Option::get('group.key');
Option::get('global.key');

Option::group('prefix'); // will return an array of options prefixed with group


Option::forget('group.key');

Option::has('group.key');

//retreive all keys, values at once
Option::all(); 




or


you can access config as array like


$config = App::make('option');

$config['mygroup.foo'] = 'bar';

echo $config['mygroup.foo'];

unset($config['mygroup.foo']);




```


Enjoy!
 



