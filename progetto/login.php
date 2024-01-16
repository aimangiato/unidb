<?php 
    include_once("funzioni.php");
    session_start();


    $logged = null;


    // controllo il login
    if(isset($_POST) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['utente'])){
        $logged = controlla_login($_POST['email'], $_POST['password'], $_POST['utente']);
        if (is_null($logged)) {
            $_SESSION['error'] = 'Credenziali errate, riprova';
            Redirect('index.php');
        }else {
            $_SESSION['email'] = $logged;
            $_SESSION['usertype'] = $_POST['utente'];
            require('redirector.php');
        }
    }

?>
