<?php
require "vendor/autoload.php";

Use eftec\bladeone;
use DaveChild\TextStatistics as TS;
use Noodlehaus\Config;

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
        ORDER BY wp_posts.post_date 
        Limit 10;
        DESC;";

$data = array();
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        array_push($data, $row);
        //echo "id: " . $row["id"]. " - Price: " . $row["price"]. " " . $row["market_cap"]. "<br>";
        //echo $row;
    }
} else {
    echo "0 results";
}

echo "lolonator";
print_r($data[0]);

foreach ($data as $key => $value) {
    print_r($data[$key]);
    $output .= $blade->run("gpuContent", $data[$key]);
    $output .= "######################### \n";
    echo $output;
    echo 'Flesch-Kincaid Reading Ease: ' . $textStatistics->fleschKincaidReadingEase($output) . "\n";
}

file_put_contents("./GPU_CONTENT_OUTPUT.txt", $output);

// close mysql connection
$conn->close();
