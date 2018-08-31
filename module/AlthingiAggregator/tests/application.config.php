<?php
return array(
    'modules' => array(
        'AlthingiAggregator',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            '../../../config/test/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            'module',
            'vendor',
        ),
    ),
);
