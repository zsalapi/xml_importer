<?php

require_once 'db_funcs.php';


class CSVImporter
{

    public $list;
    public $product_errors;
    public $relation_errors;

    public $csv_file;

    public function __construct()
    {
        //set the properties
        $this->product_errors = '';
        $this->relation_errors = '';
        $this->list = "<center><table><tr><td colspan=3><br><b>Products details:</b><br><br></td><tr></table>";

        //let It RUN
        $this->csvImport();
        $this->makeList();

        //view the results
        $this->view();


    }

    //Let's view what the object did
    public function view()
    {
        echo ($this->product_errors);
        echo ($this->relation_errors);
        if (isset($_FILES["file"]["tmp_name"])) {
            echo ($this->list);
        }
    }

    //Make List from DB
    public function makeList()
    {
        global $res, $nur;
        connectSQL();

        query("SELECT DISTINCT p.Id, p.Name, r.RelId, r.Name FROM Products AS p INNER JOIN (SELECT DISTINCT Relations.RelId, Products.Name, Relations.Parent FROM Products, Relations WHERE Products.Id=Relations.RelId) AS r ON p.Id=r.Parent");

        for ($i = 0; $i < $nur; $i++) {
            $row = nextRow();
            $this->resultList($row[0], $row[1], $row[2], $row[3]);
        }
        closeSQL();

        return true;
    }

    public function resultList($Id, $Name, $RelId, $RelProduct)
    {
        $this->list .= "<center><table width='800px'>";
        $this->list .= "<tr><td>Id</td><td width='350px'>Product name</td><td>RelId</td><td>Relation product</td><td></td>";
        $this->list .= "<tr><td>" . $Id . "</td><td>" . $Name . "</td><td>" . $RelId . "</td><td>" . $RelProduct . "</td><td></td>";
        $this->list .= "</tr></table><br><br></center>";
    }

    //Import the uploaded csv file into database
    public function csvImport()
    {

        connectSQL();
        global $ID;

        if (isset($_POST["import"])) {

            $fileName = $_FILES["file"]["tmp_name"];

            if ($_FILES["file"]["size"] > 0) {

                $file = fopen($fileName, "r");

                while (($column = fgetcsv($file, 10000, ";")) !== FALSE) {

                    $Id = "";
                    if (isset($column[0])) {
                        $Id = mysqli_real_escape_string($ID, $column[0]);
                    }
                    $Name = "";
                    if (isset($column[1])) {
                        $Name = mysqli_real_escape_string($ID, $column[1]);
                    }
                    $RelId = "";
                    if (isset($column[2])) {
                        $RelId = mysqli_real_escape_string($ID, $column[2]);
                    }

                    if (!query("SELECT * FROM Products WHERE Id=" . $Id . ";")) {
                        $sql = "INSERT INTO Products(Id, Name) VALUES (" . $Id . ",'" . $Name . "');";
                        sqlCmd($sql);
                        //echo($sql);
                    } else {
                        //I removed this because of false postive errors, the reason every relation has a parent so we silently avoid the duplication
                        //$this->productErrorLogger($Id, $Name);
                    }

                    if (!query("SELECT * FROM Relations WHERE RelId=" . $RelId . " and Parent=" . $Id . ";")) {
                        $sql2 = "INSERT INTO Relations(RelId, Parent) VALUES (" . $RelId . "," . $Id . ");";
                        sqlCmd($sql2);
                        //echo($sql2."<br>");
                    } else {
                        $this->relationErrorLogger($RelId, $Id);

                    }
                }
                // print($sql."<br>");
            }
        }


        closeSQL();

    }

    public function relationErrorLogger($RelId, $Id)
    {

        $this->relation_errors .= "<div id='hiba2'><center><table width=800><tr><td>There is already such a relation in the database: <b>Relation: " . $RelId . " / Parent:" . $Id . " </b></td></table></center></div>";
        return true;

    }

    public function productErrorLogger($Id, $Name)
    {

        $this->product_errors .= "<div id=hiba><center><table width=800><tr><td>There is already such a Product in the database: <b> " . $Id . " / " . $Name . " </b></td></table></center></div>";
        return true;

    }

}
?>