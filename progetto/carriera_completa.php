<?php
    include_once('funzioni.php');
    include_once('header.php');
    require('navbar.php');

    $db= open_pg_connection();

    $email = $_GET['id'];

    $sql = "SELECT c.matricola, i.nome, c.codice_esame, c.voto
            from unidb.carriera_esame c inner join unidb.esame e on c.codice_esame = e.codice_esame inner join unidb.insegnamento i on i.codice_i = e.codice_i
            where matricola = 
                (select matricola 
                from unidb.studente 
                where email = '{$_SESSION['email']}')";

    $result = pg_query($db, $sql);

    $esami= array();

    while($row = pg_fetch_assoc($result)) {

        $matricola = $row['matricola'];
        $nome = $row['nome'];
        $codice_esame = $row['codice_esame'];
        $voto = $row['voto'];

        $esami[$codice_esame] = array($matricola, $nome, $codice_esame, $voto);
    }

?>

<h1 class="title mt-2">La tua carriera completa</h1>
<table class="content-table">
<thead>
	<tr>
		<th>Matricola</th>
        <th>Nome</th>
		<th>Codice Esame</th>
		<th>Voto</th>
	</tr>
</thead>
<tbody>
<?php

foreach($esami as $codice_esame=>$values){  
?>
    <tr>
        <td><?php echo $values[0]; ?></td>
        <td><?php echo $values[1]; ?></td>
        <td><?php echo $values[2]; ?></td>
        <td><?php echo $values[3]; ?></td>
    </tr>
<?php
}
?>
</tbody>
</table>
<?php
    close_pg_connection($db);
?>	