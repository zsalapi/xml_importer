<?php
require_once './db/csvimport.php';

?>
<html>

<head>
    <title>CSV Importer program</title>
    <script type="text/javascript" src="./js/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="./js/csv_upload_form_check.js"></script>
    <?php $csv_obj = new CSVImporter(); ?>
</head>

<body>
    <center>
        <h2>CVS file import to MySQL/MariaDB database</h2>

        <div id="response"></div>

        <div class=" outer-scontainer">
            <div class="row">

                <form class="form-horizontal" action="csvimport_gui.php" method="post" name="frmCSVImport"
                    id="frmCSVImport" enctype="multipart/form-data">
                    <div class="input-row">
                        <label class="col-md-4 control-label">Choose a CSV file
                            File-t</label> <input type="file" name="file" id="file" accept=".csv">
                        <button type="submit" id="submit" name="import" class="btn-submit">Import</button>
                        <br />

                    </div>

                </form>

            </div>
        </div>
    </center>

</body>

</html>