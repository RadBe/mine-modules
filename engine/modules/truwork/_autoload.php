<?php

spl_autoload_register(function ($classname) {

    if(class_exists($classname)) return;

    $file = TW_DIR . '/classes/' . $classname . '.php';

    $file = str_replace("\\", '/', $file);

    if(is_file($file)) {
        include_once($file);
    }

});
