<?php
/**
 * 
 * file to fetch data from the database by url parameter
 * 
 * ESP = table
 * startDate, endDate: 2022-10-27
 * 
 */
include 'functions.php';
$config = getConfig();

// $sensors = array();
// $startDate = '';
// $endDate = '';
// $esp = '';

// send config array as JSON
//
if (isset($_GET['config'])) {
  echo json_encode($config['ESP']);
  exit;
}


// connect to database
//
$db = new SQLite3($config['db_file']);



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

  // get sensorlist from config
  //
  $sensors = $config['ESP'][$esp]['sensors'];

  // fetch values from database
  //
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
  $res = ['labels' => $labels, 'datasets' => $datasets];



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

//echo '<b>Total Execution Time:</b> ' . round(microtime(true) - $time_start, 2) . 's ';







//////////////////////////////////  FUNCTIONS  //////////////////////////////////






// function output() {
//   global $sensors, $db, $startDate, $endDate, $esp;
//   // fetch data from DB
//   $stmt = $db->prepare("SELECT * FROM $esp WHERE strftime('%Y-%m-%d %H:%M:S', date) BETWEEN :startDate AND :endDate");
//   $stmt->bindValue('startDate', $startDate, SQLITE3_TEXT);
//   $stmt->bindValue('endDate', $endDate, SQLITE3_TEXT);
//   $results = $stmt->execute();

//   $labels = [];
//   $datasets = [];
//   while ($row = $results->fetchArray()) {
//     array_push($labels, $row['date']);
//   }
//   foreach ($sensors as $key => $value) {
//     $datasets[$key]['label'] = $value['label'];
//     $datasets[$key]['yAxisID'] = $value['yAxisID'];
//     $datasets[$key]['unit'] = $value['unit'];
//     $datasets[$key]['backgroundColor'] = $value['backgroundColor'];
//     $datasets[$key]['borderColor'] = $value['borderColor'];
//     while ($row = $results->fetchArray()) {
//       $datasets[$key]['data'][] = $row[$value['name']];
//     }
//   }
//   return ['labels' => $labels, 'datasets' => $datasets];
// } // function output