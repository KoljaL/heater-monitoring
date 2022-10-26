<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// localhost:9090/store.php?ESP=haus_one&temp_1=10&temp_2=29&hum_1=40


include 'config.php';


// send config array as JSON
//
if (isset($_GET['config'])) {
  echo json_encode($config['ESP']);
  exit;
}

// connect to database
$db = new SQLite3($config['db_file']);

// set date format
date_default_timezone_set('Europe/Berlin');
// $now = new DateTimeImmutable();
// $now->format('Y-m-d H:i:s');


// check if ESP name is set in URL
//
if (isset($_GET['ESP'])) {
  $esp = $_GET['ESP'];

  // check if ESP name exists in config
  //
  if (!isset($config['ESP'][$esp])) {
    echo '<h2>ESP name not in config.php</h2>';
    exit;
  }

  // get dates from URL or use today as default
  //
  $startDate = (isset($_GET['startDate'])) ? $_GET['startDate'] . ' 00:00:00' : date('Y-m-d', time()) . ' 00:00:00';
  $endDate = (isset($_GET['endDate'])) ? $_GET['endDate'] . ' 23:59:59' : date('Y-m-d', time()) . ' 23:59:59';
 
  // fetch values from database
  //
  $res = fetchValues($esp, $startDate, $endDate);
  //print_r($res);

  // send result as JSON
  //
  echo json_encode($res);
  exit;
} 

// error if there is no ESP in URL
//
else {
  echo '<h2>no ESP name in URL</h2>';
  exit;
}








//////////////////////////////////  FUNCTIONS  //////////////////////////////////



/**
 * fetchValues($esp)
 * @param string $esp
 * @return array
 */
function fetchValues($esp, $startDate, $endDate) {
  global $db, $config;
  
  // get sensors for this ESP
  //
  $sensors = $config['ESP'][$esp]['sensors'];
  
  // fetch data from DB
  //
  $stmt = $db->prepare("SELECT * FROM $esp WHERE strftime('%Y-%m-%d %H:%M:S', date) BETWEEN  :startDate AND :endDate");
  $stmt->bindValue('startDate', $startDate, SQLITE3_TEXT);
  $stmt->bindValue('endDate', $endDate, SQLITE3_TEXT);
  $results = $stmt->execute();

  // prepare output
  //
  $labels = [];
  $datasets = [];

  while ($row = $results->fetchArray()) {
    array_push($labels, $row['date']);
    // array_push($labels, strtotime($row['date']));
  }

  foreach ($sensors as $key => $value) {
    $datasets[$key]['label'] = $value['label'];
    $datasets[$key]['yAxisID'] = $value['yAxisID'];
    $datasets[$key]['unit'] = $value['unit'];
    $datasets[$key]['backgroundColor'] = $value['backgroundColor'];
    $datasets[$key]['borderColor'] = $value['borderColor'];

    while ($row = $results->fetchArray()) {
      $datasets[$key]['data'][] = $row[$value['name']];
    }
  }


  return ['labels' => $labels, 'datasets' => $datasets];
}