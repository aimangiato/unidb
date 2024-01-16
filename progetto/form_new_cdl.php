<?php 
    
    include_once('funzioni.php');
    include_once('header.php');
    require('navbar.php');

    $error_msg = '';
    $success_msg = '';

    if(isset($_POST) && isset($_POST['cdl'])) {
        $cdl = $_POST['cdl'];

        if(!empty($cdl['codice_cdl'])) {
            $codice_cdl = $cdl['codice_cdl'];
        }else { $error_msg = "Errore, inserire codice del corso di Laurea";}

        if(!empty($cdl['nome'])) {
            $nome = $cdl['nome'];
        } else { $error_msg = "Errore, inserire nome del corso di Laurea";}

        if(!empty($cdl['descrizione'])) {
            $descrizione = $cdl['descrizione'];
        } else { $error_msg = "Errore, inserire la descrizione del corso di Laurea";}

        if(!empty($cdl['tipo'])) {
            $tipo = $cdl['tipo'];
        } else { $error_msg = "Errore, inserire il tipo del corso di Laurea (triennale o magistrale)";}

        if (empty($error_msg)) {
            $db = open_pg_connection();
    
            $sql = "INSERT INTO unidb.cdl(codice_cdl, nome, tipo, descrizione) VALUES ($1, $2, $3, $4)";
    
            $params = array ();
            $params[] = $codice_cdl;
            $params[] = $nome;
            $params[] = $tipo;
            $params[] = $descrizione;
    
            $request = pg_prepare($db, "ins_query", $sql);
            $result = pg_execute($db, "ins_query", $params);
    
            if($result){
                $success_msg = "Corso di laurea inserito con successo";
            }else {
                $error_msg = "Errore nell'inserimento del tuo corso di laurea: " . pg_last_error($db);
            }
            close_pg_connection($db);
        }
    }



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
    <h1 class="title mt-2">Inserisci i dati del nuovo Corso di Laurea</h1>
    <div class="field">
</span>
    <h2 class="subtitle"></h2>
    <div class="control has-icons-left">
    <label class="label mt-5">Identificativo del corso di Laurea</label>
    <input class ="uk-input" type = "string" placeholder="codice" name= "cdl[codice_cdl]">
    
    <label class="label mt-5">Nome</label>
    <input class ="input" type = "string" placeholder="nome" name="cdl[nome]">

    <label class="label mt-5">Descrizione</label>
    <input class ="input" type = "string" placeholder="descrizione" name= "cdl[descrizione]">

    <label class="label mt-5">Tipo</label>
    <h2 class="subtitle"></h2>
    <div class="control has-icons-left">
        <div class="select is-full-width">
    <select placeholder = "tipo" name = "cdl[tipo]">
                <option value = "triennale">triennale</option>
                <option value = "magistrale">magistrale</option>
            </select>
            <div class="icon is-small is-left">
                <i class="fa-solid fa-book" aria-hidden="true"> </i>
            </div>
        </div>

    </div>

    <div class="field"> </div>
    <p class="control">
    <input class="button is-link is-fullwidth is-medium" type="submit" name="submit" value="Nuovo appello">
    </p>
            </div>
</div>

</form>




