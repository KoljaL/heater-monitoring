<?php


include 'assets/functions.php';
$config = getConfig();
$content = '';
$db = new SQLite3('assets/' . $config['db_file']);


if ($_GET['showTable']) {
  $table = $_GET['showTable'];
  $content = showTable($table);
}


// echo getTablesAsLinks();


function showTable($table) {
  global $db;
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


  // while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
  //   pprint($row);
  // }
  return $HTML;
}


function getTablesAsLinks() {
  global $db;
  $stmt = $db->prepare("SELECT * FROM sqlite_master WHERE type='table';");
  $results = $stmt->execute();
  $links = '';
  while ($table = $results->fetchArray(SQLITE3_ASSOC)) {
    // pprint($table);
    $links .= "<a href='./admin.php?showTable=" . $table['name'] . "'>" . $table['name'] . "</a>";
  }
  return $links;
}


?>
<style>
a {
  text-decoration: none;
  color: cornflowerblue;
}

a:hover {
  color: rgb(76, 111, 177);
}

nav {
  display: flex;
  flex-direction: row;
  gap: 1rem;
  flex-wrap: wrap;
  align-items: center;
  justify-content: flex-start;
}



.dataTable tbody,
.dataTable thead tr {
  display: block;
}

.dataTable tbody {
  height: calc(100vh - 100px);
  overflow-y: auto;
  overflow-x: hidden;
}


.dataTable tbody {
  height: calc(100vh - 100px);
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

<nav>
  <?= getTablesAsLinks() ?> </nav>


<div>
  <?= $content ?> </div>



<script>
document.querySelectorAll('input.edit').forEach(input => {
  // console.log(el)
  input.addEventListener('dblclick', (el) => {
    el.target.classList.add('editable')
  })
});
</script>