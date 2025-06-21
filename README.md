# xml_importer
XML Importer to DB with relations and CSV import/export (MySQL/MariaDB)

1. Fill out 
   $host = "";
   $user = "";
   $password = "";
   in ./db/db_funcs.php
2.  Import data database with PHPMyAdmin or another way.
3.  Copy the program into your webserver!
4.  You can run xmlimport_csvexport.php and csvimport_form.php
    This program import the data.xml into your data database.
    Optional: There is a link on the page which you can export your data into CSV file.

The trick is in the relations that are handled by the program and the names that are substituted in certain places, 
can be modified to use other kinds of XML.
