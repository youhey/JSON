<?php
/**
 * PHP JSON wrapper
 *
 * @package Json
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

require_once dirname(__FILE__).'/../src/Json.php';
require_once dirname(__FILE__).'/../src/LastError.php';
require_once dirname(__FILE__).'/../src/Exception/Decoding.php';
require_once dirname(__FILE__).'/../src/Exception/SyntaxError.php';

function timeattack($label, $repeat, $content)
{
    $time_start = microtime(true);

    for ($i = 0; $i < $repeat; $i++) {
        $content();
    }

    $time = (microtime(true) - $time_start);

    printf("%s * %d: %.8f sec\n", $label, $repeat, $time);
}

$object = '{"name":"Luke Skywalker","teacher":"Yoda","job":"Jedi","force":true,"height":1.72,"weight":77,"droid":["C-3PO","R2-D2"]}';
$array = '[2,3,5,7,11,13,17,19,23,29,31,37,41,43,47,53,59,61,67,71,73,79,83,89,97]';

echo PHP_VERSION."\n";
echo "-------------------------\n";

timeattack('JSON decoding (Object)', 100000, function() use ($object) {
    // $ignore = json_decode($object);
    $ignore = \Json\Json::parse($object);
});

timeattack('JSON decoding (Array)', 100000, function() use ($array) {
    // $ignore = json_decode($array);
    $ignore = \Json\Json::parse($array);
});

timeattack('JSON decoding (Null)', 100000, function() {
    // $ignore = json_decode('');
    $ignore = \Json\Json::parse('');
});

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
