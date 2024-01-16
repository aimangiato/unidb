<?php 

//SI ASSUME CHE L'EMAIL SIA COMPOSTA DA: nomedocente.cognomedocente@docente.uni
//LA PASSWORD VIENE GENERATA DALLA FUNZIONE randompassword() 
//CODICE DOCENTE = "D" + . STRING(PROGRESSIVO DOCENTI + 1)

    
    include_once('funzioni.php');
    include_once('header.php');
    require('navbar.php');
    $db = open_pg_connection();


    $error_msg = '';
    $success_msg = '';

    if(isset($_POST) && isset($_POST['new'])) {

        $docente = $_POST['new'];

        if(empty($docente['nome'])) {
            $error_msg = "Inserire il nome del docente";
        } else { $nome = $docente['nome']; }

        if(empty($docente['cognome'])) {
            $error_msg = "Inserire il cognome del docente";
        } else { $cognome = $docente['cognome']; }


        if(empty($error_msg)) {

            //
            $email = strtolower($nome.".".$cognome."@docente.uni");
            $password = randomPassword();


            $sql = "INSERT INTO unidb.users(email, password, utente) VALUES($1, md5($2), $3)";

            $params = array();
            $params[] = $email;
            $params[] = $password;
            $params[] = "docente";

            $request = pg_prepare($db,"insert_user", $sql);
            $result = pg_execute($db,"insert_user", $params);

            if($result) {

                //QUERY PER IMPOSTARE L'IDENTIFICATIVO DEL NUOVO DOCENTE

                $sql = "SELECT numero_docente FROM unidb.progressivo";

                $result1 = pg_query($db, $sql);

                $result1 = pg_fetch_assoc($result1);
                $str = implode($result1);
                $quantity = intval($str) + 1;
                
                $codice_docente ="D". strval($quantity);

                $anno = 1;

                $sql = "INSERT INTO unidb.docente(codice_docente, nome, cognome, email)
                    VALUES($1, $2, $3, $4)";

                $params2 = array();
                $params2[] = $codice_docente;
                $params2[] = $nome;
                $params2[] = $cognome;
                $params2[] = $email;

                $request2 = pg_prepare($db, "ins_query", $sql);
                $result2 = pg_execute($db, "ins_query", $params2);

                if($result2) {

                    $success_msg = "Nuovo utente inserito con successo";
                } else { 
            
                    $error_msg = pg_last_error($db);
                }

            }

        }

         close_pg_connection($db);
}


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