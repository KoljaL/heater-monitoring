<?php


include 'assets/functions.php';
$config = getConfig();
$password = "123";
$dbFolder = "assets";
$dbExtension = 'sqlite';

$request = json_decode(trim(file_get_contents("php://input")), true);
// $content = '';
// $tableList = '';
// $databaseList = '';



$response = [
  'API' => false,
  'message' => '',
  'data' => [
    'database' => '',
    'table' => '',
    'rows' => '',
    'key' => '',
    'value' => '',
  ]
];
// pprint($_GET);
// pprint($password);

// pprint($request['password']);

// if (!empty($_GET) && $password !== isset($request['password'])  || !isset($_GET['login'])) {
if (!isset($request['password'])) {
    if (!empty($_GET)) {
        $response = [
          'request' => $request,
          'message' => 'no password',
        ];
        echo json_encode($response);
        exit;
    }
}

if (isset($_GET['login'])) {
    $response = [
      'API' => true,
      'data' => [
        'databases' => login(),
      ],
    ];
}

if (isset($_GET['getTables'])) {
    $response = [
      'API' => true,
      'data' => [
        'tables' => getTables(),
      ],
    ];
}


if (isset($_GET['getRows'])) {
    $response = [
      'API' => true,
      'data' => [
        'rows' => getRows(),
      ],
    ];
}

if (isset($_GET['updateValue'])) {
    $response = [
      'API' => true,
      'message' => updateValue(),
    ];
}



//
// OUTPUT
//

// pprint($response, 'response');
// exit;
if ($response['API'] === true) {
    unset($response['API']);
    echo json_encode($response);
    exit;
}








/////////////////////////////////    PHP    FUNCTIONS    ///////////////////////////
/////////////////////////////////    PHP    FUNCTIONS    ///////////////////////////
/////////////////////////////////    PHP    FUNCTIONS    ///////////////////////////



function updateValue()
{
    global $request, $dbFolder, $response;
    // pprint($request);
    $database = $request['database'];
    $table = $request['table'];
    $id = $request['id'];
    $row = $request['row'];
    $value = $request['value'];

    $db = new SQLite3($dbFolder . "/" . $database);
    $stmt = $db->prepare("UPDATE $table SET $row='$value' WHERE id=$id  ");
    $result = $stmt->execute();
    if ($result) {
        return true;
    } else {
        return 'database error';
    }
}

/**
 *
 * LOGIN
 *
 */
function login()
{
    global $request, $password;
    if (isset($_GET['login'])) {
        if ($password !== $request['password']) {
            return false;
        } else {
            return getDatabases();
        }
    }
}





/**
 *
 * TABLE CONTENT
 *
 */
function getRows()
{
    global $request, $dbFolder, $response;
    // pprint($request);
    $database = $request['database'];
    $table = $request['table'];

    $db = new SQLite3($dbFolder . "/" . $database);
    $stmt = $db->prepare("SELECT * FROM $table");
    $results = $stmt->execute();
    $array = array();
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $array[] = $row;
    }
    return $array;
}


function getTables()
{
    global $request, $dbFolder;
    // pprint($request);
    $database = $request['database'];
    $db = new SQLite3($dbFolder . "/" . $database);

    $stmt = $db->prepare("SELECT * FROM sqlite_master WHERE type='table';");
    $results = $stmt->execute();
    $array = array();
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $array[] = [
          'name' => $row['name'],
          'database' => $database
        ];
    }
    return $array;
}



function getDatabases()
{
    global $dbFolder, $dbExtension;
    $files = glob($dbFolder . "/*." . $dbExtension);
    $array = array();
    foreach ($files as $file) {
        $file = str_replace($dbFolder . '/', '', $file);
        $name = str_replace('.' . $dbExtension, '', $file);
        $array[] = [
          'name' => $name,
          'file' => $file
        ];
    }
    return $array;
}



?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DB Editor</title>
  <link rel="shortcut icon"
    href="data:image/x-icon;base64,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" />


  <!-- 
