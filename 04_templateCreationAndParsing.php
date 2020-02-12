<?php
require_once 'vendor/autoload.php';

use eftec\bladeone;
use Faker\Factory;


/**
 * 1. Create Fake Data
 */

$faker = Factory::create();

$data = array();
for ($i = 0; $i < 10; $i++) {
    $r = array(
        'name' => $faker->name,
        'company' => $faker->company,
        'text' => $faker->text,
        'randomDigit' => $faker->randomDigit,
        'randomElement' => $faker->randomElement([1, 3, 5, 7, 9]),
    );
    array_push($data, $r);
}

/**
 * 2. Read fake template
 */
$filecontent = file_get_contents('htmlTemplate/templateFile.html', true);
// parse Content
// displayData
/**
 * @param string $str
 * @return string
 */
function createBladeTemplate(string $str): string
{
    $str .= str_replace('<displayData data-display=\"', '{{', $str);
    $str .= str_replace('\"></displayData>', "}}", $str);
    return $str;
}

$filecontent = createBladeTemplate($filecontent);

// save as view
$tempFile = fopen("views/TEST_TEMPLATE.blade.php", "w");
fwrite($tempFile, $filecontent);

/**
 * 3. Create view
 */

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';

$blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);

$finalOutput = "";
foreach ($data as $key => $value) {
    // directives
    $blade->directive('synonym', function ($expression) {
        return '<?php $array=' . $expression . '; $val = mt_rand(0, count($array) - 1); echo $array[$val]; ?>';
    });

    $output = $blade->run("complexPost", $value);

    $finalOutput .= $output;
    $finalOutput .= "\n ######################### \n";
}

file_put_contents("./CONTENT_OUTPUT.txt", $finalOutput);
