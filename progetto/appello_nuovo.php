<?php
    include_once("header.php");
    include_once("funzioni.php");
    require("navbar.php");

$success_msg ="";
$error_msg="";

$db = open_pg_connection();

$sql = "SELECT c.codice_cdl as cdl, c.nome as nome_cdl, codice_i , i.nome as nome_insegnamento
        FROM unidb.insegnamento i INNER JOIN unidb.cdl c ON i.codice_cdl = c.codice_cdl
        WHERE docente = (
            SELECT codice_docente
            FROM unidb.docente
            WHERE email = '{$_SESSION['email']}'
            )
        ";

$result = pg_query($db, $sql);

if (!$result) {
    $error_msg ="Errore nel caricamento dei tuoi insegnamenti: " . pg_last_error($db);
}
else {
    $insegnamenti = array();

    while($row = pg_fetch_assoc($result)) {
        $nome_cdl = $row['nome_cdl'];
        $codice_i = $row['codice_i'];
        $nome_insegnamento = $row['nome_insegnamento'];

        $insegnamenti[$codice_i] = array($nome_cdl,  $codice_i, $nome_insegnamento);
    }

}

if((isset($_POST['insegnamento']) && isset($_POST['data']) && isset($_POST['luogo']))) {

    $contaesami = "SELECT COUNT(*)
                   FROM unidb.esame
                "; 
    $count = pg_fetch_result(pg_query($db, $contaesami), 0, 0);
    $count++;

    $trovacdl = "SELECT codice_cdl
                FROM unidb.insegnamento
                WHERE codice_i = '{$_POST['insegnamento']}'
                ";

    $cdl = pg_fetch_result(pg_query($db, $trovacdl), 0, 0);
    
    $codice_esame = "E" . $count; 

    $params = array(
        $codice_esame,
        $_POST['insegnamento'],
        $cdl,
        $_POST['data'],
        $_POST['luogo']
    );

    $sql = "INSERT INTO unidb.esame(codice_esame, codice_i, codice_cdl, data_esame, luogo)
            VALUES($1, $2, $3, $4, $5)
    ";

    $request = pg_prepare($db,"", $sql);
    $result = pg_execute($db, "", $params);

    if(!$result) {
        $error_msg = "Errore nel salvataggio del nuovo appello: " . pg_last_error($db);
    }
    else {
        $success_msg = "Nuovo appello inserito con successo! Numero appello: " . $codice_esame;
    }

    if(!empty($error_msg)) {
        ?>
            <div class="notification is-danger is-light mt-6">
                <a><?php echo $error_msg; ?> </a>
            </div>
        <?php
    }if (!empty($success_msg)) {
        ?>
        <div class="notification is-success is-light mt-6">
              <a>  <?php echo $success_msg; ?> </a>
            </div>
        <?php
    }
        
close_pg_connection($db);

}


?>
    <div class="column is-centered">
    <form class="box p-6" action="<?php $_SERVER['PHP_SELF']?>" method="POST">
    <span class="icon-text">
    <span class="icon-is-large">
    <h1 class="title mt-2">Nuovo appello</h1>
    <div class="field">
    <label class="label mt-5">Insegnamento</label>
</span>
    <h2 class="subtitle"></h2>
    <div class="control has-icons-left">
        <div class="select is-full-width">
            <select name="insegnamento" >
                <?php
                foreach($insegnamenti as  $chiave=> $values) {
                    ?>
                    <option value="<?php echo $values[1] ?>"><?php echo $values[0] . " - " .  $values[1] ." - " . $values[2] ?></option>
                <?php
                }
                ?>
            </select>

            <div class="icon is-small is-left">
                <i class="fa-solid fa-book" aria-hidden="true"> </i>
            </div>
        </div>

    </div>

    <div class="field">
        <label class="label mt-5">Data</label>
        <p class="control has-icons-left">
            <input class="input" type="date" name="data"> </input>
            <span class="icon is-small is-left">
                <i class="fa-solid fa-calendar" aria-hidden="true"> </i>
            </span>
        </p>
        

    </div>
        <label class="label mt-5">Luogo</label>
        <p class="control has-icons-left">
            <input class="input" type="text" name="luogo" placeholder="Luogo"> </input>
            <span class="icon is-small is-left">
                <i class="fa-solid fa-location-dot" aria-hidden="true"> </i>
            </span>
        </p>


    <div class="field"> </div>
    <p class="control">
    <input class="button is-link is-fullwidth is-medium" type="submit" name="submit" value="Nuovo appello">
    </p>
            </div>
            </div>
            

</form>
</body>
