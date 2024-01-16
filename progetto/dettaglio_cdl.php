<?php 

include_once('funzioni.php');
include_once('header.php');
require('navbar.php');

$codice_cdl = $_GET['id'];

$db = open_pg_connection();

$sql = "SELECT i.nome, codice_i, d.nome as nomeprof, d.cognome, descrizione, anno_erogazione
        FROM unidb.insegnamento i INNER JOIN unidb.docente d ON i.docente = d.codice_docente
        WHERE codice_cdl = '{$codice_cdl}'
       ";

$result = pg_query($db, $sql);

$insegnamenti = array();

while($row = pg_fetch_assoc($result)) {

    $nome = $row['nome'];
    $codice_i = $row['codice_i'];
    $responsabile = $row['nomeprof']. " ".$row['cognome'];
    $descrizione = $row['descrizione'];
    $anno_erogazione = $row['anno_erogazione'];

    $insegnamenti[$codice_i] = array($nome, $codice_i, $responsabile, $descrizione, $anno_erogazione);

}
close_pg_connection($db)

?>

<h2> Insegnamenti di <?php echo $codice_cdl ?> </h2>

<table class= "content-table">
<thead>
	<tr>
		<th>Nome dell'insegmento</th>
        <th>Codice</th>
        <th>Responsabile</th>
		<th>Descrizione</th>
		<th>Anno erogazione</th>
	</tr>
</thead>
<tbody> 
    <?php   
        foreach($insegnamenti as $codice_i=>$values){  
    ?>
        <tr>
            <td><?php echo $values[0]; ?></td>
            <td><?php echo $values[1]; ?></td>
            <td><?php echo $values[2]; ?></td>
            <td><?php echo $values[3]; ?></td>
            <td><?php echo $values[4]; ?></td>
        </tr>
    <?php
        }
    ?>
</tbody>
</table>