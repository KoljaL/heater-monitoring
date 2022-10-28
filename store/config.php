<?php

$config = [
  'date_default_timezone_set' => 'Europe/Berlin',
  'db_file' => 'db.sqlite',
  'ESP' => [
    'haus_one' => [
      'stepped' => true,
      'tension' => .5,
      'axis' => [
        'Yleft' => [
          'value' => 'degree',
          'unit' => '°C',
          'min' => '0',
          'max' => '70',
        ],
        'Yright' => [
          'value' => 'percent',
          'unit' => '%',
          'min' => '0',
          'max' => '100',
        ],
      ],
      'sensors' => [
        [
          'name' => 'temp_1',
          'label' => 'Temperatur Keller',
          'yAxisID' => 'Yleft',
          'backgroundColor' => 'darkred',
          'borderColor' => 'darkred',
          'unit' => '°C',

        ],
        [
          'name' => 'temp_2',
          'label' => 'Temperatur Garage',
          'yAxisID' => 'Yleft',
          'backgroundColor' => 'darkblue',
          'borderColor' => 'darkblue',
          'unit' => '°C',

        ],
        [
          'name' => 'hum_1',
          'label' => 'rel. Luftfeuchte Keller',
          'yAxisID' => 'Yright',
          'backgroundColor' => 'darkgreen',
          'borderColor' => 'darkgreen',
          'unit' => '%',
        ],
        [
          'name' => 'hum_2',
          'label' => 'rel. Luftfeuchte Garage',
          'yAxisID' => 'Yright',
          'backgroundColor' => 'gold',
          'borderColor' => 'gold',
          'unit' => '%',
        ],
      ],
    ],
    'haus_two' => [
      'stepped' => true,
      'tension' => .5,
      'axis' => [
        'Yleft' => [
          'value' => 'degree',
          'unit' => '°C',
          'min' => '0',
          'max' => '70',
        ],
        'Yright' => [
          'value' => 'percent',
          'unit' => '%',
          'min' => '0',
          'max' => '100',
        ],
      ],
      'sensors' => [
        [
          'name' => 'temp_1',
          'label' => 'Temperatur Wohnzimmer',
          'yAxisID' => 'Yleft',
          'backgroundColor' => 'red',
          'borderColor' => 'red',
          'unit' => '°C',

        ],
        [
          'name' => 'temp_2',
          'label' => 'Temperatur Küche',
          'yAxisID' => 'Yleft',
          'backgroundColor' => 'blue',
          'borderColor' => 'blue',
          'unit' => '°C',

        ],
        [
          'name' => 'hum_1',
          'label' => 'rel. Luftfeuchte Wohnzimmer',
          'yAxisID' => 'Yright',
          'backgroundColor' => 'green',
          'borderColor' => 'green',
          'unit' => '%',
        ],
        [
          'name' => 'hum_2',
          'label' => 'rel. Luftfeuchte Küche',
          'yAxisID' => 'Yright',
          'backgroundColor' => 'orange',
          'borderColor' => 'orange',
          'unit' => '%',
        ],
      ],
    ]
  ]
];



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');



/**
 * 
 * 
 * FUNCTIONS
 * 
 * 
 */
$PPRINT = true;
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