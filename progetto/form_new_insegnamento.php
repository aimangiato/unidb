<?php 
    
    include_once('funzioni.php');
    include_once('header.php');
    require('navbar.php');

    $db = open_pg_connection();

    
    $error_msg = '';
    $success_msg = '';

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


    $sql = "SELECT codice_docente, nome, cognome
            FROM unidb.docente
            ";

            $result = pg_query($db, $sql);

            $docente = array();

            while($row = pg_fetch_assoc($result)) {

                $codice_docente = $row['codice_docente'];
                $nome = $row['nome'];
                $cognome = $row['cognome'];

                $docente[$codice_docente] = array($codice_docente, $nome, $cognome);
            }


    if((isset($_POST) && isset($_POST['codice_cdl']) && isset($_POST['docente']) && isset($_POST['nome']) && isset($_POST['descrizione']) && isset($_POST['anno']))) {

            $codice_cdl = explode(" - ", $_POST['codice_cdl'])[0];
            $docente = $_POST['docente'];
            $nome = $_POST['nome'];
            $descrizione = $_POST['descrizione'];
            $anno = $_POST['anno'];



            $sql = "SELECT COUNT(*)
            FROM unidb.insegnamento
            ";

            $result = pg_query($db, $sql);

            $row = pg_fetch_assoc($result);
            $count = (int)$row["count"] + 1;
            $codice_i = "I" . $count;


                $sql = "INSERT INTO unidb.insegnamento(codice_i, codice_cdl, docente, nome, descrizione, anno_erogazione) 
                VALUES ('{$codice_i}', '{$codice_cdl}', '{$docente}', '{$nome}', '{$descrizione}', '{$anno}')
                ";
                    
    
            $result = pg_query($db, $sql);
    
            if($result){
                $success_msg = "Corso di laurea inserito con successo";
            }else {
                $error_msg = "errore durante l'inserimento: " . pg_last_error($db);
            }
        }



    close_pg_connection($db);

?>

<?php
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
?>


<div class="column is-centered">
    <form class="box p-6" action="<?php $_SERVER['PHP_SELF']?>" method="POST">
    <span class="icon-text">
    <span class="icon-is-large">
    <h1 class="title mt-2">Crea un nuovo Insegnamento</h1>
    <div class="field">

    <label class="label mt-5">Corso di Laurea</label>

        <div class="control has-icons-left">
    <div class="select is-fullwidth">
    <select class ="uk-input" type = "string" placeholder="corso di laurea" name= "codice_cdl" required >

    <?php

//visualizza nel menù a tendina ciascuna tupla della query fatta precedentemente (nome e codice corso)
    foreach($cdl as $codice_cdl =>$values) {
    ?>  
           
       <option value = "<?php echo $values[0]; ?>"> <?php echo $values[0] ." - " .$values[1]; ?> </option>

        <?php
    }               
        ?>
    </select>
    <div class="icon is-small is-left">
                <i class="fa-solid fa-book" aria-hidden="true"> </i>
            </div>
        </div>
    </div>


<label class="label mt-5">Docente responsabile</label>
    <div class="control has-icons-left">
        <div class="select is-fullwidth">
    <select type = "string" placeholder="docente" name= "docente" required >

        <?php
        foreach($docente as $codice_docente =>$values) {
        ?>
            <option value = "<?php echo $values[0]; ?>"> <?php echo $values[0] ." - " .$values[1]. " ".$values[2]; ?> </option>

        <?php 
        }

        ?>

    </select>
    <div class="icon is-small is-left">
                <i class="fa-solid fa-book" aria-hidden="true"> </i>
            </div>
        </div>
    </div>



    
    <label class="label mt-5">Nome dell'insegnamento</label>
    <input class ="input" type = "string" placeholder="nome" name="nome" required>

    <label class="label mt-5">Descrizione</label>
    <input class ="input" type = "string" placeholder="descrizione" name= "descrizione" required>

    <label class="label mt-5">Anno di erogazione</label>
    <input class ="input" type = "number" placeholder="codice" name= "anno" min = "1" max = "5" required>

    <div class="field"> </div>
    <p class="control">
    <input class="button is-link is-fullwidth is-medium" type="submit" name="submit" value="Salva">
    </p>
            </div>
</div>

</form>
