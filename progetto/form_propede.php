<?php 


    include_once("header.php");
    require_once("funzioni.php");
    require("navbar.php");

    $error_msg = "";
    $success_msg = "";

    $db = open_pg_connection();

    if((isset($_POST['insegnamento']) && isset($_POST['propedeuticita']))) {

    
    
        $sql = "INSERT INTO unidb.propedeuticita(codice_i, propedeuticita)
                VALUES('{$_POST['insegnamento']}', '{$_POST['propedeuticita']}')
                ";
    
        $result = pg_query($db, $sql);
    
        if (!$result) {
            $error_msg = "errore nell'inserimento delle propedeuticità. Assicurati di aver inserito due corsi differenti e dello stesso CDL";
        }else {
            $success_msg = "Propedeuticità creata con successo!";
        }
            

    unset($_POST);
    }
$sql = "SELECT c.nome as nomecdl, codice_i, i.nome  
        FROM unidb.insegnamento i INNER JOIN unidb.cdl c on i.codice_cdl = c.codice_cdl
        ";
$result = pg_query($db, $sql); 

if (!$result) {

    $error_msg = "errore nel caricamento degli insegnamenti";

}else {
    
    $insegnamenti = array();

    while ($row = pg_fetch_assoc($result)) {
        $nomecdl = $row["nomecdl"];
        $codice_i = $row["codice_i"];
        $nome = $row["nome"];

        $insegnamenti[$codice_i] = array($nomecdl, $codice_i, $nome);
    }

}


close_pg_connection($db);
?>



<div class="column is-centered">

<span class="icon-text">
    <span class="icon-is-large">
    <h1 class="title mt-2">Crea Propedeuticità</h1>

    <form class="box p-6" action="<?php $_SERVER['PHP_SELF']?>" method="POST">
    <?php 
    if(!empty($error_msg)) {
        ?>
            <div class="notification is-danger is-light">
                <a><?php echo $error_msg; ?> </a>
            </div>
        <?php
    }if(!empty($success_msg)) {
        ?>
        <div class="notification is-success is-light">
              <a>  <?php echo $success_msg; ?> </a>
            </div>
        <?php
    }
    ?>

    <div class="field">
    <label class="label mt-5">Insegnamento</label>
    </span>
    <h2 class="subtitle"></h2>
    <div class="control has-icons-left">
        <div class="select is-fullwidth">
            <select name="insegnamento" onchange>
                <?php
                
                foreach ($insegnamenti as $codice_i => $values) {
                    ?>
                    <option value="<?php echo $values[1];?>"><?php echo $values[0] . " - ". $values[1] . " - " . $values[2];?> </option>
                    <?php
                }
                ?>
                </select>
            <div class="icon is-small is-left">
                <i class="fa-solid fa-book" aria-hidden="true"> </i>
            </div>
        </div>
    </div>
    </div>

    <div class="field">
    <label class="label mt-5">Propedeuticità</label>
    <h2 class="subtitle"></h2>
    <div class="control has-icons-left">
        <div class="select is-fullwidth">
            <select name="propedeuticita" onchange>
                <?php
                
                foreach ($insegnamenti as $codice_i => $values) {
                    ?>
                    <option value="<?php echo $values[1];?>"><?php echo $values[0] . " - ". $values[1] . " - " . $values[2];?> </option>
                    <?php
                }
                ?>
            </select>
            <div class="icon is-small is-left">
                <i class="fa-solid fa-book" aria-hidden="true"> </i>
            </div>
        </div>
    </div>
    </div>


    <div class="field"> </div>
    <p class="control">
    <input class="button is-link is-fullwidth is-medium" type="submit" name="submit" value="Salva">
    </p>
            </div>
            </div>
            

</form>