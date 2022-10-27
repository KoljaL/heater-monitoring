<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
echo "<pre>";
// localhost:9090/store.php?ESP=haus_one&temp_1=10&temp_2=29&hum_1=40
include '../config.php';
date_default_timezone_set('Europe/Berlin');



// print_r($_GET);
// print_r($config['ESP']);


// exit;


/**
 * 
 * get ESP name from URL
 * 
 */
if (isset($_GET['ESP'])) {
  $esp = $_GET['ESP'];
} else {
  echo '<h2>no ESP name in URL</h2>';
  exit;
}



/**
 * 
 * get all sensors for this ESP from config file
 * 
 */
if (isset($config['ESP'][$esp]['sensors'])) {
  $sensors = $config['ESP'][$esp]['sensors'];
  // print_r($sensors);
} else {
  echo '<h2>ESP name not in config.php</h2>';
  exit;
}



/**
 * 
 * create database file
 * 
 */
// $db = new SQLite3($config['db_file']);
$db = new PDO('sqlite:'.$config['db_file']);


/**
 * 
 * initialize database
 * 
 * store/?initDB
 * 
 */
if (isset($_GET['initDB'])) {

  // create table
  //
  if ($db->exec("CREATE TABLE IF NOT EXISTS $esp(id INTEGER PRIMARY KEY AUTOINCREMENT)")) {
    echo "<h2>CREATE TABLE IF NOT EXISTS $esp(id INTEGER PRIMARY KEY AUTOINCREMENT)</h2>";
  }

  // create date column
  //
  if ($db->exec("ALTER TABLE $esp ADD COLUMN date DATETIME DEFAULT CURRENT_TIMESTAMP")) {
    echo "<h2>ALTER TABLE $esp ADD COLUMN date DATETIME DEFAULT CURRENT_TIMESTAMP</h2>";
  }

  // create a column for every sensor
  //
  foreach ($sensors as $sensor) {
    $sensorName = $sensor['name'];
    // if ($db->exec("ALTER TABLE $esp ADD COLUMN $sensorName INTEGER NOT NULL DEFAULT '0' ")) {
    if($db->exec("ALTER TABLE $esp ADD COLUMN $sensorName TEXT ")) {
      echo "<h2>ALTER TABLE $esp ADD COLUMN $sensorName TEXT </h2>";
    }
  }
  echo "<h2>Database initialized</h2>";
  exit;
}


/**
 * 
 * insert values
 * 
 * store/?saveValues&ESP=haus_one&temp_1=10&temp_2=29&hum_1=40
 * 
 */
if (isset($_GET['saveValues'])) {
  unset($_GET['ESP']);
  $array = array();
  $placeholder = array();
  $date = date("Y-m-d H:i:s");

  // match GET values to sensors
  //
  foreach ($sensors as $sensor) {

    // assign GET value or 0
    //
    $array[$sensor['name']] = $_GET[$sensor['name']] ?? 0;

    // remove all characters except: 0-9 , .
    //
    $array[$sensor['name']] = preg_replace('/[^0-9.,]/', '', $array[$sensor['name']]);

    // add placeholder for value
    //
    $placeholder[] = '?';
  }

  // print_r($array);

  // make strings from arrays
  //
  $columns = '(' . implode(',', array_keys($array)) . ',date)';
  $placeholder = '(' . implode(',', $placeholder) . ',?)';
  // $values = "('" . implode("','", $array) . "','" . $date . "')";

  // echo $columns . "<br>" . $values . "<br>" . $placeholder . "<br>";


  // prepare statement
  //
  $statement = $db->prepare("INSERT INTO $esp" . $columns . " VALUES" . $placeholder);

  // try to execute or catch error
  try {
    $statement->execute(array_values($array));
    $id = $db->lastInsertId();
    echo "<h2>values inserted in row $id</h2>";
    print_r($array);
  } catch (\Throwable $th) {
    throw $th;
  }
  exit;
}



//////////////////////////////////  FUNCTIONS  //////////////////////////////////




// /**
//  * insertValues($esp)
//  * @param string $esp
//  * @return void
//  */
// function insertValues($esp) {
//   global $db;
//   // get all keys and values from URL as two strings
//   $date = date("Y-m-d H:i:s");
//   $columns = '(' . implode(',', array_keys($_GET)) . ',date)';
//   $values = "('" . implode("','", $_GET) . "','" . $date . "')";

//   // echo $columns . "<br>";
//   // echo $values . "<br>";

//   // prepare and execute statement
//   $statement = $db->prepare("insert into $esp" . $columns . " values" . $values);
//   $statement->execute();
// }




// /**
//  *
//  * initDB()
//  *
//  * @param string $esp
//  * @param array $sensors
//  */
// function initDB($esp) {
//   global $db, $config;

//   // get all sensors for this ESP from database
//   $sensors = $config['ESP'][$esp]['sensors'];
//   // print_r($sensors);

//   $db->exec("CREATE TABLE IF NOT EXISTS  $esp(id INTEGER PRIMARY KEY AUTOINCREMENT)");
//   $db->exec("ALTER TABLE  $esp ADD COLUMN date DATETIME DEFAULT CURRENT_TIMESTAMP ");
//   foreach ($sensors as $sensor) {
//     $sensorName = $sensor['name'];
//     $db->exec("ALTER TABLE  $esp ADD COLUMN $sensorName INTEGER NOT NULL DEFAULT '0' ");
//   }
// }






////////////// OLD


// prepare and execute statement
// $statement = $db->prepare("insert into $esp" . $columns . " values" . $values);
// $statement->execute();



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