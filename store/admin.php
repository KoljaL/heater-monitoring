<?php


include 'assets/functions.php';
$config = getConfig();
$password = "123";
$dbFolder = "assets";
$dbExtension = 'sqlite';
$content = '';
$tableList = '';
$databaseList = '';
$response = array();




/**
 * 
 * LOGIN
 * 
 */
if (isset($_GET['login'])) {
  $passwordCheck = json_decode(trim(file_get_contents("php://input")), true)['password'];
  if ($password !== $passwordCheck) {
    $response = [
      'errorText' => 'Password wrong',
      'data' => ''
    ];
  } else {
    session_start();
    $_SESSION['loggedin'] = true;
    $response = [
      'databases' => getDatabasesTablessAsLinks(),
      'errorText' => '',
      'session' => [
        'id' => session_id(),
      ]
    ];
  }

  echo json_encode($response);
  exit;

} // login
else {
  session_start();
  if (!$_SESSION['loggedin']) {
    $response = [
      'errorText' => 'not logged in',
    ];
    // pprint($_SESSION);
    echo json_encode($response);
    exit;
  }
}






/**
 * 
 * getTabels
 * 
 */
if (isset($_GET['getTables'])) {
  $table = $_GET['getTables'];

  $response = [
    'tables' => getTablesAsLinks($table),
    'errorText' => '',
    'session' => [
      'id' => session_id(),
    ]
  ];
  echo json_encode($response);
  exit;
}






/**
 * 
 * getTabelContent
 * 
 */
if (isset($_GET['getTabelContent']) && isset($_GET['database'])) {
  $table = $_GET['getTabelContent'];
  $database = $_GET['database'];

  $response = [
    'table' => getTabelContent($table, $database),
    'errorText' => '',
    'session' => [
      'id' => session_id(),
    ]
  ];
  echo json_encode($response);
  exit;
}

/////////////////////////////////    PHP    FUNCTIONS    ///////////////////////////
/////////////////////////////////    PHP    FUNCTIONS    ///////////////////////////
/////////////////////////////////    PHP    FUNCTIONS    ///////////////////////////

/**
 * 
 * TABLE CONTENT
 *
 */
function getTabelContent($table, $database) {
  global $db;

  $db = new SQLite3($database);

  $stmt = $db->prepare("SELECT * FROM $table");
  $results = $stmt->execute();

  $firstRow = true;
  $HTML = '<table class="dataTable">';
  while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

    // TABLE HEADER
    //
    if ($firstRow) {
      $HTML .= '<thead><tr>';
      foreach ($row as $key => $value) {
        $HTML .= '<th class="' . $key . '">' . $key . '</th>';
      }
      $HTML .= '</tr></thead><tbody>';
      $firstRow = false;
    }

    // TABLE BODY ROWS
    //
    $HTML .= '<tr>';

    // TABLE CELLS
    //
    foreach ($row as $key => $value) {
      $id = $row[array_keys($row)[0]];
      $name = $table . '_' . $id . '_' . $key;
      $HTML .= '<td><input class="edit ' . $key . '" type="text" name="' . $name . '" value="' . $value . '" /></td>';
    }
    $HTML .= '</tr>';
  }
  $HTML .= '</tbody></table>';
  return $HTML;
}


function getTablesAsLinks($database) {
  global $db;
  $db = new SQLite3($database);
  $stmt = $db->prepare("SELECT * FROM sqlite_master WHERE type='table';");
  $results = $stmt->execute();
  $links = '<ul>';
  while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $links .= "<li><a class='getTabelContent' data-file='" . $row['name'] . "&database=" . $database . "' href='#'>" . $row['name'] . "</a></li>";
  }
  $links .= '</ul>';
  return $links;
}



function getDatabasesTablessAsLinks() {
  global $dbFolder, $dbExtension;
  $files = glob($dbFolder . "/*." . $dbExtension);
  $links = '<ul>';
  foreach ($files as $file) {
    $name = str_replace($dbFolder . '/', '', $file);
    $name = str_replace('.' . $dbExtension, '', $name);
    $links .= "<li><a class='showDatabase' data-file='" . $file . "' href='#'>" . $name . "</a></li>";
  }
  $links .= '</ul>';
  return $links;
}



