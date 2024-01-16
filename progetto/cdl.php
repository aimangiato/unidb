<?php
    include_once('funzioni.php');
    include_once('header.php');
    require('navbar.php');


    $db= open_pg_connection();

    $sql = "SELECT * FROM unidb.cdl";

    $result = pg_query($db,$sql);

    $cdl = array();

    while($row = pg_fetch_assoc($result)) {


        $codice_cdl = $row['codice_cdl'];
        $nome = $row['nome'];
        $tipo = $row['tipo'];
        $descrizione = $row['descrizione'];

        $cdl[$codice_cdl] = array($codice_cdl, $nome, $tipo, $descrizione);

    }
    close_pg_connection($db);

?>

<h1 class="title mt-2">Corsi di laurea dell'ateneo</h1>
<table class= "content-table">
<thead>
	<tr>
		<th>Codice del corso</th>
        <th>Nome</th>
		<th>Tipo di laurea</th>
		<th>Descrizione</th>
	</tr>
</thead>
<tbody> 
    <?php   
        foreach($cdl as $codice_cdl=>$values){  

            $link = 'dettaglio_cdl.php?id='.$codice_cdl;
    ?>
        <tr>
            <td><a href= <?php echo $link;?>> <?php echo $values[0]; ?></td>
            <td><?php echo $values[1]; ?></td>
            <td><?php echo $values[2]; ?></td>
            <td><?php echo $values[3]; ?></td>
        </tr>
    <?php
        }
    ?>
</tbody>
</table>