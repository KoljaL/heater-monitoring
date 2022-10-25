<?php

$config = [
  'db_file' => 'db.sqlite',
  'ESP' => [
    'haus_one' =>
    [
      'sensors' => [
        [
          'name' => 'temp_1',
          'label' => 'Temperatur Wohnzimmer', 
          'backgroundColor' => 'red',
          'borderColor' => 'red',
          'value' => 'degree',
          'unit' => '°C',
          'min' => '10',
          'max' => '30',
        ],
        [
          'name' => 'temp_2',
          'label' => 'Temperatur Küche', 
          'backgroundColor' => 'blue',
          'borderColor' => 'blue',
          'value' => 'degree',
          'unit' => '°C',
          'min' => '10',
          'max' => '30',
        ],
        [
          'name' => 'hum_1',
          'label' => 'rel. Luftfeuchte Wohnzimmer', 
          'backgroundColor' => 'green',
          'borderColor' => 'green',
          'value' => 'percent',
          'unit' => '%',
          'min' => '10',
          'max' => '100',
        ],
        [
          'name' => 'hum_2',
          'label' => 'rel. Luftfeuchte Küche',
          'backgroundColor' => 'orange',
          'borderColor' => 'orange',
          'value' => 'percent',
          'unit' => '%',
          'min' => '10',
          'max' => '100',
        ],
      ],
    ]
  ]
];