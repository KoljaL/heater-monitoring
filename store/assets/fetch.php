<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// localhost:9090/store.php?ESP=haus_one&temp_1=10&temp_2=29&hum_1=40
// $time_start = microtime(true);


include '../config.php';
include 'interpol.php';

$sensors = array();
$startDate = '';
$endDate = '';
$esp = '';

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
  // $res = fetchValues($esp, $startDate, $endDate);
  //print_r($res);
  $sensors = $config['ESP'][$esp]['sensors'];


  if ($esp === 'haus_one') {
    $res = interpolate();
  } else {
    $res = output();
  }

  //echo '<b>Total Execution Time:</b> ' . round(microtime(true) - $time_start, 2) . 's ';


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
 *
 * INTERPOLATE
 *
 */
function interpolate() {
  global $sensors, $db, $startDate, $endDate, $esp;
  // pprint($db, '$db');

  // fetch data from DB
  $stmt = $db->prepare("SELECT * FROM $esp WHERE strftime('%Y-%m-%d %H:%M:S', date) BETWEEN :startDate AND :endDate");
  $stmt->bindValue('startDate', $startDate, SQLITE3_TEXT);
  $stmt->bindValue('endDate', $endDate, SQLITE3_TEXT);
  $results = $stmt->execute();


  $data = [];
  while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $date = $row['date'];
    unset($row['date']);
    $data[$date] = $row;
  }

  /**
   *
   * filling missing values
   *
   */
  foreach ($data as $key => $time) {
    // if ($key === 0) { continue; }
    foreach ($time as $k => $v) {
      if ($v === NULL) {
        $data[$key][$k] = isset($timeStore[$k]) ? $timeStore[$k] : 0;
      }
    }
    $timeStore = $data[$key];
  }
  // pprint($data, '$data');



  /**
   *
   * get array properties
   *
   */
  $countArray = count($data);
  $firstDate = array_keys($data)[1];
  $lastDate = array_keys($data)[$countArray - 1];
  // pprint($countArray, '$countArray');
  // pprint($firstDate, '$firstDate');
  // pprint($lastDate, '$lastDate');
  // pprint($data, '$data');


  /**
   *
   * create DatePeriod & resampleArray
   *
   */
  $firstDate = new DateTime($firstDate);
  $lastDate = new DateTime($lastDate);
  $DatePeriod = new DatePeriod($firstDate, new DateInterval('PT1M'), $lastDate);
  // pprint($range, '$range');
  $resampleArray = array();
  foreach ($DatePeriod as $dt) {
    $resampleArray[] = $dt->format('Y-m-d H:i:s');
  }
  // pprint($resampleArray, '$resampleArray');



  /**
   *
   * start Interpolation
   *
   */
  $vInterpolation = new LnInterpolation($data);
  // pprint($vInterpolation, '$vInterpolation');
  $outputArray = array();
  foreach ($resampleArray as $value) {
    $outputArray[$value] = $vInterpolation->calculate($value);
  }
  // $countOutput = count($outputArray);
  // pprint($countOutput, '$countOutput');
  // pprint($outputArray, '$outputArray');



  /**
   * 
   * combine labels wit data
   *
   */
  $labels = array_keys($outputArray);
  // pprint($labels, '$labels');
  $datasets = [];

  foreach ($sensors as $key => $value) {
    $datasets[$key]['label'] = $value['label'];
    $datasets[$key]['name'] = $value['name'];
    $datasets[$key]['yAxisID'] = $value['yAxisID'];
    $datasets[$key]['unit'] = $value['unit'];
    $datasets[$key]['backgroundColor'] = $value['backgroundColor'];
    $datasets[$key]['borderColor'] = $value['borderColor'];

    foreach ($outputArray as $k => $v) {
      // pprint($v[$value['name']]);
      $datasets[$key]['data'][] = $v[$value['name']];
    }
  }
  // pprint($datasets, '$datasets');
  return ['labels' => $labels, 'datasets' => $datasets];
} // function interpolate





/**
 * 
 * OUTPUT
 * 
 */
function output() {
  global $sensors, $db, $startDate, $endDate, $esp;
  // fetch data from DB
  $stmt = $db->prepare("SELECT * FROM $esp WHERE strftime('%Y-%m-%d %H:%M:S', date) BETWEEN :startDate AND :endDate");
  $stmt->bindValue('startDate', $startDate, SQLITE3_TEXT);
  $stmt->bindValue('endDate', $endDate, SQLITE3_TEXT);
  $results = $stmt->execute();

  $labels = [];
  $datasets = [];
  while ($row = $results->fetchArray()) {
    array_push($labels, $row['date']);
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
} // function output