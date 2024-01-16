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
        $error_msg = "errore nell'eliminazione dello studente: " . pg_last_error($db);
    }else {
        $success_msg = "studente eliminato e archiviato in storico con successo";
    }
}

$sql = "SELECT matricola, s.nome, cognome, email, s.codice_cdl, c.nome as nomecorso
        FROM unidb.studente s INNER JOIN unidb.cdl c ON s.codice_cdl = c.codice_cdl
        ORDER BY nomecorso, nome, cognome
";

$result = pg_query($db, $sql);

if (!$result) {

    $error_msg = "errore nella visualizzazione degli studenti, prova più tardi";
}else {

    $studenti = array();

    while ($row = pg_fetch_assoc($result)) {
        $matricola = $row["matricola"];
        $nome = $row["nome"];
        $cognome = $row["cognome"];
        $email = $row["email"];
        $cdl = $row["codice_cdl"] . " - " . $row["nomecorso"];
        

        $studenti[$matricola] = array($matricola, $nome, $cognome, $email, $cdl);
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
    <h1 class="title mt-2">Gestione studenti</h1>
    <h2 class="subtitle">Clickare sulla matricola per visualizzare la carriera</h2>

    <a class="block button is-link is-outlined is-fullwidth mt-2" href="form_new_studente.php">Crea un nuovo studente</a>




<table class="content-table">
<thead>

    <tr>

        <th>Matricola</th>
        <th>Nome</th>
        <th>Cognome</th> 
        <th>Email</th> 
        <th>Corso di Laurea</th>
        <th colspan="2">Azioni</th>

    </tr>

</thead>

<tbody>

<?php
        //separa gli studenti per esame a cui sono iscritti
        $separator = "";

        foreach($studenti as  $matricola=> $values) {
            $link1 = 'carriera_completa.php?id=' . $values[3];
            $link2 = 'change_pwd.php?id=' . $values[3];
            $link3 = $_SERVER['PHP_SELF'] . "?del=" . $values[3]; 
?>

            <?php if($values[4] != $separator) {
                $separator = $values[4] 
                ?>
            <tr>
                <td class="has-text-centered has-text-weight-bold" colspan="100"><?php echo $separator ?></td>
            </tr>
            <?php } ?>

            <tr>
            <td> <a href="<?php echo $link1 ?>"><?php echo $values[0]; ?> </a></td>  
            <td><?php echo $values[1]; ?></td>
            <td><?php echo $values[2]; ?></td>
            <td><?php echo $values[3]; ?></td>
            <td><?php echo $values[4]; ?></td>
            <td><a class="button is-link is-small" href=<?php echo $link2 ?>>Modifica Password </a></td>
            <td><a class="button is-link is-small" href=<?php echo $link3 ?>>Elimina </a></td>
        </tr>

<?php
            } ?>


</tbody>
</table>


