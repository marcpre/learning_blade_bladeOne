<?php
require_once 'vendor/autoload.php';

use eftec\bladeone\BladeOne;
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
 * 2. Create view
 */
$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';

$blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
$finalOutput = "";
foreach ($data as $key => $value) {
    // directives
    $blade->directive('randomValFromArr', function ($expression) {
        return '<?php $array=' . $expression . '; $val = mt_rand(0, count($array) - 1); echo $array[$val]; ?>';
    });

    $output = $blade->run("complexPost", $value);

    $finalOutput .= $output;
    $finalOutput .= "\n ######################### \n";
}

file_put_contents("./COMPLEX_CONTENT_OUTPUT.txt", $finalOutput);


/**
 * Create FINAL RESULT array
 */
/*
$data = array();
$i = 0;
if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        // array_push($data, $row);

        // TODO remove after finish
        if ($row["ID"] == 4204) {

            echo "Get variables: " . $row["ID"] . "\n";

            $manufacturer = $conn->query(createMetaQuery($row["ID"], 'manufacturer'))->fetch_assoc()["meta_value"];
            $algorithm = $conn->query(createMetaQuery($row["ID"], 'algorithm'))->fetch_assoc()["meta_value"];
            $hashRate = $conn->query(createMetaQuery($row["ID"], 'hash_rate'))->fetch_assoc()["meta_value"];
            $powerConsumption = $conn->query(createMetaQuery($row["ID"], 'watt_estimate'))->fetch_assoc()["meta_value"];
            $modelName = $row["post_title"];
            $category = $conn->query(createPostIDQuery($row["ID"]))->fetch_assoc()["name"];
            $coins = getCoinList($row["ID"], $conn);
            $averageMiningCosts30 = getMiningCosts($row["ID"], $conn);
            $averageMiningProfit30 = getMiningProfitability($row["ID"], $conn);
            $miningModelsByCompany = getminingModelsByCompany($row["ID"], $manufacturer, $conn);
            $currentPrice = getAmazon($row["ID"], 'price', $conn);
            $comparisonTableArray = getComparisonTable($row["ID"], $manufacturer, $conn);

            $i++;
            array_push($data, array(
                'postId' => $row["ID"],
                'company' => $manufacturer,
                'category' => $category,
                'algorithm' => $algorithm,
                'hashRate' => number_format($hashRate),
                'powerConsumption' => number_format($powerConsumption),
                'model' => $modelName,
                'listOfAlgorithms' => $algorithm,
                'listOfCryptocurrencies' => $coins,
                'miningCosts' => number_format((float)$averageMiningCosts30, 2, '.', ''),
                'miningModel' => $miningModelsByCompany,
                'dailyProfitOfMiner' => number_format((float)$averageMiningProfit30, 5, '.', ''),
                'numberOfMiningModels' => $miningModelsByCompany,
                'currentPrice' => number_format((float)$currentPrice),
                'dayToday' => date('F jS, Y', strtotime("now")),
                'monthToday' => date('F, Y', strtotime("now")),
                'comparisonTableArray' => $comparisonTableArray,
            ));

            // TODO remove after finish
            break;
        } // TODO also remove this!
    }
} else {
    echo "0 results";
}

echo "Fill template \n";


$finalOutput = '';
$spintax = new Spintax();
foreach ($data as $key => $value) {
    print_r($data[$key]);

    $output = '';

    $output = $blade->run("singlePost", $data[$key]);
    // create spintax
    $output = str_replace("</synonym>", "}", $output);
    $output = str_replace("\">", "|", $output);
    $output = str_replace("<synonym words=\"", "{", $output);

    // replace tags
    $output = str_replace("<insertdata>", "", $output);
    $output = str_replace("</insertdata>", "", $output);
    $output = str_replace("<ifelse>", "", $output);
    $output = str_replace("</ifelse>", "", $output);

    // &nbsp;
    $output = str_replace("&nbsp;", " ", $output);
    $output = str_replace("  +", " ", $output); // replace 1 or more spaces

    $finalOutput .= $spintax->process($output);
    $finalOutput .= "\n ######################### \n";

    $finalOutput = str_replace("  +", " ", $finalOutput); // replace 1 or more spaces

    echo $finalOutput;
    // echo 'Flesch-Kincaid Reading Ease: ' . $textStatistics->fleschKincaidReadingEase($output) . "\n";
}

file_put_contents("./COMPLEX_CONTENT_OUTPUT.txt", $finalOutput);

*/