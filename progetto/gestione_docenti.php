<?php

include_once("header.php");
require_once("funzioni.php");
require("navbar.php");

$error_msg = "";
$success_msg = "";



$db = open_pg_connection();

if(isset($_GET['del'])) {

    $sql = "DELETE FROM unidb.users
            WHERE email = '{$_GET['del']}'
    ";

    $result = pg_query($db, $sql);

    if(!$result) {
        $error_msg = "errore nell'eliminazione del docente";
    }else {
        $success_msg = "docente eliminato con successo";
    }
}

$sql = "SELECT *
        FROM unidb.docente
        ORDER BY nome, cognome
";

$result = pg_query($db, $sql);

if (!$result) {

    $error_msg = "errore nella visualizzazione dei docenti, prova più tardi";
}else {

    $docenti = array();

    while ($row = pg_fetch_assoc($result)) {
        $codice_docente = $row["codice_docente"];
        $nome = $row["nome"];
        $cognome = $row["cognome"];
        $email = $row["email"];

        $docenti[$codice_docente] = array($codice_docente, $nome, $cognome, $email);
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
    <h1 class="title mt-2">Gestione docenti</h1>

    <a class="block button is-link is-outlined is-fullwidth mt-2" href="form_new_docente.php">Crea un nuovo docente</a>




<table class="content-table">
<thead>

    <tr>

        <th>Identificativo</th>
        <th>Nome</th>
        <th>Cognome</th> 
        <th>Email</th> 
        <th colspan="2">Azioni</th>

    </tr>

</thead>

<tbody>

<?php
        //separa gli studenti per esame a cui sono iscritti
        $separator = "";

        foreach($docenti as  $codice_docente=> $values) {
            $link1 = 'change_pwd.php?id=' . $values[3];
            $link2 = $_SERVER['PHP_SELF'] . "?del=" . $values[3]; 
?>
            <tr>
            <td><?php echo $values[0]; ?></td>  
            <td><?php echo $values[1]; ?></td>
            <td><?php echo $values[2]; ?></td>
            <td><?php echo $values[3]; ?></td>
            <td><a class="button is-link is-small" href=<?php echo $link1 ?>>Modifica password </a></td>
            <td><a class="button is-link is-small" href=<?php echo $link2 ?>>Elimina </a></td>
        </tr>

<?php
            } ?>


</tbody>
</table>



