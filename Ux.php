<?php
namespace Ux;
use Phalcon\Cache\Backend\Mongo;

date_default_timezone_set('Asia/Tehran');

require_once 'VaText.php';
/**
 * Return the value from an associative array or an object.
 * Taked from Garden core (for use this functions in other projects).
 *
 * @note Garden.Core function.
 * @param string $key The key or property name of the value.
 * @param mixed $collection The array or object to search.
 * @param mixed $default The value to return if the key does not exist.
 * @param bool $remove Whether or not to remove the item from the collection.
 * @return mixed $result The value from the array or object.
 * /
function getValue($key, &$collection, $default = false, $remove = false)
{
$result = $default;
if (is_array($collection) && array_key_exists($key, $collection)) {
$result = $collection[$key];
if ($remove) unset($collection[$key]);
} elseif (is_object($collection) && property_exists($collection, $key)) {
$result = $collection->$key;
if ($remove) unset($collection->$key);
}
return $result;
}

function println($string)
{
echo $string . PHP_EOL;
}


/***
 * @link http://stackoverflow.com/questions/14995307/sending-var-dump-to-firebug-console/15315520?noredirect=1#comment21631578_15315520
 */
function log($var, $name = '', $now = false)
{

//    if ($var === null)          $type = 'NULL';
//    else if (is_bool($var))     $type = 'BOOL';
//    else if (is_string($var))   $type = 'STRING[' . strlen($var) . ']';
//    else if (is_int($var))      $type = 'INT';
//    else if (is_float($var))    $type = 'FLOAT';
//    else if (is_array($var))    $type = 'ARRAY[' . count($var) . ']';
//    else if (is_object($var))   $type = 'OBJECT';
//    else if (is_resource($var)) $type = 'RESOURCE';
//    else                        $type = '???';

    $type = gettype($var);
    if (is_string($var)) $type .= '(' . strlen($var) . ')';
    if (is_array($var)) $type = '(' . count($var) . ')';


    if (strlen($name)) {
        strLog("$type $name: " . var_export($var, true), $now);
    } else {
        strLog("$type:" . var_export($var, true), $now);
    }
}

function strLog($str, $now = false)
{
    if ($now) {
        echo "<script type='text/javascript'>\n";
        echo "//<![CDATA[\n";
        echo "console.log(", json_encode($str), ");\n";
        echo "//]]>\n";
        echo "</script>";
    } else {
        register_shutdown_function('\Ux\strLog', $str, true);
    }
}

namespace Ux\Arr;

/***
 * @param array $array
 * @return array
 * @notes flatten associative multi dimension array recursive
 * @link http://cowburn.info/2012/03/17/flattening-a-multidimensional-array-in-php/
 */
function flatten(array $array)
{
    $output = array();
    array_walk_recursive($array, function ($current) use (&$output) {
        $output[] = $current;
    });
    return $output;
}

/***
 * @param array $array
 * @return array
 * @author R. Salarmehr
 * @author jackflash
 * @link http://stackoverflow.com/questions/13525893/array-permutation-in-php
 */
function permutation(array $array)
{
    $array = array_map(function ($v) {
        return (array)$v;
    }, $array);
    $count = array_map('count', $array);
    $finalSize = array_product($count);
    $arraySize = count($array);
    $output = array_fill(0, $finalSize, []);
    for ($i = 0; $i < $finalSize; $i++) {
        for ($c = 0; $c < $arraySize; $c++) {
            $output[$i][] = $array[$c][$i % $count[$c]];
        }
    }
    return $output;
}

/***
 * @param array $array1
 * @param array $array2
 * @return array
 * @link http://stackoverflow.com/questions/683702/how-do-you-perform-a-preg-match-where-the-pattern-is-an-array-in-php
 */
function union(array $array1, array $array2)
{
    return array_unique(array_merge($array1, $array2));
}

namespace Ux\Num;

/***
 * @param int $integer
 * @param boolean $uppercase
 * @return string
 */
function toRomanic($integer, $uppercase = true)
{
    if (!is_int($integer)) return $integer;
    $table = array(
        'M' => 1000,
        'CM' => 900,
        'D' => 500,
        'CD' => 400,
        'C' => 100,
        'XC' => 90,
        'L' => 50,
        'XL' => 40,
        'X' => 10,
        'IX' => 9,
        'V' => 5,
        'IV' => 4,
        'I' => 1,
    );
    $return = '';
    while ($integer > 0) {
        foreach ($table as $rom => $arb) {
            if ($integer >= $arb) {
                $integer -= $arb;
                $return .= $rom;
                break;
            }
        }
    }

    return ($uppercase) ? $return : strtolower($return);
}

function toWestern($string)
{
    $w = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $e = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    return str_replace($e, $w, $string);
}

