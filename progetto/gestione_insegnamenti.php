


<?php 


require_once("funzioni.php");
require_once("navbar.php");
require_once("header.php");

$db = open_pg_connection();


$sql = "SELECT cdl.nome as nome_cdl, codice_i , insegnamento.codice_cdl, insegnamento.nome as nome_insegnamento, insegnamento.descrizione, anno_erogazione
        FROM unidb.insegnamento INNER JOIN unidb.cdl ON insegnamento.codice_cdl = cdl.codice_cdl
        WHERE docente = (
            SELECT codice_docente
            FROM unidb.docente
            WHERE email = '{$_SESSION['email']}'
            )
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

    $insegnamenti[$codice_i] = array($codice_cdl, $cdl, $codice_i, $insegnamento, $descrizione, $anno);
}

close_pg_connection($db);

?>

<h1 class="title mt-2">Insegnamenti di cui sei responsabile</h1>
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
        foreach($insegnamenti as  $codice_i=> $values) {

            $link = 'gestione_appelli.php'; 
    ?>


            <tr> 
            <td><?php echo $values[2]; ?></td>  
            <td><?php echo $values[0] . ' - ' . $values[1]; ?></td>
            <td><?php echo $values[3]; ?></td>  
            <td><?php echo $values[4]; ?></td>  
            <td><?php echo $values[5]; ?></td>
            <td><a href=<?php echo $link ?>>Appelli </a></td>
        </tr>

       <?php 
       } 
       ?>
</tdbody>
</table>
</body>