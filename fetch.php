<?php
// error_reporting(E_ALL ^ E_WARNING);
// localhost:9090/store.php?ESP=haus_one&temp_1=10&temp_2=29&hum_1=40


include 'config.php';
// connect to database
$db = new SQLite3($config['db_file']);

// set date format
date_default_timezone_set('Europe/Berlin');
$now = new DateTimeImmutable();




if (isset($_GET['ESP'])) {
  $esp = $_GET['ESP'];
  $startDate = (isset($_GET['startDate'])) ? $_GET['startDate'] . ' 00:00:00' : date('Y-m-d', time()) . ' 00:00:00';
  $endDate = (isset($_GET['endDate'])) ? $_GET['endDate'] . ' 23:59:59' : $now->format('Y-m-d H:i:s');

  $res = fetchValues($esp, $startDate, $endDate);
  //print_r($res);
  echo json_encode($res);
  exit;
}


if (isset($_GET['config'])) {
  echo json_encode($config['ESP']);
}








//////////////////////////////////  FUNCTIONS  //////////////////////////////////



/**
 * fetchValues($esp)
 * @param string $esp
 * @return array
 */
function fetchValues($esp, $startDate, $endDate) {
  global $db, $config;
  // get current sensors
  $sensors = $config['ESP'][$esp]['sensors'];
  // fetch data from DB
  $stmt = $db->prepare("SELECT * FROM $esp WHERE strftime('%Y-%m-%d %H:%M:S', date) BETWEEN  :startDate AND :endDate");
  $stmt->bindValue('startDate', $startDate, SQLITE3_TEXT);
  $stmt->bindValue('endDate', $endDate, SQLITE3_TEXT);
  $results = $stmt->execute();

  // prepare output
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