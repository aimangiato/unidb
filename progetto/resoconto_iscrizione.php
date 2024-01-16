<?php 

    include_once('funzioni.php');
    include_once('header.php');
    require('navbar.php');

    $error_msg = "";
    $success_msg = "";

    $db= open_pg_connection();

    $email = $_GET['id'];
    $codice_esame = $_GET['cod'];

    $sql = "SELECT matricola
            from unidb.studente
            where email = '{$email}'
            ";
    
    $result = pg_query($db, $sql);
    $matricola = '';

    while($row = pg_fetch_assoc($result)) {
        $matricola = $row['matricola'];
    }

    $sql = "INSERT INTO unidb.iscrizione_esame(matricola, codice_esame) VALUES($1, $2)";

    $params = array();

    $params[] = $matricola;
    $params[] = $codice_esame;

    $request = pg_prepare($db,"insert_query", $sql);
    $response = pg_execute($db, "insert_query", $params);

    if (!$response) {
        $error_msg = pg_last_error($db);
    }else {
        $success_msg = "Iscrizione effettuata con successo!";
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

<table class="content-table">
<thead>
	<tr>
       
        <th>Codice Esame</th>
        <th>Matricola </th>
	</tr>
</thead>
<tbody>

    <tr>
        <td><?php echo $codice_esame ?></td>
        <td><?php echo $matricola?></td>
        
    </tr>

</tbody>
</table>
</div>
<?php
    close_pg_connection($db);

?>	



CONSTRAINT carriera_esame_voto_check CHECK (voto > 0::numeric AND voto < 31::numeric)