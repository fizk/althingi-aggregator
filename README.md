#Althingi

ZF2 application. Place in your module directory under then name **Althingi**. Remember to register in `config/application.config.php`

I usually override `init_autoloader.php` so I can use the `vendor` directory inside the module and not the one in the skeleton application.
Replace content in  `init_autoloader.php` with

```php

// Composer autoloading
if (file_exists('module/Althingi/vendor/autoload.php')) {
    $loader = include 'module/Althingi/vendor/autoload.php';
}

if (class_exists('Zend\Loader\AutoloaderFactory')) {
    return;
}

$zf2Path = false;

if (is_dir('vendor/ZF2/library')) {
    $zf2Path = 'vendor/ZF2/library';
} elseif (getenv('ZF2_PATH')) {      // Support for ZF2_PATH environment variable or git submodule
    $zf2Path = getenv('ZF2_PATH');
} elseif (get_cfg_var('zf2_path')) { // Support for zf2_path directive value
    $zf2Path = get_cfg_var('zf2_path');
}

if ($zf2Path) {
    if (isset($loader)) {
        $loader->add('Zend', $zf2Path);
        $loader->add('ZendXml', $zf2Path);
    } else {
        include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
        Zend\Loader\AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true
            )
        ));
    }
}

if (!class_exists('Zend\Loader\AutoloaderFactory')) {
    throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
}

```

This makes PHPUnits tests easier to manage.
