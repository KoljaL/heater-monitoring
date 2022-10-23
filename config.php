<?php

$config = [
  'db_file' => 'db.sqlite',
  'ESP' => [
    'haus_one' =>
    [
      'sensors' => [
        [
          'name' => 'temp_1',
          'title' => 'Temperatur Wohnzimmer',
          'value' => 'degree',
          'unit' => '°C',
          'min' => '10',
          'max' => '30',
        ],
        [
          'name' => 'temp_2',
          'title' => 'Temperatur Küche',
          'value' => 'degree',
          'unit' => '°C',
          'min' => '10',
          'max' => '30',
        ],
        [
          'name' => 'hum_1',
          'title' => 'rel. Luftfeuchte Wohnzimmer',
          'value' => 'percent',
          'unit' => '%',
          'min' => '10',
          'max' => '100',
        ],
        [
          'name' => 'hum_2',
          'title' => 'rel. Luftfeuchte Küche',
          'value' => 'percent',
          'unit' => '%',
          'min' => '10',
          'max' => '100',
        ],
      ],
    ]
  ]
];