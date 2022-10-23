<?php

echo "<pre>";
// localhost:9090/store.php?ESP=haus_one&temp_1=10&temp_2=29&hum_1=40
include 'config.php';

// error_reporting(E_ALL ^ E_WARNING);


date_default_timezone_set('Europe/Berlin');
$now = new DateTimeImmutable();


// get ESP name from URL and remove it from GET array
$esp = (isset($_GET['ESP'])) ? $_GET['ESP'] : 'haus_one';
$startDate = (isset($_GET['startDate'])) ? $_GET['startDate'] : date('Y-m-d', time()).' 00:00:00';
$endDate = (isset($_GET['endDate'])) ? $_GET['endDate'] : $now->format('Y-m-d H:i:s');


// echo $startDate;
// echo "<br>";
// echo $endDate;

// connect to database
$db = new SQLite3($config['db_file']);




$res = fetchValues($esp, $startDate, $endDate);
// print_r($res);

$JSON = json_encode($res);

// print_r($JSON);


// exit;



//////////////////////////////////  FUNCTIONS  //////////////////////////////////



/**
 * fetchValues($esp)
 * @param string $esp
 * @return array
 */
function fetchValues($esp, $startDate, $endDate)
{
    global $db,$config;
    
    $sensors = $config['ESP'][$esp]['sensors'];



    $stmt = $db->prepare("SELECT * FROM $esp WHERE strftime('%Y-%m-%d %H:%M:S', date) BETWEEN  :startDate AND :endDate");
    $stmt->bindValue('startDate', $startDate, SQLITE3_TEXT);
    $stmt->bindValue('endDate', $endDate, SQLITE3_TEXT);
    $results = $stmt->execute();


    $categories = [];
    $series = [];
    while ($row = $results->fetchArray()) {
        array_push($categories,$row['date']);

        
        foreach ($sensors as $key => $value) {

          // echo $key;
          // print_r($value)."<br>";
          
          $series[$key]['name']= $value['name'];
          $series[$key]['data'][]= $row[$value['name']];

          // $series[$sensor['name']][] = $row[$sensor['name']];
        }

        // echo "{$row['id']} {$row['date']} {$row['temp_2']} \n";
    }

    // print_r($series);
    $res = ['categories' => $categories, 'series' => $series];
    // echo $categories;

    return $res;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chart</title>

  <link rel="stylesheet" href="https://uicdn.toast.com/chart/latest/toastui-chart.min.css" />
  <script src="https://uicdn.toast.com/chart/latest/toastui-chart.min.js"></script>
</head>

<body>
  <div id="chart-area"></div>

  <script>
  const el = document.getElementById('chart-area');

  const data = <?= $JSON ?>;

  // console.log(data1)
  // const data = {
  //   categories: [
  //     '01/01/2020',
  //     '02/01/2020',
  //     '03/01/2020',
  //     '04/01/2020',
  //     '05/01/2020',
  //     '06/01/2020',
  //     '07/01/2020',
  //     '08/01/2020',
  //     '09/01/2020',
  //     '10/01/2020',
  //     '11/01/2020',
  //     '12/01/2020',
  //   ],
  //   series: [{
  //       name: 'Seoul',
  //       data: [-3.5, -1.1, 4.0, 11.3, 17.5, 21.5, 25.9, 27.2, 24.4, 13.9, 6.6, -0.6],
  //     },
  //     {
  //       name: 'Seattle',
  //       data: [3.8, 5.6, 7.0, 9.1, 12.4, 15.3, 17.5, 17.8, 15.0, 10.6, 6.6, 3.7],
  //     },
  //     {
  //       name: 'Sydney',
  //       data: [22.1, 22.0, 20.9, 18.3, 15.2, 12.8, 11.8, 13.0, 15.2, 17.6, 19.4, 21.2],
  //     },
  //     {
  //       name: 'Moscow',
  //       data: [-10.3, -9.1, -4.1, 4.4, 12.2, 16.3, 18.5, 16.7, 10.9, 4.2, -2.0, -7.5],
  //     },
  //     {
  //       name: 'Jungfrau',
  //       data: [-13.2, -13.7, -13.1, -10.3, -6.1, -3.2, 0.0, -0.1, -1.8, -4.5, -9.0, -10.9],
  //     },
  //   ],
  // };
  // console.log(data)

  const options = {
    chart: {
      title: '24-hr Average Temperature',
      width: 1000,
      height: 500
    },
    xAxis: {
      title: 'Month',
      pointOnColumn: true,
      date: {
        format: 'YY-MM-DD',
      },
    },
    yAxis: {
      title: 'Amount',
    },
    tooltip: {
      formatter: (value) => `${value}°C`,
    },
    legend: {
      align: 'bottom',
    },
  };

  const chart = toastui.Chart.lineChart({
    el,
    data,
    options
  });
  </script>
</body>

</html>