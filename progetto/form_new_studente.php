<?php 

//SI ASSUME CHE I NUOVI STUDENTI DA INSERIRE SIANO TUTTI DEL PRIMO ANNO ACCADEMICO
//SI ASSUME CHE L'EMAIL SIA COMPOSTA DA: nomestudente.cognomestudente@studenti.uni
//LA PASSWORD VIENE GENERATA DALLA FUNZIONE randompassword() 
//NUOVA MAIL = NOME.COGNOME@STUDENTI.UNI
//NUOVA MATRICOLA = "S" . STRING(PROGRESSIVO MATRICOLE + 1)

    
    include_once('funzioni.php');
    include_once('header.php');
    require('navbar.php');

    $db = open_pg_connection();

    
            $sql = "SELECT codice_cdl, nome
                    FROM unidb.cdl
                    ";
            $result = pg_query($db, $sql);

            $cdl = array();

            while($row = pg_fetch_assoc($result)) {

                $codice_cdl = $row['codice_cdl'];
                $nome = $row['nome'];

                $cdl[$codice_cdl] = array($codice_cdl, $nome);

            }



    $error_msg = '';
    $success_msg = '';

    if(isset($_POST) && isset($_POST['new'])) {

        $studente = $_POST['new'];

        if(empty($studente['nome'])) {
            $error_msg = "Inserire il nome dello studente";
        } else { $nome = $studente['nome']; }

        if(empty($studente['cognome'])) {
            $error_msg = "Inserire il cognome dello studente";
        } else { $cognome = $studente['cognome']; }

        if(empty($studente['cdl'])) {
            $error_msg = "Scegliere il corso di laurea";
        } else { $corso = $studente['cdl']; }

        if(empty($error_msg)) {

            //
            $email = strtolower($nome.".".$cognome."@studenti.uni");
            $password = randomPassword();


            $sql = "INSERT INTO unidb.users(email, password, utente) VALUES($1, md5($2), $3)";

            $params = array();
            $params[] = $email;
            $params[] = $password;
            $params[] = "studente";

            $request = pg_prepare($db,"insert_user", $sql);
            $result = pg_execute($db,"insert_user", $params);

            if($result) {

                //QUERY PER IMPOSTARE IL NUMERO DI MATRICOLA DEL NUOVO UTENTE

                $sql = "SELECT numero_studente FROM unidb.progressivo";

                $result1 = pg_query($db, $sql);

                $result1 = pg_fetch_assoc($result1);
                $str = implode($result1);
                $quantity = intval($str) + 1;
                
                $matricola ="S". strval($quantity);

                $anno = 1;

                $sql = "INSERT INTO unidb.studente(matricola, nome, cognome, anno, email, codice_cdl)
                    VALUES($1, $2, $3, $4, $5, $6)";

                $params2 = array();
                $params2[] = $matricola;
                $params2[] = $nome;
                $params2[] = $cognome;
                $params2[] = $anno;
                $params2[] = $email;
                $params2[] = $corso;

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
    <h3>Inserire dati anagrafici e accademici</h3>
    <label>nome</label>
    <input class ="uk-input" type = "string" placeholder="nome" name= "new[nome]">
        
        <label>cognome</label>
        <input class ="uk-input" type = "string" placeholder="cognome" name="new[cognome]">
    
        <label>corso di Laurea</label>
        <select class= "uk-input" type = "string" placeholder= "seleziona il corso" name="new[cdl]"> 
            <?php

        //visualizza nel menù a tendina ciascuna tupla della query fatta precedentemente (nome e codice corso)
            foreach($cdl as $codice_cdl =>$values) {
            ?>  
                   
               <option value = "<?php echo $values[0]; ?>"> <?php echo $values[0] ." " .$values[1]; ?> </option>

                <?php
            }               
                ?>
            </select>

            <button>
                SALVA
            </button>
        </form>
    </body>
</html>
