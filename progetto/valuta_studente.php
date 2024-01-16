<?php

include_once("header.php");
require_once("funzioni.php");
require("navbar.php");

/*
IN QUESTA PAGINA E' MOSTRATO UN FORM DI VALUTAZIONE PER GLI STUDENTI;
SE NON è MAI STATO VALUTATO LA CELLA "VOTO" SARà VUOTA, ALTRIMENTI SARà
PREIMPOSTATA SU $_POST['voto']
*/ 
$error_msg = "";
$success_msg = "";

if (!isset($_GET["voto"])) {
    $_GET["voto"] = "";
}

if (!(isset($_POST) )) {
    $error_msg = "Compilare tutti i campi per la valutazione";
}else if (isset($_POST['insegnamento']) && isset($_POST['studente']) && isset($_POST['voto'])) {

    $db = open_pg_connection();

    $codice_esame = explode(" - ", $_POST['insegnamento'])[0];
    $matricola = explode(" - ", $_POST['studente'])[0];

    $sql = "SELECT codice_i
            FROM unidb.esame
            WHERE codice_esame = '{$codice_esame}'
            ";

    $result = pg_query($db, $sql);

    if (!$result) {
        $error_msg = "errore nell'inserimento del voto: " . pg_last_error($db);
    }else {
        while ($row = pg_fetch_assoc($result)) {
            $codice_i = $row["codice_i"];
        }

        $sql = "INSERT INTO unidb.carriera_esame(matricola, codice_esame, voto, codice_i)
        VALUES('{$matricola}', '{$codice_esame}', '{$_POST['voto']}', '{$codice_i}')
        ON CONFLICT (matricola, codice_esame)
        DO UPDATE SET voto = '{$_POST["voto"]}'
                ";
    
        $result = pg_query($db, $sql);
    
        if (!$result) {
            $error_msg = "errore nell'inserimento del voto: " . pg_last_error($db);
        }else {
            $success_msg = "Voto inserito con successo!";
        }
    }

    close_pg_connection($db);

}

unset($_POST['voto']);
unset($_POST['insegnamento']);
unset($_POST['studente']);


?>

<div class="column is-centered">
    <form class="box p-6" action="<?php $_SERVER['PHP_SELF']?>" method="POST">
    <?php 
    if(!empty($error_msg)) {
        ?>
            <div class="notification is-danger is-light mt-6">
                <a><?php echo $error_msg; ?> </a>
            </div>
        <?php
    }if(!empty($success_msg)) {
        ?>
        <div class="notification is-success is-light mt-6">
              <a>  <?php echo $success_msg; ?> </a>
            </div>
        <?php
    }
    ?>
    <span class="icon-text">
    <span class="icon-is-large">
    <h1 class="title mt-2">Valuta Studente</h1>
    <div class="field">
    <label class="label mt-5">Insegnamento</label>
</span>
    <h2 class="subtitle"></h2>
    <div class="control has-icons-left">
        <input class="input" type="text" name="insegnamento" value="<?php echo $_GET['esame_insegnamento'] ?>" placeholder="Insegnamento" required="" readonly="">

            <div class="icon is-small is-left">
                <i class="fa-solid fa-book" aria-hidden="true"> </i>
            </div>
        </div>

    </div>

    <div class="field">
        <label class="label mt-5">Data</label>
        <p class="control has-icons-left">
            <input class="input" type="text" name="studente" value="<?php echo $_GET['matricola_studente'] ?>" placeholder required> </input>
            <span class="icon is-small is-left">
                <i class="fa-solid fa-calendar" aria-hidden="true"> </i>
            </span>
        </p>
        

    </div>
        <label class="label mt-5">Voto</label>
        <p class="control has-icons-left">
            <input class="input" type="text" name="voto" value="<?php echo $_GET['voto']?>" placeholder="voto"> </input>
            <span class="icon is-small is-left">
                <i class="fa-solid fa-pen" aria-hidden="true"> </i>
            </span>
        </p>


    <div class="field"> </div>
    <p class="control">
    <input class="button is-link is-fullwidth is-medium" type="submit" name="submit" value="Valuta">
    </p>
            </div>
            </div>
            

</form>