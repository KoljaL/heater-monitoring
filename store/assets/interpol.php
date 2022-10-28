<?php
// $time_start = microtime(true);

// echo "<pre>";

require_once('../config.php');

/**
 * 
 * https://github.com/osimosu/linear-interpolation
 * 
 */
class LnInterpolation {
  private $pDatas;
  public $next_valueStore = array();
  public $prev_valueStore = array();
  public function __construct($dDatas) {
    $this->pDatas = $dDatas;
  }

  // get previous and next element in array
  public function find($input) {
    $prev_key = null;
    $next_key = null;
    $prev_keyIDrunner = 0;
    $next_keyIDrunner = 0;

    foreach (array_keys($this->pDatas) as $key) {
      // pprint($this->pDatas[$key], '$this->pDatas[$key]');
      // pprint($key, '$key');
      // convert to timestamp for comparison
      if (strtotime($key) < strtotime($input)) {
        $prev_key = $key;
        $prev_keyID = $prev_keyIDrunner;
      } else {
        //  already found next key, move on
        if ($next_key == null) {
          $next_key = $key;
          $next_keyID = $next_keyIDrunner;
        }
      }
      $prev_keyIDrunner++;
      $next_keyIDrunner++;
    }
    return array($prev_key, $prev_keyID, $next_key, $next_keyID);
  }

