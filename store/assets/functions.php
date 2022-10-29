<?php
/**
 * 
 * 
 * just to store some functions
 * 
 * 
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');

// disable debug output globally
$PPRINT = true;



/**
 * 
 * 
 *---------------------------*
 *         FUNCTIONS         *
 *---------------------------*
 * 
 * 
 */



/**
 * 
 * 
 * read config.json and return as array
 * 
 */
function getConfig() {
  function objectToArray($d) {
    if (is_object($d)) {
      $d = get_object_vars($d);
    }
    if (is_array($d)) {
      return array_map(__FUNCTION__, $d);
    } else {
      return $d;
    }
  }
  //
  if (is_file('assets/config.json')) {
    $conf = (array) json_decode(file_get_contents('assets/config.json'));
  }
  //
  else if (is_file('config.json')) {
    $conf = (array) json_decode(file_get_contents('config.json'));
  }
  //
  else if (is_file('../config.json')) {
    $conf = (array) json_decode(file_get_contents('../config.json'));
  } else {
    pprint(__DIR__);
  }
  return objectToArray($conf);
}


/**
 * 
 * 
 * just a nice output function
 * 
 * 
 */
function pprint($value, $name = '') {
  global $PPRINT;
  if ($PPRINT === false) {
    return;
  }
  echo <<<HTML
  <pre style='
    font-family: Courier, monospace;
    font-size:.9rem;
    color:#000; 
    background:#ccc;
    border:1px solid black;
    border-radius:.5rem;
    margin:.5rem;
    padding:.2rem;
    padding-inline:1rem;
    min-width: fit-content;
    max-width: 700px;
    max-height: 700px;
    overflow: auto;
    '>
  HTML;
  echo "<b>" . $name . "</b> ";
  echo (is_string($value) || is_numeric($value)) ? '' : '<br><br>';
  print_r($value);
  echo "<br>";
  echo "</pre>";
}