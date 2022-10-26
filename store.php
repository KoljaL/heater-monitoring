<?php

echo "<pre>";
// localhost:9090/store.php?ESP=haus_one&temp_1=10&temp_2=29&hum_1=40
include 'config.php';
date_default_timezone_set('Europe/Berlin');

// connect to database
$db = new SQLite3($config['db_file']);

// print_r($_GET);
// print_r($config['ESP']);


// exit;


/**
 * 
 * remove database
 * 
 * store.php?removeDB
 * 
 */
if (isset($_GET['removeDB'])) {
  unlink($config['db_file']);
  echo "Database removed<br>";
}


/**
 * 
 * initialize database
 * 
 * store.php?initDB
 * 
 */
if (isset($_GET['initDB'])) {
  $esp = (isset($_GET['ESP'])) ? $_GET['ESP'] : key($config['ESP']);
  unset($_GET['initDB']);

  $db = new SQLite3($config['db_file']);
  initDB($esp);
  echo "Database initialized<br>";
}

/**
 *
 * insert dummy values
 *
 * store.php?dummyValue
 *
 */
if (isset($_GET['dummyValue'])) {
  $esp = (isset($_GET['ESP'])) ? $_GET['ESP'] : key($config['ESP']);
  $_GET['ESP'] = $esp;
  unset($_GET['dummyValue']);
  $sensors = $config['ESP'][$esp]['sensors'];
  foreach ($sensors as $sensor) {
    $AxisID = $sensor['yAxisID'];
    $min = $config['ESP'][$esp]['axis'][$AxisID]['min'];
    $max = $config['ESP'][$esp]['axis'][$AxisID]['max'];
    $name = $sensor['name'];
    $_GET[$name] = rand($min, $max);
  }
  print_r($_GET);
}


/**
 * 
 * insert values
 * 
 * store.php?ESP=haus_one&temp_1=10&temp_2=29&hum_1=40
 * 
 */
if (isset($_GET['saveValues'])) {
  $esp = $_GET['ESP'];
  unset($_GET['ESP']);
  insertValues($esp);
  echo "insert values<br>";
}


/**
 * 
 * insert dummy values
 * 
 * store.php?dummyData
 * 
 */
if (isset($_GET['dummyData'])) {
  $esp = (isset($_GET['ESP'])) ? $_GET['ESP'] : key($config['ESP']);
  $datem2 = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 2, date("Y")));
  $datem1 = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
  $date0 = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
  $datep1 = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")));
  $datep2 = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 2, date("Y")));

  dummyData($datem2);
  dummyData($datem1);
  dummyData($date0);
  dummyData($datep1);
  dummyData($datep2);
  echo "insert dummy values<br>";
}




function dummyData($day) {
  global $db, $esp;
  $i = 0;
  // hours
  for ($h = 0; $h < 24; $h++) {
    if ($h < 10) {
      $h = "0" . $h;
    }
    // minutes
    for ($m = 0; $m < 60; $m += 5) {
      if ($m < 10) {
        $m = "0" . $m;
      }

      // if (rand(1, 5) > 3) {
      //   // echo "'2022-10-" . $day . ' ' . $h . ":" . $m . ":00'<br>";
      //   continue;
      // }

      if ($h === 12 || $h === 18) {
        continue;
      }


      // OUTPUT 
      $date = "'" . $day . ' ' . $h . ":" . $m . ":00'";

      if($esp === 'haus_one'){
        $temp_1 = "'" . rand(10, 20) . "'";
        $temp_2 = "'" . rand(20, 30) . "'";
        $hum_1 = "'" . rand(50, 60) . "'";
        $hum_2 = "'" . rand(70, 80) . "'";
      }else{
        $temp_1 = "'" . rand(20, 30) . "'";
        $temp_2 = "'" . rand(40, 60) . "'";
        $hum_1 = "'" . rand(40, 60) . "'";
        $hum_2 = "'" . rand(30, 80) . "'";   
      }
      
      
      // insert into DB
      $statement = $db->prepare("insert into $esp ('temp_1', 'temp_2', 'hum_1', 'hum_2','date')  values ($temp_1, $temp_2, $hum_1, $hum_2,$date)");
      $statement->execute();
      $i++;
      // OUTPUT
    } // $m
  } // $h
  echo "insert $i rows for $day<br>";
}


//////////////////////////////////  FUNCTIONS  //////////////////////////////////




/**
 * insertValues($esp)
 * @param string $esp
 * @return void
 */
function insertValues($esp) {
  global $db;
  // get all keys and values from URL as two strings
  $date = date("Y-m-d H:i:s");
  $columns = '(' . implode(',', array_keys($_GET)) . ',date)';
  $values = "('" . implode("','", $_GET) . "','" . $date . "')";

  // echo $columns . "<br>";
  // echo $values . "<br>";

  // prepare and execute statement
  $statement = $db->prepare("insert into $esp" . $columns . " values" . $values);
  $statement->execute();
}




/**
 *
 * initDB()
 *
 * @param string $esp
 * @param array $sensors
 */
function initDB($esp) {
  global $db, $config;

  // get all sensors for this ESP from database
  $sensors = $config['ESP'][$esp]['sensors'];
  // print_r($sensors);

  $db->exec("CREATE TABLE IF NOT EXISTS  $esp(id INTEGER PRIMARY KEY AUTOINCREMENT)");
  $db->exec("ALTER TABLE  $esp ADD COLUMN date DATETIME DEFAULT CURRENT_TIMESTAMP ");
  foreach ($sensors as $sensor) {
    $sensorName = $sensor['name'];
    $db->exec("ALTER TABLE  $esp ADD COLUMN $sensorName INTEGER NOT NULL DEFAULT '0' ");
  }
}






////////////// OLD






// echo key($config['ESP']);

// $date = strtolower(date("Y-m-d", strtotime(date('d-m-Y'))));
// echo $date;

// $tomorrow = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));

// $date = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")));
// echo $date;



// $esp = (isset($_GET['ESP'])) ? $_GET['ESP'] : key($config['ESP']);

// $stm = $db->prepare("INSERT INTO $esp(temp_1, temp_2) VALUES (?, ?)");
// $stm->bindParam(1, $firstName);
// $stm->bindParam(2, $lastName);
// $firstName = 'Lucy';
// $lastName = 'Brown';
// $stm->execute();







// fetchValues($esp);
// /**
//  * fetchValues($esp)
//  * @param string $esp
//  * @return void
//  */
// function fetchValues($esp)
// {
//     global $db;

//     $stmt = $db->prepare("SELECT * FROM $esp WHERE strftime('%Y-%m-%d %H:%M:S', date) BETWEEN  :startDate AND :endDate");
//     $stmt->bindValue('startDate', '2022-10-23 08:00:15', SQLITE3_TEXT);
//     $stmt->bindValue('endDate', '2022-10-23 09:00:15', SQLITE3_TEXT);


//     $res = $stmt->execute();

//     while ($row = $res->fetchArray()) {
//         echo "{$row['id']} {$row['date']} {$row['temp_2']} \n";
//     }
// }