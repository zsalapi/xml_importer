<?php

require_once 'db_funcs.php';

function csvExport()
{
  connectSQL();
  global $res, $nur;

  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="data.csv"');


  query("SELECT DISTINCT p.Id, p.Name, r.RelId, r.Name FROM Products AS p INNER JOIN (SELECT DISTINCT Relations.RelId, Products.Name, Relations.Parent FROM Products, Relations WHERE Products.Id=Relations.RelId) AS r ON p.Id=r.Parent");


  $fp = fopen('php://output', 'wb');
  for ($i = 0; $i < $nur; $i++) {
    $row = nextRow();
    //Comment: if you echo data here It will be go to the csv file//
    fputcsv($fp, $row, ';');
  }
  fclose($fp);

  closeSQL();


}
csvExport();

?>