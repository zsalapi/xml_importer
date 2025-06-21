<?php
//DB functions
function connectSQL()
{

    global $ID, $csign;

    $host = "";
    $user = "";
    $password = "";
    $db = "data";

    $ID = mysqli_connect("$host", "$user", "$password", "$db", 3306);
    if ($ID->connect_errno) {
        echo "Failed to connect to MySQL: (" . $ID->connect_errno . ") " . $ID->connect_error;
    }
    $csign = 1;


}

function closeSQL()
{

    global $ID, $csign;

    if ($csign = 1) {
        $ID->close();
    }
    $csign = 0;

}

function query($qstr)
{

    global $ID, $res, $nur;
    $res = mysqli_query($ID, $qstr);
    $nur = mysqli_num_rows($res);
    if ($nur > 0) {
        return true;
    } else {
        return false;
    }
}


function sqlCmd($qstr)
{

    global $ID, $res, $nur;
    if (!empty($ID->error)) {
        echo "<center>" . $ID->error . "</center>";
    }
    $res = mysqli_query($ID, $qstr);


}

function nextRow()
{

    global $ID, $res;
    if (!empty($ID->error)) {
        echo "<center>" . $ID->error . "</center>";
    }
    $row = $res->fetch_row();
    return $row;

}
?>