function toEstern($srting)
{
    $w = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $e = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    return str_replace($w, $e, $srting);
}

/***
 * @param string $roman
 * @return int
 */
function toArabic($roman)
{
    if (!is_string($roman)) return $roman;
    $roman = strtoupper($roman);
    $romans = array(
        'M' => 1000,
        'CM' => 900,
        'D' => 500,
        'CD' => 400,
        'C' => 100,
        'XC' => 90,
        'L' => 50,
        'XL' => 40,
        'X' => 10,
        'IX' => 9,
        'V' => 5,
        'IV' => 4,
        'I' => 1,
    );

    $result = 0;

    foreach ($romans as $key => $value) {
        while (strpos($roman, $key) === 0) {
            $result += $value;
            $roman = substr($roman, strlen($key));
        }
    }
    return $result;
}

namespace Ux\Ref;
function nullDoc()
{
    $quirks = array(' null' => null, ' true' => true, 'false' => false, '    0' => 0, '    1' => 1, '   \0' => "\0", 'unset' => $unset, '  "" ' => '', '  [] ' => []);

    echo '        isset  is_null ===null  ==null  empty' . PHP_EOL;
    foreach ($quirks as $k => $var) {

        echo $k . ' ';
        echo isset($var) ? '|   T   ' : '|   F   ';
        echo is_null($var) ? '|   T   ' : '|   F   ';
        echo (null === $var) ? '|   T   ' : '|   F   ';
        echo (null == $var) ? '|   T   ' : '|   F   ';
        echo (empty($var)) ? '|   T   ' : '|   F   ';
        echo '|' . PHP_EOL;
    }
}

namespace Ux\Str;

function normalize($string, $lang = 'fa')
{
    if ($lang == 'fa') {
        $string = persinafy($string);
        $string = preg_replace(['@ي@u', '@ك@u', '#,#u', '#;#u', '#%#u', '#ـ+#u',], ['ی', 'ک', '،', '؛', '٪', ''], $string);
        $string = preg_replace('#(["\'`]+)(.+?)(\1)#u', '«\2»', $string);
        $string = preg_replace('#[ ‌  ]*([:;,؛،.؟!]{1})[ ‌  ]*#u', '\1 ', $string);
        $string = preg_replace('#([۰-۹]+):\s+([۰-۹]+)#u', '\1:\2', $string);
    }
//    $string = preg_replace('#\s*(\[)\s*([^)]+?)\s*?(\])\s*#u', ' \1\2\3 ', $string);
//    $string = preg_replace('#\s*(\{)\s*([^)]+?)\s*?(\})\s*#u', ' \1\2\3 ', $string);
//    $string = preg_replace('#\s*(\()\s*([^)]+?)\s*?(\))\s*#u', ' \1\2\3 ', $string);
//    $string = preg_replace('#\s*(«)\s*([^)]+?)\s*?(»)\s*#u', ' \1\2\3 ', $string);
//    $string = preg_replace('#\s*(“)\s*([^)]+?)\s*?(”)\s*#u', ' \1\2\3 ', $string);
    $string = preg_replace(['#\s*([\[\{\(«“])\s*#u', '#\s*([\]\}\)»”])\s*#u'], [' \1', '\1 '], $string);

    return $string;
}

function persinafy($string, $lang = 'fa')
{
    $noMedialForm = 'ادذرزژوآأإءة';
    $string = trim($string);
    $string = preg_replace('#(?<=[ادذرزژوآأإء])\x{200c}#u', '', $string);
    $string = preg_replace(['@ي@u', '@ك@u', '#,#u', '#;#u', '#%#u', '#ـ+#u',], ['ی', 'ک', '،', '؛', '٪', ''], $string);
    $string = preg_replace('#\s\x{200c}+|\x{200c}+\s+#u', ' ', $string);
    $string = preg_replace('#\s{2,}#u', ' ', $string);
    $string = preg_replace('#^\x{200c}|\x{200c}$#u', '', $string);
    return $string;
}


function fullTrim($string)
{
    return tailTrim(headTrim($string));
//    return mb_ereg_replace('.*','x',$string);
//    return mb_ereg_replace('(?:^[^\pL]*)|(?:[^\pL]*$)','',$string);
//    return preg_replace('#(?:^[^\pL]*)|(?:[^\pL]*$)#u','',$string);
}

function tailTrim($string)
{
    return preg_replace('#[^\pN\pL]+$#u', '', $string);
}

function headTrim($string)
{
    return preg_replace('#^[^\pN\pL]+#u', '', $string);
}


function random($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

namespace Ux\Date;
function elapsed($time, $date = false)
{
    if ($date) $time = time() - $time; // to get the time since that moment

    $tokens = array(
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
    }

}


