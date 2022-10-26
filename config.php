<?php

$config = [
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
          'backgroundColor' => 'lightblue',
          'borderColor' => 'lightblue',
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