<?php
/**
 * 
 * file to get values from url and save to database
 * 
 * http://localhost:9090/store/?ESP=EG_1&WZ_TR=10&WZ_HK1_VL=29&WZ_HK1_RL=40
 * 
 * to initialize database: http://localhost:9090/store/?initDB&ESP=EG_1
 * 
 */

include 'assets/functions.php';
$config = getConfig();
// print_r($_GET);
// print_r($config['ESP']);
// exit;


/**
 * 
 * get ESP name from URL or call initDB function
 * 
 */
if (isset($_GET['ESP'])) {
  $esp = $_GET['ESP'];
}

// init database
elseif (isset($_GET['initDB'])) {
  echo '<h2>create new database</h2>';
  initDB();
  exit;
}

// throw error 
else {
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
 * connect to database 
 * 
 */
$db = new PDO('sqlite:assets/' . $config['db_file']);




/**
 * 
 * insert values
 * 
 * http://localhost:9090/store/?ESP=EG_1&WZ_TR=10&WZ_HK1_VL=29&WZ_HK1_RL=40
 * 
 */
unset($_GET['ESP']);
$array = array();
$placeholder = array();

// match GET values to sensors from config
//
foreach ($sensors as $sensor) {

  if (isset($_GET[$sensor['name']])) {

    // assign GET value
    //
    $array[$sensor['name']] = $_GET[$sensor['name']];

    // remove all characters except: 0-9 , .
    //
    $array[$sensor['name']] = preg_replace('/[^0-9.,]/', '', $array[$sensor['name']]);

    // add placeholder for value
    //
    $placeholder[] = '?';
  }
}

// add date to array
//
$array['date'] = date("Y-m-d H:i:s");


// make strings from arrays
//
$columns = '(' . implode(',', array_keys($array)) . ')';
$placeholder = '(' . implode(',', $placeholder) . ',?)';


// pprint($array, '$array');
// pprint($columns, '$columns');
// pprint($placeholder, 'placeholder');
// pprint(array_values($array), 'array_values($array)');


// prepare statement
//
$statement = $db->prepare("INSERT INTO $esp" . $columns . " VALUES" . $placeholder);


// try to execute or catch error
//
try {
  $statement->execute(array_values($array));
  $id = $db->lastInsertId();
  echo "<h2>values inserted in row $id</h2>";
  pprint($array);
} catch (\Throwable $th) {
  throw $th;
}
exit;




/**
 * 
 * initialize database
 * 
 * store/?initDB
 * 
 */
function initDB() {
  global $config;
  $db = new PDO('sqlite:assets/' . $config['db_file']);


  // get all ESP from config for the loop
  //
  $ESPtables = $config['ESP'];
  foreach ($ESPtables as $esp) {


    // create a table for every ESP
    //
    $espName = $esp['name'];
    if ($db->exec("CREATE TABLE IF NOT EXISTS $espName (id INTEGER PRIMARY KEY AUTOINCREMENT)")) {
      echo "<h2>CREATE TABLE IF NOT EXISTS $espName (id INTEGER PRIMARY KEY AUTOINCREMENT)</h2>";
    }


    // create date column
    //
    if ($db->exec("ALTER TABLE $espName ADD COLUMN date DATETIME DEFAULT CURRENT_TIMESTAMP")) {
      echo "<h2>ALTER TABLE $espName ADD COLUMN date DATETIME DEFAULT CURRENT_TIMESTAMP</h2>";
    }


    // create a column for every sensor
    //
    foreach ($esp['sensors'] as $sensor) {
      // pprint($sensor);
      $sensorName = $sensor['name'];
      if ($db->exec("ALTER TABLE $espName ADD COLUMN $sensorName TEXT ")) {
        echo "<h2>ALTER TABLE $espName ADD COLUMN $sensorName TEXT </h2>";
      }

    }
    echo "<h2>database table for $espName initialized</h2>";

  }
  exit;
}



//////////////////////////////////  FUNCTIONS  //////////////////////////////////

// // create table
// //
// if ($db->exec("CREATE TABLE IF NOT EXISTS $esp(id INTEGER PRIMARY KEY AUTOINCREMENT)")) {
//   echo "<h2>CREATE TABLE IF NOT EXISTS $esp(id INTEGER PRIMARY KEY AUTOINCREMENT)</h2>";
// }

// // create date column
// //
// if ($db->exec("ALTER TABLE $esp ADD COLUMN date DATETIME DEFAULT CURRENT_TIMESTAMP")) {
//  echo "<h2>ALTER TABLE $esp ADD COLUMN date DATETIME DEFAULT CURRENT_TIMESTAMP</h2>";
// }

// // create a column for every sensor
// //
// foreach ($sensors as $sensor) {
//   $sensorName = $sensor['name'];
//   // if ($db->exec("ALTER TABLE $esp ADD COLUMN $sensorName INTEGER NOT NULL DEFAULT '0' ")) {
//  if ($db->exec("ALTER TABLE $esp ADD COLUMN $sensorName TEXT ")) {
//    echo "<h2>ALTER TABLE $esp ADD COLUMN $sensorName TEXT </h2>";
//   }
// }
// echo "<h2>Database initialized</h2>";
// exit;


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