<?php
echo "<pre>";

include 'config.php';




$esp = 'haus_one';
$sensors = $config['ESP'][$esp]['sensors'];

// delete db file
unlink($config['db_file']);
$db = new SQLite3($config['db_file']);


initDB($esp, $sensors);


$stm = $db->prepare("INSERT INTO $esp(temp_1, temp_1) VALUES (?, ?)");
$stm->bindParam(1, $firstName);
$stm->bindParam(2, $lastName);

$firstName = 'Peter';
$lastName = 'Novak';
$stm->execute();

$firstName = 'Lucy';
$lastName = 'Brown';
$stm->execute();

$res = $db->query("SELECT * FROM $esp");

while ($row = $res->fetchArray()) {
    echo "{$row[0]} {$row[1]} {$row[2]}\n";
}





print_r($esp);
print_r($sensors);
print_r($config);




//////////////////////////////////  FUNCTIONS  //////////////////////////////////


/**
 * 
 * initDB()
 * 
 * @param string $esp
 * @param array $sensors
 */
function initDB($esp, $sensors){
  global $db;
  $db->exec("CREATE TABLE IF NOT EXISTS  $esp(id INTEGER PRIMARY KEY AUTOINCREMENT)");  
  $db->exec("ALTER TABLE  $esp ADD COLUMN date DATETIME DEFAULT CURRENT_TIMESTAMP ");
  foreach ($sensors as $sensor) {
    $sensorName = $sensor['name'];
    $db->exec("ALTER TABLE  $esp ADD COLUMN $sensorName INTEGER NOT NULL DEFAULT '0' ");
  }
}






 