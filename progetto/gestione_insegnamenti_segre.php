<?php 



require_once("funzioni.php");
require("navbar.php");
require_once("header.php");

$error_msg = "";
$success_msg = "";

$db = open_pg_connection();

if (isset($_GET["del"])) {
    $sql = "DELETE FROM unidb.insegnamento
            WHERE codice_i = '{$_GET['del']}'
        ";

    $result = pg_query($db, $sql);
    if (!$result) {
        $error_msg = "errore nella cancellazione". pg_last_error();
    }else {
        $success_msg = "Insegnamento cancellato con successo";
    }

    unset($_GET);
}


$sql = "SELECT cdl.nome as nome_cdl, codice_i , insegnamento.codice_cdl, insegnamento.nome as nome_insegnamento, insegnamento.descrizione, anno_erogazione, d.codice_docente,  d.nome as nomeprof, d.cognome
        FROM unidb.insegnamento INNER JOIN unidb.cdl ON insegnamento.codice_cdl = cdl.codice_cdl INNER JOIN unidb.docente d ON d.codice_docente = insegnamento.docente
        ORDER BY d.nome, d.cognome
        ";

$result = pg_query($db, $sql);

$insegnamenti = array();

while ($row = pg_fetch_assoc($result)) {
    $cdl = $row['nome_cdl'];
    $codice_i = $row['codice_i'];
    $codice_cdl = $row['codice_cdl'];
    $insegnamento = $row['nome_insegnamento'];
    $descrizione = $row['descrizione'];
    $anno = $row['anno_erogazione'];
    $docente = $row['codice_docente'] . " - " . $row['nomeprof'] . " " . $row['cognome'];

    $insegnamenti[$codice_i] = array( $codice_cdl, $cdl, $codice_i, $insegnamento, $descrizione, $anno, $docente);
}

close_pg_connection($db);

?>



<h1 class="title mt-2">Gestione Insegnamenti</h1>

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

<a class="block button is-link is-outlined is-fullwidth mt-2" href="form_new_insegnamento.php">Crea un nuovo Insegnamento</a>
<table class="content-table">
<thead>

    <tr>

        <th>Codice</th>  
        <th>Corso di Laurea</th>
        <th>Nome </th>
        <th>Descrizione</th>  
        <th>Anno</th>
        <th>Controlli</th>

    </tr>
</thead>

<tbody>
   

    <?php
        $separator = "";
        foreach($insegnamenti as  $codice_cdl=> $values) {
            //aggiungi dati da mandare in $_GET per modificare l'insegnamento
            $dati = array(
                "mod" => "si",
                "insegnamento" => $values[2],
                "cdl" => $values[0] . " - " . $values[1],
                "docente" => $values[6],
                "nome" => $values[3],
                "descrizione" => $values[4],
                "anno" => $values[5]
            );
            $link2 = $_SERVER['PHP_SELF'] . "?del=" . $values[2];

    ?>

            <?php if($values[6] != $separator) {
               $separator = $values[6];
               ?>
            <tr>
               <td class="has-text-centered has-text-weight-bold" colspan="100"><?php echo "DOCENTE:  " . $separator ?></td>
           </tr>
<?php } ?>

            <tr> 
            <td><?php echo $values[2]; ?></td>  
            <td><?php echo $values[0] . ' - ' . $values[1]; ?></td>
            <td><?php echo $values[3]; ?></td>  
            <td><?php echo $values[4]; ?></td>  
            <td><?php echo $values[5]; ?></td>
            <td><a class="button is-link is-small"  href=<?php echo $link2 ?>>Elimina</a></td>
        </tr>

       <?php 
       } 
       ?>
</tdbody>
</table>
</body>

</html>