  public function calculate($input) {
    // pprint($input, '$input');
    //get previous and next keys
    global $next_valueStore, $prev_valueStore;
    list($prev_key, $prev_keyID, $next_key, $next_keyID) = $this->find($input);
    // pprint($prev_key, '$prev_key');
    // pprint($next_key, '$next_key');
    //get previous and next values
    $prev_value = $this->pDatas[$prev_key];
    $next_value = $this->pDatas[$next_key];
    unset($prev_value['id']);
    unset($next_value['id']);
    // pprint($prev_value, '$prev_value');
    // pprint($next_value, '$next_value');
    $return = array();
    // $next_valueStore = array();
    // $prev_valueStore = array();
    // pprint($prev_value, '$prev_value');
    // pprint($next_value, '$next_value');

    // loop over every value inside $prev and $next_value arrays
    for ($i = 0; $i < count($prev_value); $i++) {
      // if ($next_value[array_keys($next_value)[$i]] === null || $prev_value[array_keys($prev_value)[$i]] === null) {
      //   $temp2 = NULL;
      // } else {
      // perform linear interpolation calculation
      $temp1 = ((strtotime($input) - strtotime($prev_key)) * ($next_value[array_keys($next_value)[$i]] - $prev_value[array_keys($prev_value)[$i]]) / (strtotime($next_key) - strtotime($prev_key)));
      $temp2 = round($temp1 + $prev_value[array_keys($prev_value)[$i]], 2);
      // }
      $return[array_keys($next_value)[$i]] = $temp2;
    }
    // pprint($return, '$return');
    // pprint('', '');

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


// $PPRINT = true;


// /**
//  * 
//  * sample data
//  * 
//  */
// // $inputArray = array(
// //   '2022-10-26 00:00:00' => ['temp' => 1, 'hum' => 1, 'hum1' => 221, 'hum3' => 221, 'hum4' => 2541, 'hum5' => 2761, 'hum6' => 261],
// //   '2022-10-26 00:01:00' => ['temp' => 2, 'hum' => 2, 'hum1' => 223, 'hum3' => 223, 'hum4' => 2543, 'hum5' => 2763, 'hum6' => 263],
// //   '2022-10-26 05:00:00' => ['temp' => null, 'hum' => 3, 'hum1' => 226, 'hum3' => 226, 'hum4' => 2546, 'hum5' => 2766, 'hum6' => 266],
// //   '2022-10-26 07:00:00' => ['temp' => 4, 'hum' => 4, 'hum1' => 228, 'hum3' => 228, 'hum4' => 2548, 'hum5' => 2768, 'hum6' => 268],
// //   '2022-10-26 09:00:00' => ['temp' => 7, 'hum' => 7, 'hum1' => 322, 'hum3' => 232, 'hum4' => 3542, 'hum5' => 3762, 'hum6' => 362],
// //   '2022-10-26 10:00:00' => ['temp' => 9, 'hum' => 9, 'hum1' => 422, 'hum3' => 242, 'hum4' => 4542, 'hum5' => 4762, 'hum6' => 462],
// //   '2022-10-26 23:59:58' => ['temp' => 10, 'hum' => 10, 'hum1' => 522, 'hum3' => 252, 'hum4' => 5542, 'hum5' => 5762, 'hum6' => 562],
// //   '2022-10-26 23:59:59' => ['temp' => 15, 'hum' => 15, 'hum1' => 622, 'hum3' => 262, 'hum4' => 6542, 'hum5' => 6762, 'hum6' => 662]
// // );
// $inputArray = array(
//   '2022-10-26 00:00:00' => ['temp' => 1, 'hum' => 1],
//   '2022-10-26 01:00:00' => ['temp' => 2, 'hum' => 2],
//   '2022-10-26 02:00:00' => ['temp' => null, 'hum' => 3],
//   '2022-10-26 03:00:00' => ['temp' => 4, 'hum' => 4],
//   '2022-10-26 04:00:00' => ['temp' => null, 'hum' => 7],
//   '2022-10-26 05:00:00' => ['temp' => 9, 'hum' => 9],
//   '2022-10-26 06:00:00' => ['temp' => null, 'hum' => 10],
//   '2022-10-26 07:00:00' => ['temp' => 15, 'hum' => 15]
// );


// /**
//  * 
//  * get array properties
//  * 
//  */
// $countArray = count($inputArray);
// $firstDate = array_keys($inputArray)[0];
// $lastDate = array_keys($inputArray)[$countArray - 1];
// // pprint($inputArray, '$inputArray');
// // pprint($countArray, '$countArray');
// // pprint($firstDate, '$firstDate');
// // pprint($lastDate, '$lastDate');



// /**
//  *  
//  * create DatePeriod
//  * 
//  */
// $firstDate = new DateTime($firstDate);
// $lastDate = new DateTime($lastDate);
// $DatePeriod = new DatePeriod($firstDate, new DateInterval('PT1M'), $lastDate);
// // pprint($firstDate, '$firstDate');
// // pprint($lastDate, '$lastDate'); 
// // pprint($DatePeriod, '$DatePeriod');


// /**
//  * 
//  * create resampleArray
//  *
//  */
// $resampleArray = array();
// $removeFirst = true;
// foreach ($DatePeriod as $dt) {
//   if ($removeFirst) {
//     $removeFirst = false;
//     continue;
//   }
//   $resampleArray[] = $dt->format('Y-m-d H:i:s');
// }
// // pprint($resampleArray, '$resampleArray');



// /**
//  * 
//  * start Interpolation
//  *
//  */
// $vInterpolation = new LnInterpolation($inputArray);
// // pprint($vInterpolation, '$vInterpolation');
// $outputArray = array();
// foreach ($resampleArray as $value) {
//   $outputArray[$value] = $vInterpolation->calculate($value);
//   // pprint('', '');
// }
// $countOutput = count($outputArray);
// // pprint($countOutput, '$countOutput');
// // pprint($outputArray, '$outputArray');
















// echo '<b>Total Execution Time:</b> ' . round(microtime(true) - $time_start, 2) . 's ';

// /**
//  *
//  * INTERPOLATE
//  *
//  */
// function interpolate() {
// global $sensors, $db, $startDate, $endDate, $esp;
// // pprint($db, '$db');

// // fetch data from DB
// $stmt = $db->prepare("SELECT * FROM $esp WHERE strftime('%Y-%m-%d %H:%M:S', date) BETWEEN :startDate AND :endDate");
// $stmt->bindValue('startDate', $startDate, SQLITE3_TEXT);
// $stmt->bindValue('endDate', $endDate, SQLITE3_TEXT);
// $results = $stmt->execute();


// $data = [];
// while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
// $date = $row['date'];
// unset($row['date']);
// $data[$date] = $row;
// }

// /**
//    *
//    * filling missing values
//    *
//    */
// foreach ($data as $key => $time) {
// // if ($key === 0) { continue; }
// foreach ($time as $k => $v) {
// if ($v === NULL) {
// $data[$key][$k] = isset($timeStore[$k]) ? $timeStore[$k] : 0;
// }
// }
// $timeStore = $data[$key];
// }
// // pprint($data, '$data');



// /**
// *
// * get array properties
// *
// */
// $countArray = count($data);
// $firstDate = array_keys($data)[1];
// $lastDate = array_keys($data)[$countArray - 1];
// // pprint($countArray, '$countArray');
// // pprint($firstDate, '$firstDate');
// // pprint($lastDate, '$lastDate');
// // pprint($data, '$data');


// /**
// *
// * create DatePeriod & resampleArray
// *
// */
// $firstDate = new DateTime($firstDate);
// $lastDate = new DateTime($lastDate);
// $DatePeriod = new DatePeriod($firstDate, new DateInterval('PT1M'), $lastDate);
// // pprint($range, '$range');
// $resampleArray = array();
// foreach ($DatePeriod as $dt) {
// $resampleArray[] = $dt->format('Y-m-d H:i:s');
// }
// // pprint($resampleArray, '$resampleArray');



// /**
// *
// * start Interpolation
// *
// */
// $vInterpolation = new LnInterpolation($data);
// // pprint($vInterpolation, '$vInterpolation');
// $outputArray = array();
// foreach ($resampleArray as $value) {
// $outputArray[$value] = $vInterpolation->calculate($value);
// }
// // $countOutput = count($outputArray);
// // pprint($countOutput, '$countOutput');
// // pprint($outputArray, '$outputArray');



// /**
// *
// * combine labels wit data
// *
// */
// $labels = array_keys($outputArray);
// // pprint($labels, '$labels');
// $datasets = [];

// foreach ($sensors as $key => $value) {
// $datasets[$key]['label'] = $value['label'];
// $datasets[$key]['name'] = $value['name'];
// $datasets[$key]['yAxisID'] = $value['yAxisID'];
// $datasets[$key]['unit'] = $value['unit'];
// $datasets[$key]['backgroundColor'] = $value['backgroundColor'];
// $datasets[$key]['borderColor'] = $value['borderColor'];

// foreach ($outputArray as $k => $v) {
// // pprint($v[$value['name']]);
// $datasets[$key]['data'][] = $v[$value['name']];
// }
// }
// // pprint($datasets, '$datasets');
// return ['labels' => $labels, 'datasets' => $datasets];
// } // function interpolate








////////////////////////////   OLD   //////////////////////////
////////////////////////////   OLD   //////////////////////////









// pprint($next_value[array_keys($next_value)[$i]], '$next_value[array_keys($next_value)[$i]]');
// if ($prev_value[array_keys($prev_value)[$i]] === null) {
//   $sensorWithoutValue = array_keys($prev_value)[$i];
//   // pprint($i, '$i');
//   // pprint(array_keys($prev_value)[$i], 'array_keys($prev_value)[$i]');
//   // pprint($prev_value, '$prev_value');
//   // pprint($prev_keyID, '$prev_keyID');
//   $veryPrevValue = '';
//   if ($prev_keyID >= 1) {
//     // echo "<br><br>";
//     // pprint('IF KEY ID >1', '');
//     for ($j = $prev_keyID; $j > 1; $j--) {
//       // pprint($j, 'J');
//       // pprint($prev_keyID, '$prev_keyID');
//       // pprint($sensorWithoutValue, '$sensorWithoutValue');
//       if ($this->pDatas[array_keys($this->pDatas)[$j]][$sensorWithoutValue]) {
//         $veryPrevValue = $this->pDatas[array_keys($this->pDatas)[$j]][$sensorWithoutValue];
//         // pprint($veryPrevValue, '$veryPrevValue');
//         $prev_value[array_keys($prev_value)[$i]] = $veryPrevValue;
//         break;
//       } else {
//         // $prev_value[array_keys($prev_value)[$i]] = 99;
//       }
//     }
//   }
// }


// if ($next_value[array_keys($next_value)[$i]] === null) {
//   $sensorWithoutValue = array_keys($next_value)[$i];
//   // pprint($i, '$i');
//   // pprint(array_keys($next_value)[$i], 'array_keys($next_value)[$i]');
//   // pprint($next_value, '$next_value');
//   // pprint($next_keyID, '$next_keyID');
//   $veryPrevValue = '';
//   if ($next_keyID <= 400) {
//     // echo "<br><br>";
//     // pprint('IF KEY ID >1', '');
//     for ($j = $next_keyID; $j < 439; $j++) {
//       //   // pprint($j, 'J');
//       //   // pprint($next_keyID, '$next_keyID');
//       //   // pprint($sensorWithoutValue, '$sensorWithoutValue');
//       if ($this->pDatas[array_keys($this->pDatas)[$j]][$sensorWithoutValue]) {
//         $veryPrevValue = $this->pDatas[array_keys($this->pDatas)[$j]][$sensorWithoutValue];
//         // pprint($veryPrevValue, '$veryPrevValue');
//         $next_value[array_keys($next_value)[$i]] = $veryPrevValue;
//         break;
//       } else {
//         // $next_value[array_keys($next_value)[$i]] = 99;
//       }
//     }
//   }
// }






// store values for case NULL
// if ($next_value[array_keys($next_value)[$i]] === null) {
//   $next_value[array_keys($next_value)[$i]] = isset($next_valueStore[$i]) ?? 0;
// } else {
//   $next_valueStore[$i] = $next_value[array_keys($next_value)[$i]];
// }
// store values for case NULL
// if ($prev_value[array_keys($prev_value)[$i]] === null) {
//   $prev_value[array_keys($prev_value)[$i]] = isset($prev_valueStore[$i]) ?? 11;
// } else {
//   $prev_valueStore[$i] = $prev_value[array_keys($prev_value)[$i]];
// }



// INTERCHANGE VALUES -> no solution if both are NULL
// if ($prev_value[array_keys($prev_value)[$i]] === null) {
//   $prev_value[array_keys($prev_value)[$i]] = $next_value[array_keys($next_value)[$i]];
// }
// if ($next_value[array_keys($next_value)[$i]] === null) {
//   $next_value[array_keys($next_value)[$i]] = $prev_value[array_keys($prev_value)[$i]];
// }

// if ($prev_value[array_keys($prev_value)[$i]] === null && $next_value[array_keys($next_value)[$i]] === null) {
//   $prev_value[array_keys($prev_value)[$i]] = 99;
//   $next_value[array_keys($next_value)[$i]] = 99;
// }