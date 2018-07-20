<?php
require "vendor/autoload.php";

Use eftec\bladeone;
use DaveChild\TextStatistics as TS;
use Noodlehaus\Config;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
define("BLADEONE_MODE", 2); // (optional) 1=forced (test),2=run fast (production), 0=automatic, default value.
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
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully";

$sql = "SELECT * FROM wp_ticker Limit 5;";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Price: " . $row["price"]. " " . $row["market_cap"]. "<br>";
        //echo $row;
    }
} else {
    echo "0 results";
}

/*
foreach ($csv as $key => $value) {
    $output .= $blade->run("hello",$csv[$key]);
    $output .= "######################### \n";
    echo $output;
    echo 'Flesch-Kincaid Reading Ease: ' . $textStatistics->fleschKincaidReadingEase($output);
}

file_put_contents("./GPU_CONTENT_OUTPUT.txt", $output);

// echo $blade->run("hello",array("variable1"=>"value1"));
*/

// close mysql connection
$conn->close();
