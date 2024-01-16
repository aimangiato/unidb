<?php

session_start();

include_once("header.php");
require_once("funzioni.php");
require("navbar.php");

$codice_esame = $_GET['cod'];

$db = open_pg_connection();

$sql= "DELETE FROM unidb.esame
        WHERE codice_esame = '{$codice_esame}'
    ";

$result = pg_query($db, $sql);

if (!$result) {
    echo pg_last_error($db);
}else {
    ?>

    <h1>Cancellazione effettuata con successo</h1>

<?php
}



?>