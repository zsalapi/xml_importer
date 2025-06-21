<?php
require_once('./db/xmlimport.php');
?>
<html>

<head>
  <title>CSV Export / XML Importer program (MySQL/MariaDB)</title>
  <h1>
    <center>CSV Export / XML Importer program (MySQL/MariaDB)</center>
  </h1>
  </title>
</head>

<body>
  <?php

  $xmlobj = new XMLImporter("data.xml");

  ?>
  <br>
  <center><a href='./db/csvexport.php'>Export into CSV file</a></center>
</body>

</html>