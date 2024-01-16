<?php
    session_start();
    include_once("funzioni.php");
    if (isset($_SESSION) && isset($_SESSION['email'])) {
        switch ($_POST['utente']) {
            case 'studente':      
                $_SESSION['home'] = 'homepage_studente.php';
                Redirect('homepage_studente.php');  
                    break;

            case 'docente':
                $_SESSION['home'] = 'homepage_docente.php';
                Redirect('homepage_docente.php'); 
                    break;

            case 'segreteria':
                $_SESSION['home'] = 'homepage_segreteria.php';
                Redirect('homepage_segreteria.php');   
                    break;
        }
    } else {
    Redirect('index.php');     
            }
?> 