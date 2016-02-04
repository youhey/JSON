<?php
/**
 * PHP JSON wrapper
 *
 * @package Json
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    die('require 5.5>');
}

require_once dirname(__FILE__).'/../src/Json.php';
require_once dirname(__FILE__).'/../src/LastError.php';
require_once dirname(__FILE__).'/../src/Exception/Decoding.php';
require_once dirname(__FILE__).'/../src/Exception/SyntaxError.php';
require_once dirname(__FILE__).'/../src/Exception/Encoding.php';

function timeattack($label, $repeat)
{
    $bench = function() use ($label) {
        $start = microtime(true);
        yield;
        $time = (microtime(true) - $start);
        printf("%s: %.8f sec\n", $label, $time);
    };

    foreach ($bench() as $_) {
        for ($i = 0; $i < $repeat; $i++) {
            yield;
        }
    }
}

$php_object = array(
        'name' => 'Luke Skywalker',
        'teacher' => 'Yoda',
        'job' => 'Jedi',
        'force' => true,
        'height' => 1.72,
        'weight' => 77,
        'droid' => array('C-3PO', 'R2-D2'),
    );
$php_array = array(2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89, 97); 

$json_object = '{"name":"Luke Skywalker","teacher":"Yoda","job":"Jedi","force":true,"height":1.72,"weight":77,"droid":["C-3PO","R2-D2"]}';
$json_array = '[2,3,5,7,11,13,17,19,23,29,31,37,41,43,47,53,59,61,67,71,73,79,83,89,97]';

echo PHP_VERSION."\n";
echo "-------------------------\n";

foreach (timeattack('Json\Json decoding (Object)', 100000) as $_) {
    $ignore = \Json\Json::parse($json_object);
};

foreach (timeattack('Json\Json decoding (Array)', 100000) as $_) {
    $ignore = \Json\Json::parse($json_array);
}

foreach (timeattack('Raw-JSON decoding (Object)', 100000) as $_) {
    $ignore = @json_decode($json_object, false, 512, JSON_BIGINT_AS_STRING);
}

foreach (timeattack('Raw-JSON decoding (Array)', 100000) as $_) {
    $ignore = @json_decode($json_array, false, 512, JSON_BIGINT_AS_STRING);
}

$is_supported = version_compare(PHP_VERSION, '5.4.0', '>=');
foreach (timeattack('IfVersioning-JSON decoding (Object)', 100000) as $_) {
    if ($is_supported) {
        $ignore = @json_decode($json_object, false, 512, JSON_BIGINT_AS_STRING);
    } else {
        $ignore = @json_decode($json_object);
    }
}

$is_supported = version_compare(PHP_VERSION, '5.4.0', '>=');
foreach (timeattack('IfVersioning-JSON decoding (Array)', 100000) as $_) {
    if ($is_supported) {
        $ignore = @json_decode($json_array, false, 512, JSON_BIGINT_AS_STRING);
    } else {
        $ignore = @json_decode($json_array);
    }
}

foreach (timeattack('RealComp-JSON decoding (Object)', 100000) as $_) {
    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $ignore = @json_decode($json_object, false, 512, JSON_BIGINT_AS_STRING);
    } else {
        $ignore = @json_decode($json_object);
    }
}

foreach (timeattack('RealComp-JSON decoding (Array)', 100000) as $_) {
    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $ignore = @json_decode($json_array, false, 512, JSON_BIGINT_AS_STRING);
    } else {
        $ignore = @json_decode($json_array);
    }
}

if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
    $statement = '@json_decode($json_object, false, 512, JSON_BIGINT_AS_STRING);';
} else {
    $statement = '@json_decode($json_object);';
}
foreach (timeattack('Eval-JSON decoding (Object)', 100000) as $_) {
    $ignore = eval($statement);
}

if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
    $statement = '@json_decode($json_array, false, 512, JSON_BIGINT_AS_STRING);';
} else {
    $statement = '@json_decode($json_array);';
}
foreach (timeattack('Eval-JSON decoding (Array)', 100000) as $_) {
    $ignore = eval($statement);
}

if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
    $statement = function ($json) {
        return @json_decode($json, false, 512, JSON_BIGINT_AS_STRING);
    };
} else {
    $statement = function ($json) {
        return @json_decode($json);
    };
}
foreach (timeattack('Function-JSON decoding (Object)', 100000) as $_) {
    $ignore = $statement($json_object);
}

if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
    $statement = function ($json) {
        return @json_decode($json, false, 512, JSON_BIGINT_AS_STRING);
    };
} else {
    $statement = function ($json) {
        return @json_decode($json);
    };
}
foreach (timeattack('Function-JSON decoding (Array)', 100000) as $_) {
    $ignore = $statement($json_array);
}

foreach (timeattack('Json\Json encoding (Object)', 100000) as $_) {
    $ignore = \Json\Json::stringify($php_object);
}

foreach (timeattack('JSON encoding (Array)', 100000) as $_) {
    $ignore = \Json\Json::stringify($php_array);
}

foreach (timeattack('Raw-JSON encoding (Object)', 100000) as $_) {
    $ignore = @json_encode($php_object, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

foreach (timeattack('Raw-JSON encoding (Array)', 100000) as $_) {
    $ignore = @json_encode($php_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

$is_supported = version_compare(PHP_VERSION, '5.4.0', '>=');
foreach (timeattack('IfVersioning-JSON encoding (Object)', 100000) as $_) {
    if ($is_supported) {
        $ignore = @json_encode($php_object, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } else {
        $ignore = @json_encode($php_object);
    }
}

$is_supported = version_compare(PHP_VERSION, '5.4.0', '>=');
foreach (timeattack('IfVersioning-JSON encoding (Array)', 100000) as $_) {
    if ($is_supported) {
        $ignore = @json_encode($php_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } else {
        $ignore = @json_encode($php_array);
    }
}

foreach (timeattack('RealComp-JSON encoding (Object)', 100000) as $_) {
    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $ignore = @json_encode($php_object, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } else {
        $ignore = @json_encode($php_object);
    }
}

foreach (timeattack('RealComp-JSON encoding (Array)', 100000) as $_) {
    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $ignore = @json_encode($php_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } else {
        $ignore = @json_encode($php_array);
    }
}

if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
    $statement = '@json_encode($php_object, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);';
} else {
    $statement = '@json_encode($php_object);';
}
foreach (timeattack('Eval-JSON encoding (Object)', 100000) as $_) {
    $ignore = eval($statement);
}

if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
    $statement = '@json_encode($php_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);';
} else {
    $statement = '@json_encode($php_array);';
}
foreach (timeattack('Eval-JSON encoding (Array)', 100000) as $_) {
    $ignore = eval($statement);
}

if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
    $statement = function ($var) {
        return @json_encode($var, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    };
} else {
    $statement = function ($var){
        return @json_encode($var);
    };
}
foreach (timeattack('Function-JSON encoding (Object)', 100000) as $_) {
    $ignore = $statement($php_object);
}

if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
    $statement = function ($var) {
        return @json_encode($var, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    };
} else {
    $statement = function ($var){
        return @json_encode($var);
    };
}
foreach (timeattack('Function-JSON encoding (Array)', 100000) as $_) {
    $ignore = $statement($php_array);
}

echo "-------------------------\n";
echo "done.\n";

ob_start(function ($buffer) {
    return '';
});

?>

5.5.27
-------------------------
JSON decoding (Object) * 100000: 0.96562314 sec
JSON decoding (Array) * 100000: 0.78059888 sec
JSON decoding (Null) * 100000: 0.09851217 sec

5.5.27
-------------------------
JSON decoding (Object) * 100000: 0.81310296 sec
JSON decoding (Array) * 100000: 0.78910112 sec
JSON decoding (Null) * 100000: 0.08151007 sec

5.5.27
-------------------------
JSON decoding (Object) * 100000: 0.76809716 sec
JSON decoding (Array) * 100000: 0.80460286 sec
JSON decoding (Null) * 100000: 0.07550907 sec
