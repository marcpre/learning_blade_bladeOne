<?php
require "vendor/autoload.php";
require "src/Spintax/Spintax.php";

Use eftec\bladeone;
use DaveChild\TextStatistics as TS;
use Noodlehaus\Config;
use Spintax;

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

$posts = "SELECT  wp_posts.* 
        FROM wp_posts  
        LEFT JOIN wp_term_relationships ON (wp_posts.ID = wp_term_relationships.object_id) WHERE 1=1  AND ( 
             wp_term_relationships.term_taxonomy_id = 43 OR wp_term_relationships.term_taxonomy_id = 55 
        ) AND wp_posts.post_type = 'computer-hardware' AND (wp_posts.post_status = 'publish') 
        GROUP BY wp_posts.ID 
        ORDER BY wp_posts.post_date";

$metaQuery = "SELECT * FROM wp_postmeta WHERE post_id = %s and meta_key = '%s' LIMIT 1;";

$result = $conn->query($posts);

/**
 * Create FINAL RESULT array
 */
$data = array();
$i = 0;
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        // array_push($data, $row);
        
        $manufacturer = $conn->query(createMetaQuery($row["ID"], 'manufacturer'))->fetch_assoc()["meta_value"];
        $algorithm = $conn->query(createMetaQuery($row["ID"], 'algorithm'))->fetch_assoc()["meta_value"];
        $hashRate = $conn->query(createMetaQuery($row["ID"], 'hash_rate'))->fetch_assoc()["meta_value"];
        $powerConsumption = $conn->query(createMetaQuery($row["ID"], 'watt_estimate'))->fetch_assoc()["meta_value"];
        $modelName = $row["post_title"];
        $category = $conn->query(createPostIDQuery($row["ID"]))->fetch_assoc()["name"];
     
        $i++;
        array_push($data, array(
            'postId' => $key, //artificial coin id 
            'company' => $manufacturer,
            'category' => $category,
            'algorithm' => $algorithm,
            'hashRate' => $hashRate,
            'powerConsumption' => $powerConsumption,
            'model' => $modelName,
            'listOfAlgorithms' => 'lolo ' . $i,
            'listOfCryptocurrencies' => 'MistOfCryptocurrencies ' . $i,
            'miningCosts' => 'MiningCosts ' . $i,
            'miningModel' => 'miningModel ' . $i,
            'dailyProfitOfMiner' => 'MiningCosts ' . $i,
            'numberOfMiningModels'  => 'numberOfMiningModels ' . $i,
            'dayToday' => date('F jS, Y', strtotime("now")),
            'monthToday' => date('F, Y', strtotime("now")),
        ));
    }
} else {
    echo "0 results";
}

echo "Fill template \n";

/**
 * Fill template
 **/
$finalOutput = '';
$spintax = new Spintax();
foreach ($data as $key => $value) {
    print_r($data[$key]);
    
    $output = '';
    
    $output = $blade->run("singlePost", $data[$key]);
    // create spintax
    $output = str_replace("</synonym>","}",$output);
    $output = str_replace("\">","|",$output);
    $output = str_replace("<synonym words=\"","{",$output);

    // replace tags
    $output = str_replace("<insertdata>","",$output);
    $output = str_replace("</insertdata>","",$output);
    $output = str_replace("<ifelse>","",$output);
    $output = str_replace("</ifelse>","",$output);
     
    // &nbsp;
    $output = str_replace("&nbsp;"," ",$output);
    $output = str_replace("/\s+/"," ",$output); // replace 1 or more spaces
    
    $finalOutput .= $spintax->process($output);   
    
    $finalOutput .= "\n ######################### \n";
    echo $finalOutput;
    // echo 'Flesch-Kincaid Reading Ease: ' . $textStatistics->fleschKincaidReadingEase($output) . "\n";
}

file_put_contents("./SINGLE_CONTENT_OUTPUT.txt", $finalOutput);

// close mysql connection
$conn->close();


function createMetaQuery ($postID, $metaValue) {
    $str = "SELECT * FROM wp_postmeta WHERE post_id = " . $postID ." and meta_key = '" . $metaValue ."' LIMIT 1;";
    return $str;
}

function createPostIDQuery ($postID) {
    $str = "SELECT t.* 
    FROM `wp_terms` t
    JOIN `wp_term_taxonomy` tt ON(t.`term_id` = tt.`term_id`)
    JOIN `wp_term_relationships` ttr ON(ttr.`term_taxonomy_id` = tt.`term_taxonomy_id`)
    WHERE tt.`taxonomy` = 'category'
    AND ttr.`object_id` = " . $postID;
    return $str;
}
