<?php
/**
 * PHP JSON wrapper
 *
 * @package Json
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

require_once dirname(__FILE__).'/../src/Json.php';
require_once dirname(__FILE__).'/../src/LastError.php';
require_once dirname(__FILE__).'/../src/Exception/Encoding.php';

function timeattack($label, $repeat, $content)
{
    $time_start = microtime(true);

    for ($i = 0; $i < $repeat; $i++) {
        $content();
    }

    $time = (microtime(true) - $time_start);

    printf("%s * %d: %.8f sec\n", $label, $repeat, $time);
}

$object = array(
        'name' => 'Luke Skywalker',
        'teacher' => 'Yoda',
        'job' => 'Jedi',
        'force' => true,
        'height' => 1.72,
        'weight' => 77,
        'droid' => array('C-3PO', 'R2-D2'),
    );

$array = array(
        2,
        3,
        5,
        7,
        11,
        13,
        17,
        19,
        23,
        29,
        31,
        37,
        41,
        43,
        47,
        53,
        59,
        61,
        67,
        71,
        73,
        79,
        83,
        89,
        97,
    );

$fp = fopen('php://memory', 'rw'); 

echo PHP_VERSION."\n";
echo "-------------------------\n";

timeattack('JSON encoding (Object)', 100000, function() use ($object) {
    // $ignore = json_encode($object);
    $ignore = \Json\Json::stringify($object);
});

timeattack('JSON encoding (Array)', 100000, function() use ($array) {
    // $ignore = json_encode($array);
    $ignore = \Json\Json::stringify($array);
});

timeattack('JSON encoding (Null)', 100000, function() {
    // $ignore = json_encode(null);
    $ignore = \Json\Json::stringify(null);
});

timeattack('JSON encoding (hasError)', 100000, function() use ($fp) {
    // $ignore = json_encode(array($fp));
    try {
        $ignore = \Json\Json::stringify(array($fp));
    } catch (\Json\Exception\Encoding $e) {}
});

echo "-------------------------\n";
echo "done.\n";

ob_start(function ($buffer) {
    return '';
});

?>

5.5.27
-------------------------
JSON encoding (Object) * 100000: 0.51506591 sec
JSON encoding (Array) * 100000: 0.27953506 sec
JSON encoding (Null) * 100000: 0.05300689 sec
JSON encoding (hasError) * 100000: 1.35517216 sec

5.5.27
-------------------------
JSON encoding (Object) * 100000: 0.63157988 sec
JSON encoding (Array) * 100000: 0.29753804 sec
JSON encoding (Null) * 100000: 0.05650711 sec
JSON encoding (hasError) * 100000: 1.38767600 sec

5.5.27
-------------------------
JSON encoding (Object) * 100000: 0.42255306 sec
JSON encoding (Array) * 100000: 0.28103590 sec
JSON encoding (Null) * 100000: 0.05300713 sec
JSON encoding (hasError) * 100000: 1.30566597 sec
