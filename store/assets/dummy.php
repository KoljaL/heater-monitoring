<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// date_default_timezone_set('Europe/Berlin');

// echo "<pre>";
include '../functions.php';
$config = getConfig();


////////// START

// days before and after today
$createDays = 5;
// create values for every x minutes
$minuteIntervall = 15;

removeDB();
// create new database
$db = new SQLite3($config['db_file']);

$esp = 'EG_1';
initDB();
createDummyData($createDays);

$esp = 'EG_2';
initDB();
createDummyData($createDays);

exit;
////////////////////////////////// FUNCTIONS //////////////////////////////////



/**
 * 
 * remove database
 * 
 */
function removeDB() {
  global $config, $db;
  unlink($config['db_file']);
  echo "<h2>Database removed</h2>";
}


/**
 * 
 * initialize database
 * 
 */
function initDB() {
  global $db, $config, $esp;
  $sensors = $config['ESP'][$esp]['sensors'];
  $db->exec("CREATE TABLE IF NOT EXISTS $esp(id INTEGER PRIMARY KEY AUTOINCREMENT)");
  $db->exec("ALTER TABLE $esp ADD COLUMN date DATETIME DEFAULT '0' ");
  foreach ($sensors as $sensor) {
    $sensorName = $sensor['name'];
    $db->exec("ALTER TABLE $esp ADD COLUMN $sensorName TEXT ");
  }
  echo "<h2>Database for $esp initialized</h2>";
}






/**
 * 
 * insert dummy data
 * 
 * 
 */
function createDummyData($days) {
  global $db, $esp, $config, $minuteIntervall;
  $sensors = $config['ESP'][$esp]['sensors'];

  // offset counter
  $j = 1;
  // sinus value
  $sin = 0;


  // days
  for ($d = -$days; $d < $days + 1; $d++) {
    $date = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + $d, date("Y")));
    // row counter
    $i = 0;

    // hours
    for ($h = 0; $h < 24; $h++) {
      $h = ($h < 10) ? "0" . $h : $h;

      // minutes
      for ($m = 0; $m < 60; $m += $minuteIntervall) {
        $m = ($m < 10) ? "0" . $m : $m;



        // missing values for EG_2
        if ($esp === 'EG_2') {
          // // randomly add no value
          if (rand(1, 10) > 8) {
            continue;
          }

          // // no values in hours
          // if ($h === 12 || $h === 18) {
          //   continue;
          // }
        }



        // date
        $datetime = "'" . $date . ' ' . $h . ":" . $m . ":00'";

        // sinus runner
        $sin = ($sin < 100) ? $sin -= 1 : $sin += 1;

        /////// RANDOM KEYS /////
        // values
        // $valuesArray = [
        //   round($j * .01 + sin($sin / 100) * 10, 2),
        //   round(25 + cos($sin / 100) * 20, 2),
        //   round(50 + sin($sin / 2) * 20, 2),
        //   round(25 + sin($sin / 5) * 10, 2),
        //   round(90 - $j * .1 + cos($sin / 100) * 10, 2),
        //   round(25 + sin($sin / 100) * 20, 2),
        //   round(40 + sin($sin / 20) * 20, 2),
        //   round(45 + sin($sin / 15) * 10, 2),
        // ];

        // $dataArray = [];
        // $v = 0;
        // foreach ($sensors as $sensor) {
        //   $dataArray[$sensor['name']] = $valuesArray[$v];
        //   $v = ($v >= count($valuesArray) - 1) ? 0 : $v += 1;
        // }

        $dataArray = [
          "WZ_TR" => round(17 + sin($sin / 2) * 2, 2),
          "WZ_HK1_VL" => round(50 + cos($sin / 2) * 2, 2),
          "WZ_HK1_RL" => round(40 + sin($sin / 2) * 2, 2),
          "WZ_HK2_VL" => round(45 + cos($sin / 3) * 2, 2),
          "WZ_HK2_RL" => round(35 + sin($sin / 3) * 2, 2)
        ];



        // pprint($dataArray);

        // remove random key value from $array
        if ($esp === 'EG_2') {
          $keyToUnlink = array_keys($dataArray)[rand(0, 1)];
          unset($dataArray[$keyToUnlink]);
        }

        // make strings from array
        $columns = '(' . implode(',', array_keys($dataArray)) . ',date)';
        $values = "(" . implode(",", $dataArray) . "," . $datetime . ")";
        // insert into DB
        $statement = $db->prepare("INSERT INTO $esp" . $columns . " VALUES" . $values);
        $statement->execute();

        // row counter
        $i++;
        // offset counter
        $j++;

      } // $m
    } // $h
    echo "insert $i rows for $date<br>";
  } //$d
}