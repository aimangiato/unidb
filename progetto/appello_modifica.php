<?php 
    include_once("header.php");
    require_once("funzioni.php");
    require("navbar.php");

    $error_msg = "";
    $success_msg = "";
    if((isset($_POST['insegnamento']) && isset($_POST['data']) && isset($_POST['luogo']))) {

        $db = open_pg_connection();

        $codice_esame = $_GET['codice'];
    
    
        $sql = "UPDATE unidb.esame
                SET data_esame = '{$_POST['data']}', luogo = '{$_POST['luogo']}'
                WHERE codice_esame = '{$codice_esame}'
                ";
    
        $result = pg_query($db, $sql);
    
        if (!$result) {
            $error_msg = "Modifica non riuscita, controllare i parametri inseriti e riprovare";
        }else {
            $success_msg = "Modifica salvata con successo!";
            $_GET['data'] = $_POST['data'];
            $_GET['luogo'] = $_POST['luogo'];
        }
            

    unset($_POST['insegnamento']);
    unset($_POST['data']);
    unset($_POST['luogo']);
    close_pg_connection($db);
    }


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
    <h1 class="title mt-2">Modifica appello</h1>
    <div class="field">
    <label class="label mt-5">Insegnamento</label>
</span>
    <h2 class="subtitle"></h2>
    <div class="control has-icons-left">
        <input class="input" type="text" name="insegnamento" value="<?php echo $_GET['codice'] . ' - ' . $_GET['nome'] ?>" placeholder="Insegnamento" required="" readonly="">

            <div class="icon is-small is-left">
                <i class="fa-solid fa-book" aria-hidden="true"> </i>
            </div>
        </div>

    </div>

    <div class="field">
        <label class="label mt-5">Data</label>
        <p class="control has-icons-left">
            <input class="input" type="date" name="data" value="<?php echo $_GET['data'] ?>" placeholder required> </input>
            <span class="icon is-small is-left">
                <i class="fa-solid fa-calendar" aria-hidden="true"> </i>
            </span>
        </p>
        

    </div>
        <label class="label mt-5">Luogo</label>
        <p class="control has-icons-left">
            <input class="input" type="text" name="luogo" value="<?php echo $_GET['luogo'] ?>" placeholder="Luogo"> </input>
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