?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DB Editor</title>

  <style>
  :root {
    --header-height: 50px;
    --footer-height: 50px;
    --main-height: calc(100vh - var(--header-height) - var(--footer-height));
    --sidebar-width: 150px;
    --bg-main: #1b1b1b;
    --bg-header: #212121;
    --bg-footer: #212121;
    --bg-sidebar: rgb(52, 52, 52);
    --text-color: rgb(205, 205, 205);
    --link-color: rgb(140, 180, 255);
    --link-hover-color: rgb(94, 158, 255);
    --border-color: #858585;
  }


  /* scrollbar */

  ::-webkit-scrollbar {
    display: none;
  }

  * {
    -ms-overflow-style: none;
    scrollbar-width: none;
    box-sizing: border-box;
  }

  html {
    font-size: 16px;
    line-height: 1.5rem;
  }

  body {
    margin: 0;
    font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    color: var(--text-color);
    background-color: var(--bg-main);
  }

  header {
    flex-shrink: 0;
    height: var(--header-height);
    outline: 1px solid var(--border-color);
    background-color: var(--bg-header);
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
  }

  footer {
    flex-shrink: 0;
    height: var(--footer-height);
    outline: 1px solid var(--border-color);
    background-color: var(--bg-footer);
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
  }

  main {
    flex-grow: 1;
    display: flex;
    flex-direction: row;
    outline: 1px solid var(--border-color);
  }

  main nav {
    flex-shrink: 0;
    width: var(--sidebar-width);
    background-color: var(--bg-sidebar);
    z-index: 10;
  }

  main article {
    /* outline: 1px solid var(--border-color); */
    flex-grow: 1;
    position: relative;
  }

  #toggleSidebar {
    display: none;
  }

  @media only screen and (max-width: 800px) {
    #toggleSidebar {
      display: block;
    }

    main nav {
      overflow: hidden;
      width: 0;
      transform: translateX(calc(var(--sidebar-width) * -1));
    }

    #sidebarCKB:checked~nav {
      width: var(--sidebar-width);
      transform: translateX(0);
      position: absolute;
      /* height: calc(100% - var(--header-height) - var(--footer-height)); */
      height: var(--main-height);
      transition: transform 1s;
    }
  }

  a {
    text-decoration: none;
    color: var(--link-color);
  }

  a:hover {
    text-decoration: none;
    color: var(--link-hover-color);
  }

  nav ul {
    padding-left: 0;
  }

  li {
    list-style-type: none;
  }

  input {
    font-size: 1rem;
    color: var(--text-color);
    background-color: transparent;
    border: 1px solid var(--border-color);
    border-radius: .3rem;
    padding: .2rem;
  }

  form.login {
    width: 120px;
    margin-inline: auto;
    margin-top: 3rem;
  }

  form.login input {
    margin-top: 1rem;
    width: 100px;
  }


  .dbList {
    padding: .5rem;
  }

  .dataTable tbody,
  .dataTable thead tr {
    display: block;
  }



  .dataTable tbody {
    height: calc(var(--main-height) - 50px);
    overflow: scroll;
  }

  .dataTable th {
    text-align: left;
    padding: .5rem;
    width: 100px;
  }

  .dataTable td {
    padding-inline: .5rem;
  }



  .dataTable .edit {
    border: 1px solid transparent;
    cursor: pointer;
    width: 100px;
  }

  .dataTable .edit:focus {
    outline: none;
  }

  .dataTable .editable:focus {
    border: 1px solid green;
    cursor: auto;
  }


  .dataTable th.id,
  .dataTable td .id {
    width: 30px;
  }

  .dataTable th.date,
  .dataTable .edit.date {
    width: 150px;
  }
  </style>
</head>

<body>
  <header>
    <div>Logo</div>
    <div>Title</div>
    <div>Icon</div>
    <div id="toggleSidebar">
      <label for="sidebarCKB">X</label>
    </div>
  </header>
  <main>
    <input id="sidebarCKB" type="checkbox" style="display:none">
    <nav>
      <div class="dbList">
        <h3>Databases</h3>
        <div></div>
      </div>
      <div class="tableList">
        <h3>Tabels</h3>
        <div></div>
      </div>
    </nav>
    <article>

      <form class="login">
        <label>Password:
          <input id=password name=password type="password" value="123">
        </label>
        <input type="submit" value="submit">
      </form>

    </article>
  </main>
  <footer>
    <div> </div>
    <div><a href="#">Impressum</a></div>
    <div>Link</div>
  </footer>


  <script>
  // ELEMENTS
  const loginForm = document.querySelector('form.login')
  const dbList = document.querySelector('.dbList')
  const tableList = document.querySelector('.tableList')
  const article = document.querySelector('article')
  // LISTENER
  article.addEventListener('dblclick', makeEditable)
  tableList.addEventListener('click', getTableContent)
  dbList.addEventListener('click', getDatabasesTables)
  loginForm.addEventListener('submit', login)
  // VARIABLES
  var sessionID = '';





  /**
   * 
   * MAKE INPUT FIELDS EDITABLE
   * 
   */
  function makeEditable(event) {
    event.preventDefault()
    // console.log(event.target)
    event.target.classList.add('editable')
  }







  /**
   * 
   * GET TABLE CONTENT
   * 
   */
  function getTableContent(event) {
    event.preventDefault()
    if (event.target.className === 'getTabelContent') {
      let file = event.target.dataset.file
      // console.log(event.target)
      fetch('admin.php?getTabelContent=' + file, {
          method: 'GET',
          mode: "same-origin",
          credentials: "same-origin",
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
        })
        .then(response => response.json())
        .then(data => {
          document.querySelector('article').innerHTML = data.table
          // console.log('Success:', data);
        })
        .catch((error) => {
          console.error('Error:', error);
        });
    }
  }






  /**
   * 
   * GET DATABASE TABLES
   * 
   */
  function getDatabasesTables(event) {
    event.preventDefault()
    if (event.target.className === 'showDatabase') {
      let file = event.target.dataset.file
      // console.log(event.target)
      fetch('admin.php?getTables=' + file, {
          method: 'GET',
          mode: "same-origin",
          credentials: "same-origin",
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
        })
        .then(response => response.json())
        .then(data => {
          document.querySelector('.tableList div').innerHTML = data.tables
          document.querySelector('article').innerHTML = ''
          // console.log('Success:', data);
        })
        .catch((error) => {
          console.error('Error:', error);
        });
    }
  }






  /**
   * 
   * LOGIN
   * 
   */
  function login(event) {
    event.preventDefault()
    fetch('admin.php?login', {
        method: 'POST',
        mode: "same-origin",
        credentials: "same-origin",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          password: document.querySelector('form #password').value
        }),
      })
      .then(response => response.json())
      .then(data => {
        sessionID = data.session.id
        document.querySelector('.dbList div').innerHTML = data.databases
        document.querySelector('article').innerHTML = ''
        // console.log('Success:', data);
      })
      .catch((error) => {
        console.error('Error:', error);
      });
  }
  </script>

</body>

</html>