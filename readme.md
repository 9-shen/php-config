What is This?
=============
`Config` is a simple yet a powerfull library to allow end users to alter the
default configuration values provided by your package. and yes it can also be 
used in end projects.

Installation
============
`composer require elgoumri-oussama/php-config`

Getting Started
===============
```php
Config__load('configfile', [
    'key1' => [
        'key2' => [
            'key3' => 'value',  
        ],
    ],
    'key2' => false,
]);

$value = config('key1.key2.key3');
// return: 'value'

config('key2', true);   // Set the value of 'key2' to true
```

How it Work
===========
If you simply use `Config__load()` with no arguments, then it will allow you to
set configuration at runtime as you go.

`Config` put all the configuration files at `config` directory at the base of
your project, it will also create/use the `config` directory when it's used by a
package.

If `configfile` was not found, then it will create one, and export the default
configuration to it, allowing the end user to modify the configuration file 
however they want, `Config` will simply pickup the new configuration, and 
combine it with the default one.

Unlike other packages, `Config` support setting values as array, so use it if 
you need it, it's there :)

If no extension is given to the `configfile`, it will assume it's a php config
file, and export default configuration to it.

`Config` support both `json` and `php` format, so if you want to export default
configuration to `json` just use `.json` extension, ex: 
`Config__load('configfile.json', [ ... ])`

`Config` will create defaults configuration file for you out the default values
you provide, check out the example below.

If a `.env` file is found on the root directory of the project, `Config` will 
pick it up, and combine it with the default configuration.

Let's assume we have the following:
```php
Config__load('configfile', [
    'key' => [
        'subkey' => 'value',
    ],
]);

// configfile.php   - this file is generated automatically
<?php

return [
    'key' => [
        'subkey' => 'modified',      
    ],
];

// .env
# you can comment, and use empty lines
# you can emit " and ' when setting the values
key.subkey="i win"
```

When you run:
```php
$value = config('key.subkey');
// return: 'i win'
```

if `.env` file not exists, then you get: `modified` otherwise you get `value`
if you did not provide defaults, then `$value` will be `null`
