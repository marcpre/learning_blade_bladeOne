<?php
require_once 'vendor/autoload.php';

use eftec\bladeone\BladeOne;
use Faker\Factory;


/**
 * 1. Create Fake Data
 */
$faker = Factory::create();

$valArr = array();
for ($i = 0; $i < 10; $i++) {
    $r = array(
        'name' => $faker->name,
        'company' => $faker->company,
        'text' => $faker->text,
        'randomDigit' => $faker->randomDigit,
        'randomElement' => $faker->randomElement([1, 3, 5, 7, 9]),
    );
    array_push($valArr, $r);
}

/**
 * 2. Read template
 */
$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
$blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);


$blade->directive('randomValFromArr', function ($expression) {
    return '<?php $array=' . $expression . '; $val = mt_rand(0, count($array) - 1); echo $array[$val]; ?>';
});
/*
$blade->directive('randomValFromArr', function ($expression) {
    return '<?php $array=' . $expression . '; $val = mt_rand(0, count($array) - 1); echo print_r($array[$val],true); ?>';
});
*/
$filesArr = array_diff(scandir($views . "/Content"), array('.', '..'));

/**
 * 3. Create view
 */
$finalOutput = "";
foreach ($valArr as $key => $value) {
    $randView = trim(str_replace(".blade.php", "", $filesArr[array_rand($filesArr, 1)]));

    try {
        $content = $blade->run('Content.' . $randView, $value);
        //$content = $blade->run('Content/' . $randView, $valArr);
    } catch (\Exception $ex) {
        error_log($ex);
    }
    $finalOutput .= $content;
    $finalOutput .= "\n ######################### \n";
}

file_put_contents("./MULTIPLE_FOLDERS.txt", $finalOutput);
