<?php

require_once 'db_funcs.php';



class XMLImporter
{
  public $list;
  public $product_errors;
  public $relation_errors;

  public $xml_file;

  public function __construct($xmlfile)
  {
    //set the properties
    $this->product_errors = '';
    $this->relation_errors = '';
    $this->xml_file = $xmlfile;
    $this->list = '<table align=center><tr><td colspan=3><br>Products details:<br><br></td></tr></table>';

    //let It RUN
    $this->xmlParse();
    $this->relationHandling();
    $this->makeList();

    //view the results
    $this->view();

  }

  public function view()
  {
    echo ($this->product_errors);
    echo ($this->relation_errors);
    echo ($this->list);
  }

  //Importing XML
  public function loadTitlesIntoArray($tagName, $path)
  {

    // load XML into simplexml
    $xml = simplexml_load_file($path);

    // if the XML is valid
    if ($xml instanceof SimpleXMLElement) {

      $dom = new DOMDocument('1.0', 'utf-8');
      $dom->preserveWhiteSpace = true;
      $dom->formatOutput = true;

      // use it as a source
      $dom->loadXML($xml->asXML());

      $titels = array();
      $marker = $dom->getElementsByTagName($tagName);

      for ($i = $marker->length - 1; $i >= 0; $i--) {
        $new = $marker->item($i)->textContent;
        $new = $new . ";";
        array_push($titels, $new);
      }

      // print_r( $titels );
      return $titels;
    }
  }

  //Make List from DB
  public function makeList()
  {
    global $nur;
    connectSQL();

    query("SELECT DISTINCT p.Id, p.Name, r.RelId, r.Name FROM Products AS p INNER JOIN (SELECT DISTINCT Relations.RelId, Products.Name, Relations.Parent FROM Products, Relations WHERE Products.Id=Relations.RelId) AS r ON p.Id=r.Parent");

    for ($i = 0; $i < $nur; $i++) {
      $row = nextRow();
      // print_r($row);
      $this->resultList($row[0], $row[1], $row[2], $row[3]);
    }
    closeSQL();

    return true;


  }
  //HTML result of the list for view
  public function resultList($id, $name, $relId, $relProduct)
  {
    $this->list .= "<center><table width='800px'>";
    $this->list .= "<tr><td>Id</td><td width='350px'>Product name</td><td>RelId</td><td>Relation product</td><td></td>";
    $this->list .= "<tr><td>" . $id . "</td><td>" . $name . "</td><td>" . $relId . "</td><td>" . $relProduct . "</td><td></td>";
    $this->list .= "</tr></table><br><br></center>";
  }

  //spliting the text with ; character
  public function spliting($str)
  {
    return preg_split('/;/', $str);
  }

  //Importing Relations
  public function relationHandling()
  {
    connectSQL();

    $titles = $this->loadTitlesIntoArray("Product", "data.xml");
    for ($i = 0; $i < count($titles); $i++) {

      $sor = $titles[$i];
      //var_dump($sor);
      //we are looking for numbers in the line
      $string = preg_replace('/\D+/', ';', $sor);
      $product = $this->spliting($string);

      //to avoid duplicates
      $product_ok = array_unique($product);

      //var_dump($product_ok);
      //handle the relations from the product
      foreach ($product_ok as $relation) {
        //the second is the Parent after comes the Relations of Parent
        $Parent = $product_ok[1];
        //Commented line: You can use this if you wanted to upload parent - parent connection too but It's obvious
        //if ($relation == "") {
        //So I filter parent - parent connection
        if (($relation == "") or ($relation == $Parent)) {
        } else {
          if (!query("SELECT * FROM Relations WHERE RelId=" . $relation . " and Parent=" . $Parent . ";")) {
            $sql2 = "INSERT INTO Relations(RelId, Parent) VALUES (" . $relation . "," . $Parent . ");";
            sqlCmd($sql2);
            //echo($sql2."<br>");
          } else {
            $this->relationErrorLogger($relation, $Parent);
          }
        }
      }
    }

    closeSQL();


    return true;
  }

  public function relationErrorLogger($RelId, $Parent)
  {

    $this->relation_errors .= "<div id='hiba2'><center><table width=800><tr><td>There is already such a relation in the database: <b>Relation: " . $RelId . " / Parent:" . $Parent . " </b></td></table></center></div>";
    return true;

  }

  //Importing Products
  public function xmlParse()
  {
    connectSQL();

    $output = "";

    $xml = simplexml_load_file("$this->xml_file") or die("Error: Cannot import your file as an XML!");

    foreach ($xml->children() as $row) {
      $Id = $row->Id;
      $Name = $row->Name;
      $RelId = $row->RelId;

      if (!query("SELECT * FROM Products WHERE Id=" . $Id . ";")) {
        $sql = "INSERT INTO Products(Id, Name) VALUES (" . $Id . ",'" . $Name . "');";
        sqlCmd($sql);
        //echo($sql);
      } else {

        $this->productErrorLogger($Id, $Name);
      }
    }
    closeSQL();
    return true;
  }

  public function productErrorLogger($Id, $Name)
  {

    $this->product_errors .= "<div id=hiba><center><table width=800><tr><td>There is already such a Product in the database: <b> " . $Id . " / " . $Name . " </b></td></table></center></div>";
    return true;

  }

}
?>