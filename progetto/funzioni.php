<?php


function Redirect($url, $permanent = false) {
    header("Location: $url", true, $permanent ? 301 : 302);
    exit();
  }


/*
Open connection with PostgreSQL server
*/
function open_pg_connection() {
	include_once('conf.php');
    
    $connection = pg_connect("host=".myhost." port=".myport." dbname=".mydb." user=".myuser." password=".mypsw);

    if (!$connection) {
        echo "Errore durante il tentativo di connessione al server";
        exit;
    }
    
    return $connection;
    
}

/*
Close connection with PostgreSQL server
*/
function close_pg_connection($db) {
        
    return pg_close ($db);
    
}

/*
* check the validity of given credentials
*/
function controlla_login($email, $password, $utente) {
    
    $logged = null;

    $db = open_pg_connection();

    $sql = "SELECT email FROM unidb.users WHERE email = $1 AND password = md5($2) AND utente = $3";

    $params = array(
    	$email,
    	$password,
    	$utente
    );

    $result = pg_prepare($db, "check_user", $sql);
    $result = pg_execute($db, "check_user", $params);

    if($row = pg_fetch_assoc($result)){
    	$logged = $row['email'];
    }

    close_pg_connection($db);

    return $logged;
    
}

function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

?>