//  
//   ######   ######   ######
//  ##    ## ##    ## ##    ##
//  ##       ##       ##
//  ##        ######   ######
//  ##             ##       ##
//  ##    ## ##    ## ##    ##
//   ######   ######   ######
//   
-->

  <style>
  :root {
    --header-height: 50px;
    --footer-height: 30px;
    --main-height: calc(100vh - var(--header-height) - var(--footer-height));
    --sidebar-width: 150px;
    --bg-main: #1b1b1b;
    --bg-header: #212121;
    --bg-footer: #212121;
    --bg-sidebar: rgb(52, 52, 52);
    --blue: rgb(82, 139, 255);
    --yellow: rgb(209, 154, 102);
    --green: rgb(152, 195, 121);
    --red: rgb(190, 80, 70);
    --text-color: rgb(205, 205, 205);
    --link-color: var(--blue);
    /* --link-color: rgb(140, 180, 255); */
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
    background-color: var(--bg-header);
    padding-inline: 1rem;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
  }

  header .menuIcon {
    width: 24px;
    height: 24px;
  }

  header .breadcrumb {
    min-width: 300px;
    align-self: end;
    margin-bottom: 3px;
  }

  header .showDatabase {
    color: var(--blue);
  }

  header .showTable {
    color: var(--green);
    padding-left: 1rem;

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
    height: calc(var(--main-height) - 1px);
    border-right: 1px solid var(--border-color);
    background-color: var(--bg-sidebar);
    z-index: 10;
  }

  main nav>div {
    padding: .5rem;
  }

  main article {
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
      height: calc(var(--main-height) - 1px);
      transition: transform 1s;
    }
  }

  a {
    text-decoration: none;
    color: var(--link-color);
  }

  a:hover {
    /* color: var(--link-hover-color); */
    filter: brightness(1.25);
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

  main .centered {
    width: max-content;
    margin-inline: auto;
    margin-top: 3rem;
  }

  form.login {
    width: 150px;
  }

  form.login input:not([type="checkbox"]) {
    display: block;
    margin-top: .5rem;
    width: 100px;
  }

  form.login label {
    display: block;
    margin-top: .5rem;
    margin-bottom: .5rem;
  }

  form #wrongPW {
    color: var(--red);
  }

  .dbList,
  .tableList {
    display: none;
  }

  .dbList a {
    color: var(--blue);
  }


  .tableList a {
    color: var(--green);
  }


  /*   TABLE */
  .dataTable {
    border-collapse: collapse;
    width: 100%;
    overflow: scroll;
    display: block;
    scrollbar-width: thin;
    width: calc(100vw - var(--sidebar-width) - 1px);
  }

  .dataTable thead td {
    cursor: pointer;
  }

  .dataTable thead,
  .dataTable tbody {
    display: block;
  }

  .dataTable tbody {
    height: calc(var(--main-height) - 50px);
    width: max-content;
    overflow-y: scroll;
    overflow-x: visible;
  }


  .dataTable tbody tr:nth-of-type(even) {
    backdrop-filter: brightness(0.8)
  }

  .dataTable tbody tr:hover {
    background-color: var(--bg-sidebar);
  }

  .dataTable th {
    text-align: left;
    padding: .5rem;
    min-width: 200px;
    white-space: nowrap;
  }

  .dataTable td {
    padding-inline: .5rem;
    min-width: 200px;
    white-space: nowrap;
    outline: 2px solid transparent;
    border-radius: .5rem;
  }

  td[contenteditable]:focus {
    outline: 2px solid var(--yellow);
  }

  td[contenteditable].waiting {
    outline: 2px solid var(--blue);
  }

  td[contenteditable].success {
    outline: 2px solid var(--green);
  }

  td[contenteditable].error {
    outline: 2px solid var(--red);
  }

  .dataTable th.id,
  .dataTable td.id {
    min-width: 50px;
    max-width: 50px;
  }

  .dataTable th.date,
  .dataTable td.date {
    min-width: 250px;
    max-width: 250px;
  }
  </style>
