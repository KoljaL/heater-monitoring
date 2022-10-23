<?php

echo "<pre>";
// localhost:9090/store.php?ESP=haus_one&temp_1=10&temp_2=29&hum_1=40
include 'config.php';



// get ESP name from URL and remove it from GET array
$esp = ($_GET['ESP']) ? $_GET['ESP'] : 'haus_one';
unset($_GET['ESP']);
// print_r($_GET);




// delete db file & connect to database
// unlink($config['db_file']);
$db = new SQLite3($config['db_file']);

// initialize database
// initDB($esp);
insertValues($esp);

fetchValues($esp);

/**
 * fetchValues($esp)
 * @param string $esp
 * @return void
 */
function fetchValues($esp)
{
    global $db;
    $res = $db->query("SELECT * FROM $esp");
    $result = [];
    while ($row = $res->fetchArray()) {
      $result[] = $row;
        // echo "{$row[0]} {$row[1]} {$row[2]}\n";
    }
    print_r($result);

    // return $result;

}


/**
 * insertValues($esp)
 * @param string $esp
 * @return void
 */
function insertValues($esp)
{
    global $db;
    // get all keys and values from URL as two strings
    $columns = '('.implode(',', array_keys($_GET)).')';
    $values = '('.implode(',', $_GET).')';
    // prepare and execute statement
    $statement = $db->prepare("insert into $esp" .$columns ." values" .$values);
    $statement->execute();
}









// print_r($esp);
// print_r($sensors);
// print_r($config);




//////////////////////////////////  FUNCTIONS  //////////////////////////////////


/**
 *
 * initDB()
 *
 * @param string $esp
 * @param array $sensors
 */
function initDB($esp)
{
    global $db,$config;

    // get all sensors for this ESP from database
    $sensors = $config['ESP'][$esp]['sensors'];

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