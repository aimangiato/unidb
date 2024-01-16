<?php 

//SI ASSUME CHE L'EMAIL SIA COMPOSTA DA: nomestudente.cognomestudente@studenti.uni
//LA PASSWORD VIENE GENERATA DALLA FUNZIONE randompassword() 
//NUOVA MAIL = NOME.COGNOME@SEGRETERIA.UNI


    include_once('funzioni.php');
    include_once('header.php');
    require('navbar.php');

    $db = open_pg_connection();

    $error_msg = '';
    $success_msg = '';

    if(isset($_POST) && isset($_POST['new'])) {

        $segreteria = $_POST['new'];

        if(empty($segreteria['nome'])) {
            $error_msg = "Inserire il nome del nuovo utente segreteria";
        } else { $nome = $segreteria['nome']; }

        if(empty($segreteria['cognome'])) {
            $error_msg = "Inserire il cognome del nuovo utente segreteria";
        } else { $cognome = $segreteria['cognome']; }

        if(empty($error_msg)) {

            //
            $email = strtolower($nome.".".$cognome."@segreteria.uni");
            $password = randomPassword();


            $sql = "INSERT INTO unidb.users(email, password, utente) VALUES($1, md5($2), $3)";

            $params = array();
            $params[] = $email;
            $params[] = $password;
            $params[] = "segreteria";

            $request = pg_prepare($db,"insert_user", $sql);
            $result = pg_execute($db,"insert_user", $params);

            if($result) {



                $sql = "INSERT INTO unidb.segreteria(email) VALUES ('$email')";

                $result2 = pg_query($db, $sql);

                if ($result2) {
                    $success_msg = "Nuovo utente inserito con successo";
                }


            } else { 
            
                    $error_msg = pg_last_error($db);
                }

            }

        }

         close_pg_connection($db);


?>

<?php if(!empty($error_msg)) {
    ?>

    <div class="uk-alert-danger" uk-alert>
        <a class="uk-alert-close" uk-close></a>
        <p><?php echo $error_msg; ?></p>
    </div>

<?php
}

if (!empty($success_msg)) {
    ?>
    <div class="uk-alert-success" uk-alert>
        <a class="uk-alert-close" uk-close></a>
        <p><?php echo $success_msg. ": password = ". $password; ?></p>
    </div>
    <?php
    }

?>
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
    <h3>Inserire dati anagrafici</h3>
    <label>nome</label>
    <input class ="uk-input" type = "string" placeholder="nome" name= "new[nome]">
        
    <label>cognome</label>
    <input class ="uk-input" type = "string" placeholder="cognome" name="new[cognome]">

            <button>
                SALVA
            </button>
        </form>
    </body>
</html>