</head>
<!-- 
//  
//  ##     ## ######## ##     ## ##
//  ##     ##    ##    ###   ### ##
//  ##     ##    ##    #### #### ##
//  #########    ##    ## ### ## ##
//  ##     ##    ##    ##     ## ##
//  ##     ##    ##    ##     ## ##
//  ##     ##    ##    ##     ## ########
//   
-->

<body>
  <header>
    <h2>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="2.757 3.149 493.825 493.719" width="30" height="30">
        <path id="Level_1" style="opacity: 1; fill: rgb(190, 80, 70); stroke: rgb(0, 0, 0); stroke-width: 7;"
          d="M 203.003 110.256 C 145.17 107.605 95.455 100.789 60.235 91.47 C 42.605 86.805 28.505 81.479 18.856 75.741 C 9.143 69.964 3.615 63.399 3.615 56.755 C 3.615 51.715 7.11 46.533 13.535 41.794 C 19.9 37.1 29.396 32.514 41.723 28.199 C 114.43 2.789 271.337 -4.567 386.077 12.056 C 436.533 19.377 472.298 30.362 488.01 43.336 C 492.523 47.099 495.34 51.01 496.171 54.809 C 497.012 58.649 495.823 62.655 492.881 66.362 C 475.961 87.168 408.406 103.256 311.353 109.525 C 293.557 110.675 222.579 111.151 203.003 110.256 Z">
        </path>
        <path id="Level_2" style="opacity: 1; fill: rgb(209, 154, 102); stroke: rgb(0, 0, 0); stroke-width: 7;"
          d="M 204.404 367.949 C 118.712 363.975 48.565 351.037 19.456 333.851 C 11.615 329.196 5.35 322.846 3.928 318.148 C 3.185 315.514 2.837 298.483 2.837 263.629 L 2.837 209.952 L 7.386 216.204 C 19.413 232.901 71.315 247.777 143.904 255.395 C 180.362 259.225 195.016 259.851 248.77 259.873 C 301.359 259.897 312.929 259.466 348.052 256.17 C 422.529 249.187 476.827 234.508 491.65 217.302 L 496.092 212.178 L 496.345 266.248 C 496.482 295.665 496.684 309.466 495.69 316.817 C 494.647 324.53 492.031 325.645 487.559 329.178 C 463.854 347.677 400.172 361.423 311.353 367.172 C 293.59 368.321 223.433 368.827 204.404 367.949 Z">
        </path>
        <path id="Level_3" style="opacity: 1; fill: rgb(152, 195, 121); stroke: rgb(0, 0, 0); stroke-width: 7;"
          d="M 218.884 239.515 C 110.747 236.488 23.309 217.768 6.318 194.059 L 3.304 189.815 L 3.058 137.007 C 2.938 111.102 2.882 97.969 3.076 91.613 C 3.174 88.406 3.355 86.727 3.665 86.051 C 3.787 85.784 4.133 85.405 4.422 85.297 C 4.763 85.169 5.341 85.303 5.56 85.449 C 5.865 85.652 6.168 86.051 6.392 86.36 C 30.895 120.403 189.816 140.392 338.731 128.171 C 394.793 123.579 440.633 114.601 468.637 102.718 C 480.913 97.52 487.892 93.194 492.026 88.183 L 496.572 82.719 L 496.572 137.254 C 496.572 166.776 496.734 180.438 495.738 187.666 C 495.229 191.361 494.348 193.584 493.027 195.243 C 491.702 196.908 489.929 198.091 487.673 199.95 C 456.081 225.854 340.352 242.897 218.884 239.515 Z">
        </path>
        <path id="Level_4" style="opacity: 1; fill: rgb(82, 139, 255); stroke: rgb(0, 0, 0); stroke-width: 7;"
          d="M 203.922 496.723 C 201.357 496.564 192.542 496.042 184.332 495.563 C 92.232 490.178 21.351 472.701 6.318 451.733 L 3.305 447.49 L 3.038 394.722 L 2.757 339.492 L 7.218 344.833 C 25.137 366.457 92.014 382.044 192.379 388.064 C 217.81 389.593 288.155 389.343 314.487 387.632 C 369.165 384.082 414.493 377.384 447.244 368.013 C 459.475 364.517 477.921 356.818 484.243 352.568 C 486.737 350.897 490.107 348.007 491.745 346.126 L 496.573 340.615 L 496.55 394.75 C 496.539 424.302 496.693 438.057 495.625 445.405 C 494.506 453.109 491.803 454.248 487.088 458.08 C 468.308 473.158 418.033 486.096 352.444 492.702 C 319.933 495.973 305.858 496.578 257.174 496.797 C 230.485 496.915 206.517 496.882 203.922 496.723 Z">
        </path>
      </svg>
      DB Editor
    </h2>
    <div class=breadcrumb>
      <span class="showDatabase"></span>
      <span class="showTable">
      </span>
    </div>
    <div class=menuIcon>
      <label id=logOut>logOut</label>
      <label id="toggleSidebar" for="sidebarCKB">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="white">
          <path d="M 24 6 H 0 V 2 H 24 V 6 Z M 24 10 H 0 V 14 H 24 V 10 Z M 24 18 H 0 V 22 H 24 V 18 Z" />
        </svg>
      </label>
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
        <h3>Tables</h3>
        <div></div>
      </div>
    </nav>
    <article>

      <form class="login centered ">
        <label>Password:
          <input id=password name=password type="password">
        </label>
        <label>store
          <input id=storePW name=storePW type="checkbox">
        </label>
        <input type="submit" value="submit">
        <label id="wrongPW"></label>
      </form>

    </article>
  </main>
  <footer>
    <div> </div>
    <div><a href="#"></a></div>
    <div></div>
  </footer>

  <!-- 
  //  
  //   ######   ######  ########  #### ########  ########
  //  ##    ## ##    ## ##     ##  ##  ##     ##    ##
  //  ##       ##       ##     ##  ##  ##     ##    ##
  //   ######  ##       ########   ##  ########     ##
  //        ## ##       ##   ##    ##  ##           ##
  //  ##    ## ##    ## ##    ##   ##  ##           ##
  //   ######   ######  ##     ## #### ##           ##
  //   
  -->

  <script>
  // ELEMENTS
  const loginForm = document.querySelector('form.login')
  const dbList = document.querySelector('.dbList')
  const tableList = document.querySelector('.tableList')
  const article = document.querySelector('article')
  const logOutBtn = document.querySelector('#logOut')
  const storePW = document.querySelector('#storePW')
  const wrongPW = document.querySelector('#wrongPW')


  // LISTENER
  loginForm.addEventListener('submit', login)
  dbList.addEventListener('click', getTables)
  tableList.addEventListener('click', getRows)
  article.addEventListener('dblclick', makeEditable)
  logOutBtn.addEventListener('click', logOut)


  function logOut() {
    window.localStorage.setItem('DBEPW', '')
    window.localStorage.setItem('DBEDB', '')
    window.localStorage.setItem('DBETA', '')
    window.location.reload();
  }

  function store(key, value) {
    if (DBobject.store) {
      window.localStorage.setItem(key, value)
    }
  }

  //   VARIABLES
  var DBobject = {
    password: window.localStorage.getItem('DBEPW') || '',
    database: window.localStorage.getItem('DBEPW') || '',
    table: window.localStorage.getItem('DBETA') || '',
    store: false,
    data: {
      databases: '',
      tables: '',
      rows: '',
      key: '',
      value: ''
    }
  }


  /**
   * 
   * ONLOAD
   * 
   */
  window.addEventListener('load', async () => {


    // if password is saved in localstorage, login directly
    if (window.localStorage.getItem('DBEPW')) {
      await login()
      // if databasename in localstorage get tables
      if (window.localStorage.getItem('DBEDB')) {
        getTables(window.localStorage.getItem('DBEDB'))
        // if tablename in localstorage get rows
        if (window.localStorage.getItem('DBETA')) {
          setTimeout(() => {
            getRows(window.localStorage.getItem('DBETA'))
          }, 500);
        }
      }
    }

  })









  /**
   * 
   * MAKE INPUT FIELDS EDITABLE
   * 
   */
  function makeEditable(event) {
    if (event.target.tagName === 'TD') {
      event.preventDefault()
      event.target.setAttribute("contenteditable", true);
      event.target.focus()
      event.target.addEventListener('focusout', updateValue)
      event.target.addEventListener('keypress', updateValueOnEnter)
    }
    // console.log(event.target)
  }

  /**
   * 
   * UPDATE VALUE ON ENTER KEY
   * 
   */
  function updateValueOnEnter(event) {
    if (event.key === 'Enter') {
      event.preventDefault()
      updateValue(event)
    }

  }

  /**
   * 
   * UPDATE VALUE
   * 
   */
  function updateValue(event) {
    event.target.classList.add('waiting')
    fetch('admin.php?updateValue', {
        method: 'POST',
        mode: "same-origin",
        credentials: "same-origin",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          password: DBobject.password,
          database: DBobject.database,
          table: DBobject.table,
          row: event.target.dataset.attr,
          id: event.target.dataset.id,
          value: event.target.textContent,
        }),
      })
      .then(response => response.json())
      .then(data => {
        console.log('data from updateValue', data)
        if (data.message == true) { // no type checking!!
          event.target.classList.remove('waiting')
          event.target.classList.add('success')
          event.target.setAttribute("contenteditable", false);
          event.target.blur()
        }
        // read error message
        else {
          console.error(data.message)
          event.target.classList.remove('waiting')
          event.target.classList.add('error')
        }
      })
      .catch((error) => {
        event.target.classList.remove('waiting')
        event.target.classList.add('error')
        console.error('Error:', error);
      });
    setTimeout(() => {
      event.target.classList.remove('success')
    }, 1000);
  }







  /**
   * 
   * GET TABLE CONTENT
   * 
   */
  async function getRows(event) {
    if (event.type === 'click' && event.target.className === 'getRows') {
      event.preventDefault()
      DBobject.table = event.target.dataset.table
      DBobject.database = event.target.dataset.database
    } else {
      DBobject.table = event
      DBobject.database = DBobject.database
    }
    fetch(`admin.php?getRows`, {
        method: 'POST',
        mode: "same-origin",
        credentials: "same-origin",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          password: DBobject.password,
          database: DBobject.database,
          table: DBobject.table,
        }),
      })
      .then(response => response.json())
      .then(data => {
        document.querySelector('.showTable').innerHTML = DBobject.table
        store('DBETA', DBobject.table)
        DBobject.data.rows = data.data.rows
        refresh();
      })
      .catch((error) => {
        console.error('Error:', error);
      });
  }






  /**
   * 
   * GET DATABASE TABLES
   * 
   */
  async function getTables(event) {
    if (event.type === 'click' && event.target.className === 'getTables') {
      event.preventDefault()
      DBobject.database = event.target.dataset.file
    } else {
      DBobject.database = event
    }
    console.log('getTables', DBobject)
    fetch('admin.php?getTables', {
        method: 'POST',
        mode: "same-origin",
        credentials: "same-origin",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          password: DBobject.password,
          database: DBobject.database,
          table: DBobject.table,
        }),
      })
      .then(response => response.json())
      .then(data => {
        store('DBEDB', DBobject.database)
        document.querySelector('.showTable').innerHTML = 'choose table'
        document.querySelector('article').innerHTML = ''

        DBobject.data.rows = ''
        DBobject.data.tables = data.data.tables
        refresh();
        return true;
      })
      .catch((error) => {
        console.error('Error:', error);
      });
  }






  /**
   * 
   * LOGIN 
   * 
   */
  async function login(event = '') {

    if (event !== '') {
      event.preventDefault()
      DBobject.password = document.querySelector('form #password').value;
    } else {
      DBobject.password = window.localStorage.getItem('DBEPW')
    }
    fetch('admin.php?login', {
        method: 'POST',
        mode: "same-origin",
        credentials: "same-origin",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          password: DBobject.password
        }),
      })
      .then(response => response.json())
      .then(data => {
        console.log(data)
        if (data.data.databases) {

          if (storePW.checked) {
            DBobject.store = true
          }
          store('DBEPW', DBobject.password)
          DBobject.data.databases = data.data.databases
          // document.querySelector('article').innerHTML = ''
          refresh();
        } else {
          wrongPW.innerHTML = 'Password incorrect'
          console.info('wrong password')
        }
      })
      .catch((error) => {
        console.error('Error:', error);
      });
  }


  /**
   * 
   * REFRESH
   * 
   */
  function refresh() {
    console.log('refresh: ', DBobject)

    // databases list
    //  
    if (DBobject.data.databases !== '') {
      let DB = '';
      DBobject.data.databases.forEach(el => {
        DB += `<li><a class="getTables" href="#" data-file=${el.file}>${el.name}</a></li>`
      });
      document.querySelector('.dbList').style.display = 'block'
      document.querySelector('.dbList div').innerHTML = DB
      document.querySelector('.showDatabase').innerHTML = 'choose database'
    }

    // table list
    //
    if (DBobject.data.tables !== '') {
      let TAB = '';
      DBobject.data.tables.forEach(el => {
        TAB += `<li><a class="getRows" href="#" data-database=${el.database} data-table=${el.name}>${el.name}</a></li>`
      });
      document.querySelector('.tableList').style.display = 'block'
      document.querySelector('.tableList div').innerHTML = TAB
    }

    // rows
    // 
    if (DBobject.data.rows !== '') {
      document.querySelector('article').innerHTML = ''
      makeTable(DBobject.data.rows)
    }

  }



  /**
   * 
   * MAKE TABLE
   * 
   */
  function makeTable(data) {

    // create table
    const table = document.createElement("table");
    table.classList.add('dataTable')
    // create THead  & TBody
    const tableHead = table.createTHead();
    const tableContent = table.createTBody();
    tableContent.id = "table-content"
    // append table to DOM 
    document.querySelector('article').appendChild(table)

    makeTableHead(data);
    makeTableBody(data);
    makeSort(data);

    function makeTableHead(data) {
      const row = document.createElement("tr");
      const objKeys = Object.keys(data[0]);
      objKeys.map((key) => {
        const cell = document.createElement("th");
        cell.setAttribute("data-attr", key);
        cell.classList.add(key);
        cell.id = key;
        cell.innerHTML = key;
        row.appendChild(cell);
      });
      tableHead.appendChild(row);
    };

    function makeTableBody(data) {
      data.map((obj) => {
        const row = createRow(obj);
        tableContent.appendChild(row);
      });
    };

    function createRow(obj) {
      const row = document.createElement("tr");
      const objKeys = Object.keys(obj);
      const id = obj['id']
      objKeys.map((key) => {
        const cell = document.createElement("td");

        cell.setAttribute("data-database", DBobject.database);
        cell.setAttribute("data-table", DBobject.table);
        cell.setAttribute("data-id", id);

        cell.setAttribute("data-attr", key);
        cell.classList.add(key);
        cell.innerHTML = obj[key];
        row.appendChild(cell);
      });
      return row;
    };


    function makeSort(data) {
      const tableButtons = document.querySelectorAll("th");
      [...tableButtons].map((button) => {
        button.addEventListener("click", (e) => {
          resetButtons(e);
          if (e.target.getAttribute("data-dir") == "desc") {
            sortData(data, e.target.id, "desc");
            e.target.setAttribute("data-dir", "asc");
          } else {
            sortData(data, e.target.id, "asc");
            e.target.setAttribute("data-dir", "desc");
          }
        });
      });


      function sortData(data, param, direction = "asc") {
        tableContent.innerHTML = '';
        const sortedData =
          direction == "asc" ? [...data].sort(function(a, b) {
            if (a[param] < b[param]) {
              return -1;
            }
            if (a[param] > b[param]) {
              return 1;
            }
            return 0;
          }) : [...data].sort(function(a, b) {
            if (b[param] < a[param]) {
              return -1;
            }
            if (b[param] > a[param]) {
              return 1;
            }
            return 0;
          });

        makeTableBody(sortedData);
      };

      const resetButtons = (event) => {
        [...tableButtons].map((button) => {
          if (button !== event.target) {
            button.removeAttribute("data-dir");
          }
        });
      };
    }
  }
  </script>

</body>

</html>