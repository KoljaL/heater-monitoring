<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$time_start = microtime(true);
date_default_timezone_set('Europe/Berlin');
echo "<pre>";


/**
 * 
 * Summary of pprint
 * 
 * @param mixed $value
 * @param string $name
 * @return void
 *
 */
function pprint($value, $name = '') {
  echo "<pre style='margin:.5rem;padding:.2rem;padding-inline:1rem;background:#ccc;border:1px solid black;border-radius:.5rem;color:#000; width:max-content;font-family: Courier, monospace;font-size:.9rem;'>";
  echo "<b>" . $name . "</b> ";
  echo (is_string($value) || is_numeric($value)) ? '' : '<br><br>';
  print_r($value);
  echo "<br>";
  echo "</pre>";
}

/**
 * 
 * https://github.com/osimosu/linear-interpolation
 * 
 */
class LnInterpolation {
  private $pDatas;
  public function __construct($dDatas) {
    $this->pDatas = $dDatas;
  }

  // get previous and next element in array
  public function find($input) {
    $prev_key = null;
    $next_key = null;
    foreach (array_keys($this->pDatas) as $key) {
      // convert to timestamp for comparison
      if (strtotime($key) < strtotime($input)) {
        $prev_key = $key;
      } else {
        //  already found next key, move on
        if ($next_key == null) {
          $next_key = $key;
        }
      }
    }
    return array($prev_key, $next_key);
  }

  public function calculate($input) {
    list($prev_key, $next_key) = $this->find($input);
    //get previous and next values
    $prev_value = $this->pDatas[$prev_key];
    $next_value = $this->pDatas[$next_key];
    $return = array();
    for ($i = 0; $i < count($prev_value); $i++) {
      // perform linear interpolation calculation
      $temp = ((strtotime($input) - strtotime($prev_key)) * ($next_value[array_keys($next_value)[$i]] - $prev_value[array_keys($prev_value)[$i]]) / (strtotime($next_key) - strtotime($prev_key)));
      $return[array_keys($next_value)[$i]] = round($temp + $prev_value[array_keys($prev_value)[$i]], 2);
    }
    return $return;
  }
}

/**
 * 
 * ////////////////////////////////////////////////////
 * /////////////////// END OF CLASS ///////////////////
 * ////////////////////////////////////////////////////
 * 
 */




/**
 * 
 * sample data
 * 
 */
$inputArray = array(
  '2022-10-26 00:00:00' => ['temp' => 1, 'hum' => 21, 'hum1' => 221, 'hum3' => 221, 'hum4' => 2541, 'hum5' => 2761, 'hum6' => 261],
  '2022-10-26 00:01:00' => ['temp' => 2, 'hum' => 23, 'hum1' => 223, 'hum3' => 223, 'hum4' => 2543, 'hum5' => 2763, 'hum6' => 263],
  '2022-10-26 05:00:00' => ['temp' => 3, 'hum' => 26, 'hum1' => 226, 'hum3' => 226, 'hum4' => 2546, 'hum5' => 2766, 'hum6' => 266],
  '2022-10-26 07:00:00' => ['temp' => 4, 'hum' => 28, 'hum1' => 228, 'hum3' => 228, 'hum4' => 2548, 'hum5' => 2768, 'hum6' => 268],
  '2022-10-26 09:00:00' => ['temp' => 7, 'hum' => 32, 'hum1' => 322, 'hum3' => 232, 'hum4' => 3542, 'hum5' => 3762, 'hum6' => 362],
  '2022-10-26 10:00:00' => ['temp' => 9, 'hum' => 42, 'hum1' => 422, 'hum3' => 242, 'hum4' => 4542, 'hum5' => 4762, 'hum6' => 462],
  '2022-10-26 23:59:58' => ['temp' => 10, 'hum' => 52, 'hum1' => 522, 'hum3' => 252, 'hum4' => 5542, 'hum5' => 5762, 'hum6' => 562],
  '2022-10-26 23:59:59' => ['temp' => 15, 'hum' => 62, 'hum1' => 622, 'hum3' => 262, 'hum4' => 6542, 'hum5' => 6762, 'hum6' => 662]
);



/**
 * 
 * get array properties
 * 
 */
$countArray = count($inputArray);
$firstDate = array_keys($inputArray)[1];
$lastDate = array_keys($inputArray)[$countArray - 1];
// pprint($inputArray, '$inputArray');
// pprint($countArray, '$countArray');
// pprint($firstDate, '$firstDate');
// pprint($lastDate, '$lastDate');



/**
 *  
 * create DatePeriod
 * 
 */
$firstDate = new DateTime($firstDate);
$lastDate = new DateTime($lastDate);
$DatePeriod = new DatePeriod($firstDate, new DateInterval('PT1M'), $lastDate);
// pprint($range, '$range');



/**
 * 
 * create resampleArray
 *
 */
$resampleArray = array();
foreach ($DatePeriod as $dt) {
  $resampleArray[] = $dt->format('Y-m-d H:i:s');
}
// pprint($resampleArray, '$resampleArray');



/**
 * 
 * start Interpolation
 *
 */
$vInterpolation = new LnInterpolation($inputArray);
// pprint($vInterpolation, '$vInterpolation');
$outputArray = array();
foreach ($resampleArray as $value) {
  $outputArray[$value] = $vInterpolation->calculate($value);
}
$countOutput = count($outputArray);
pprint($countOutput, '$countOutput');
pprint($outputArray, '$outputArray');
















echo '<b>Total Execution Time:</b> ' . round(microtime(true) - $time_start, 2) . 's ';



/**
 * 
 * resample without interpolation
 * 
 */
// $arr = [
//   ['date' => '2017-09-01', 'total' => 4],
//   ['date' => '2017-09-07', 'total' => 6],
//   ['date' => '2017-09-09', 'total' => 7]
// ];

// $result = [];
// foreach ($arr as $k => $item) {
//   $d = new DateTime($item['date']);
//   $result[] = $item;
//   if (isset($arr[$k + 1])) {
//     $diff = (new DateTime($arr[$k + 1]['date']))->diff($d)->days;
//     if ($diff > 1) {
//       $result = array_merge($result, array_map(function ($v) use ($d) {
//         $d_copy = clone $d;
//         return [
//           'date' => $d_copy->add(new DateInterval('P' . $v . 'D'))->format('Y-m-d'),
//           'total' => 0
//         ];
//       }, range(1, $diff - 1)));
//     }
//   }
// }

// print_r($result);