<?php

echo "<pre>";
// localhost:9090/store.php?ESP=haus_one&temp_1=10&temp_2=29&hum_1=40
include 'config.php';



// get ESP name from URL and remove it from GET array
$esp = (isset($_GET['ESP'])) ? $_GET['ESP'] : 'haus_one';
unset($_GET['ESP']);
// print_r($_GET);




// delete db file & connect to database
unlink($config['db_file']);
$db = new SQLite3($config['db_file']);

// initialize database
initDB($esp);
// insertValues($esp);

dummyData(21);
dummyData(22);
dummyData(23);
dummyData(24);
dummyData(25);


function dummyData($day) {
  global $db, $esp;
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
      // OUTPUT 
      // echo "Date:" . $h . ":" . $m . "<br>";
      $date = "'2022-10-".$day.' '. $h . ":" . $m . ":00'";
      $temp_1 = "'" . rand(10, 20) . "'";
      $temp_2 = "'" . rand(20, 30) . "'";
      $hum_1 = "'" . rand(50, 60) . "'";
      $hum_2 = "'" . rand(70, 80) . "'";
      // insert into DB
      $statement = $db->prepare("insert into $esp ('temp_1', 'temp_2', 'hum_1', 'hum_2','date')  values ($temp_1, $temp_2, $hum_1, $hum_2,$date)");
      $statement->execute();
      // OUTPUT
    } // $m
  } // $h
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
  $columns = '(' . implode(',', array_keys($_GET)) . ')';
  $values = '(' . implode(',', $_GET) . ')';
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