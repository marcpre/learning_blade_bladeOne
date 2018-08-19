<?php
require "vendor/autoload.php";
require "src/Spintax/Spintax.php";

error_reporting(E_ALL ^ E_NOTICE);  

use eftec\bladeone;
use DaveChild\TextStatistics as TS;
use Noodlehaus\Config;
use Spintax;
use Noodlehaus\Exception;

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
        
        echo "Get variables: " . $row["ID"] .  "\n";
        $row["ID"] = 4204;
        
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
        $currentPrice = getCurrentPrice($row["ID"], $conn);
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
            'numberOfMiningModels'  => $miningModelsByCompany,
            'currentPrice' => number_format((float)$currentPrice),
            'dayToday' => date('F jS, Y', strtotime("now")),
            'monthToday' => date('F, Y', strtotime("now")),
            'comparisonTableArray' => $comparisonTableArray,
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

    $finalOutput = str_replace("/\s+/"," ",$finalOutput); // replace 1 or more spaces   

    echo $finalOutput;
    // echo 'Flesch-Kincaid Reading Ease: ' . $textStatistics->fleschKincaidReadingEase($output) . "\n";
}

file_put_contents("./SINGLE_CONTENT_OUTPUT.txt", $finalOutput);

// close mysql connection
$conn->close();


/**
 * Functions
 **/

function createMetaQuery ($postID, $metaValue) {
    $str = "SELECT * FROM wp_postmeta WHERE post_id = " . $postID ." and meta_key = '" . $metaValue ."' LIMIT 1;";
    return $str;
}

function getMetaQuery($postID, $metaValue, $conn) {
    $str = "SELECT * FROM wp_postmeta WHERE post_id = " . $postID ." and meta_key = '" . $metaValue ."' LIMIT 1;";
    
    $res = $conn->query($str)->fetch_assoc() or die($conn->error);;
    $dat = "";
    /*
    while($ro = $res->fetch_assoc()) {
        $dat .= $ro["post_title"] . ", ";
    }
    */
    return $res;
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

function getCoinList ($postID, $conn) {
    $coins = $conn->query(createMetaQuery($postID, 'related_coins'))->fetch_assoc()["meta_value"];

    if(empty($coins)) {
        return "";
    }
    

    if(is_serialized($coins)) {
        $coins = unserialize($coins);
        $para = "";
        foreach ($coins as $key => $value) {
            $para .= $coins[$key] . ", ";
        }
        $para = preg_replace("/,\s$/", '', $para ); //remove last , from string
    } else {
        $para = $coins;
    }
    
    $str = "SELECT * FROM `wp_posts` WHERE ID IN (" . $para . ")";
    
    $res = $conn->query($str) or die($conn->error);;
    $dat = "";
    while($ro = $res->fetch_assoc()) {
        $dat .= $ro["post_title"] . ", ";
    }
    $dat = preg_replace("/,\s$/", '', $dat ); //remove last , from string
    return $dat;
}

function getMiningCosts ($postID, $conn) {
    $res = $conn->query("SELECT avg(daily_costs) FROM wp_miningprofitability WHERE created_at >= NOW() - INTERVAL 30 DAY AND post_id = " . $postID . ";")->fetch_assoc()["avg(daily_costs)"];
    return $res;
}

function getMiningProfitability ($postID, $conn) {
    $res = $conn->query("SELECT avg(daily_grossProfit) FROM wp_miningprofitability WHERE created_at >= NOW() - INTERVAL 30 DAY AND post_id = " . $postID . ";")->fetch_assoc()["avg(daily_grossProfit)"];
    return $res;
}

function getminingModelsByCompany($postID, $manufacturer, $conn) {
    $query = "SELECT P.ID, P.post_title, P.post_content, P.post_author, meta_value
FROM wp_posts AS P
LEFT JOIN wp_postmeta AS PM on PM.post_id = P.ID
WHERE P.post_type = 'computer-hardware' and P.post_status = 'publish' and meta_value = '" . $manufacturer ."' 
ORDER BY P.post_date DESC";
    
    $res = $conn->query($query) or die($conn->error);;
    $dat = "";
    while($ro = $res->fetch_assoc()) {
        $dat .= $ro["post_title"] . ", ";
    }
    $dat = preg_replace("/,\s$/", '', $dat ); //remove last , from string
    $dat = str_replace($manufacturer, '', $dat ); //remove last , from string
    $dat = trim($dat);
    return $dat;
}

function getComparisonTable($postID, $manufacturer, $conn) {
    $query = "SELECT P.ID, P.post_title, P.post_content, P.post_author, meta_value, P.guid
FROM wp_posts AS P
LEFT JOIN wp_postmeta AS PM on PM.post_id = P.ID
WHERE P.post_type = 'computer-hardware' and P.post_status = 'publish' and meta_value = '" . $manufacturer ."' 
ORDER BY P.post_date DESC";
    
    $res = $conn->query($query) or die($conn->error);;
    $dat = array();
    while($ro = $res->fetch_assoc()) {
        
        $watt = getMetaQuery($ro["ID"], 'watt_estimate', $conn);
        $hashRate = getMetaQuery($ro["ID"], 'hash_rate', $conn);
        $amzLink = getAmazon($ro["ID"], 'url', $conn);
        $image = getAmazon($ro["ID"], 'img', $conn);
          
        array_push($dat, array(
            'model' => $ro["post_title"], 
            'image' => $image, 
            'watt' => $watt["meta_value"],
            'hashRate' => $hashRate["meta_value"],
            'link' => $ro["guid"], 
            'amzLink' => $amzLink,
        ));
    }

    return $dat;
}

function getCurrentPrice ($postID, $conn) {
    $arr = $conn->query(createMetaQuery($postID, '_cegg_data_Amazon'))->fetch_assoc()["meta_value"];

    if(empty($arr)) {
        return "";
    }
    
    if(is_serialized($arr) && unserialize($arr)) {
        $arr = unserialize($arr);
        $arr = reset($arr);
        return $arr["price"];
    } else {
        $dat = "Price not available.";
    }
    return $dat;
}

function getAmazon($postID, $tag, $conn) {
    $arr = $conn->query(createMetaQuery($postID, '_cegg_data_Amazon'))->fetch_assoc()["meta_value"];

    if(empty($arr)) {
        return "";
    }

    if(is_serialized($arr) && unserialize($arr)) {
        $arr = unserialize($arr);
        $arr = reset($arr);
        return $arr[$tag];
    } else {
        $dat = $tag . " not available.";
    }
    return $dat;
}

function is_serialized( $data ) {
    // if it isn't a string, it isn't serialized
    if ( !is_string( $data ) )
        return false;
    $data = trim( $data );
    if ( 'N;' == $data )
        return true;
    if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
        return false;
    switch ( $badions[1] ) {
        case 'a' :
        case 'O' :
        case 's' :
            if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
                return true;
            break;
        case 'b' :
        case 'i' :
        case 'd' :
            if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
                return true;
            break;
    }
    return false;
}
