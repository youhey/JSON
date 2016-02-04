<?php

set_include_path(rtrim(dirname(dirname(__FILE__)), '/').'/src'.PATH_SEPARATOR.get_include_path());

// Set default timezone
date_default_timezone_set('Asia/Tokyo');

function autoLoader($class_name)
{
    $class_name = ltrim($class_name, '\\');
    $file_name  = '';
    $namespace  = '';
    if (strrpos($class_name, 'Json\\') === 0) {
        $class_name = substr($class_name, strlen('Json\\'));
    }
    if ($last_ns_pos = strrpos($class_name, '\\')) {
        $namespace  = substr($class_name, 0, $last_ns_pos);
        $class_name = substr($class_name, $last_ns_pos + 1);
        $file_name  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $file_name .= str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

    if (stream_resolve_include_path($file_name)) {
        require_once $file_name;
    }
}

spl_autoload_register('autoLoader');
