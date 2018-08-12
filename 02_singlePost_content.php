<?php
require "vendor/autoload.php";

Use eftec\bladeone;
use DaveChild\TextStatistics as TS;
use Noodlehaus\Config;
use ChillDev\Spintax\Parser;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
define("BLADEONE_MODE", 1); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.
$blade = new bladeone\BladeOne($views,$cache);

$textStatistics = new TS\TextStatistics;

// read config
$conf = Config::load('./config.json');

// connect to db
$dbname = $conf['dbName'];
$dbuser = $conf['user'];
$dbpass = $conf['pwd'];
$dbhost = $conf['host'];

// Create connection
$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . " \n");
} 
echo "Connected successfully \n";

$sql = "SELECT  wp_posts.* 
        FROM wp_posts  
        LEFT JOIN wp_term_relationships ON (wp_posts.ID = wp_term_relationships.object_id) WHERE 1=1  AND ( 
             wp_term_relationships.term_taxonomy_id IN (43)
        ) AND wp_posts.post_type = 'computer-hardware' AND (wp_posts.post_status = 'publish') 
        GROUP BY wp_posts.ID 
        ORDER BY wp_posts.post_date";

$result = $conn->query($sql);

/**
 * Create FINAL RESULT array
 */
$data = array();
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // array_push($data, $row);
        array_push($data, array(
            // 'id' => $key, //artificial coin id 
            'company' => 'COMPANY1',
            'category' => 'CATEGORY1',
            'algorithm' => 'ALGORITHM1',
            'hashRate' => 'HASHRATE1',
            'powerConsumption' => 'POWERCONSUMPTION1',
            'model' => 'MODEL1',
            'listOfAlgorithms' => 'ListOfAlgorithms1',
            'listOfCryptocurrencies' => 'MistOfCryptocurrencies',
            'miningCosts' => 'MiningCosts1',
            'dailyProfitOfMiner' => 'MiningCosts',
            'today' => date('Y-m-d'),
        ));
    }
} else {
    echo "0 results";
}

echo "Fill template \n";

/**
 * Fill template
 **/
$output = '';
foreach ($data as $key => $value) {
    print_r($data[$key]);
       
    $output = $blade->run("singlePost", $data[$key]);
    // create spintax
    $output = str_replace("</synonym>","}",$output);
    $output = str_replace("\">","|",$output);
    $output = str_replace("<synonym words=\"","{",$output);

    // create spintax
    $output = str_replace("<insertdata>","",$output);
    $output = str_replace("</insertdata>","",$output);
 
    $output = Parser::parse($output);
    $finalOutput .= $output->generate();   

    
    $finalOutput .= "\n ######################### \n";
    echo $finalOutput;
    // echo 'Flesch-Kincaid Reading Ease: ' . $textStatistics->fleschKincaidReadingEase($output) . "\n";
}

file_put_contents("./SINGLE_CONTENT_OUTPUT.txt", $finalOutput);

// close mysql connection
$conn->close();
