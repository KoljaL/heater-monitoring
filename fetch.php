<?php

// localhost:9090/store.php?ESP=haus_one&temp_1=10&temp_2=29&hum_1=40
include 'config.php';

// error_reporting(E_ALL ^ E_WARNING);


date_default_timezone_set('Europe/Berlin');
$now = new DateTimeImmutable();


// get ESP name from URL and remove it from GET array
$esp = (isset($_GET['ESP'])) ? $_GET['ESP'] : 'haus_one';
$startDate = (isset($_GET['startDate'])) ? $_GET['startDate']. ' 00:00:00' : date('Y-m-d', time()).' 00:00:00';
$endDate = (isset($_GET['endDate'])) ? $_GET['endDate']. ' 23:59:59' : $now->format('Y-m-d H:i:s');


// echo $startDate;
// echo "<br>";
// echo $endDate;

// connect to database
$db = new SQLite3($config['db_file']);




$res = fetchValues($esp, $startDate, $endDate);
// print_r($res);

echo json_encode($res);

// print_r($JSON);


// exit;



//////////////////////////////////  FUNCTIONS  //////////////////////////////////



/**
 * fetchValues($esp)
 * @param string $esp
 * @return array
 */
function fetchValues($esp, $startDate, $endDate)
{
    global $db,$config;
    // get current sensors
    $sensors = $config['ESP'][$esp]['sensors'];
    // fetch data from DB
    $stmt = $db->prepare("SELECT * FROM $esp WHERE strftime('%Y-%m-%d %H:%M:S', date) BETWEEN  :startDate AND :endDate");
    $stmt->bindValue('startDate', $startDate, SQLITE3_TEXT);
    $stmt->bindValue('endDate', $endDate, SQLITE3_TEXT);
    $results = $stmt->execute();

    // prepare output
    $categories = [];
    $series = [];
    while ($row = $results->fetchArray()) {
        // categories are dates
        array_push($categories,$row['date']);
        // loop over every sensor from $config
        foreach ($sensors as $key => $value) {
          $series[$key]['name']= $value['title'];
          $series[$key]['data'][]= $row[$value['name']];
        }
    }
    $res = ['categories' => $categories, 'series' => $series];
    return $res;
}