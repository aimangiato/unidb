

<?php 
    include_once('funzioni.php');
    include_once('header.php');
    require('navbar.php');

    $db = open_pg_connection();

    $credenziale = $_GET['id'];

    $sql = "SELECT codice_esame, i.codice_i, i.codice_cdl, data_esame, luogo, i.nome as nomecorso 
            FROM unidb.esame e inner join unidb.insegnamento i on e.codice_i = i.codice_i 
            WHERE e.codice_cdl = (
                select codice_cdl
                from unidb.studente
                where email = '{$credenziale}'
                )
            ORDER BY codice_esame, data_esame ASC
            ";

    $result = pg_query($db, $sql);

    $appelli = array();

    //raccolgo in un array associativo (con chiave = codice_esame) tutti gli appelli
    while($row = pg_fetch_assoc($result)) {


        $codice_esame = $row['codice_esame'];
        $codice_i = $row['codice_i'] . " - " . $row['nomecorso'];
        $codice_cdl = $row['codice_cdl'];
        $data_esame = $row['data_esame'];
        $luogo = $row['luogo'];


        $appelli[$codice_esame] = array($codice_esame, $codice_i, $codice_cdl, $data_esame, $luogo);
    }

    close_pg_connection($db);
    


?>

<body>
        <h1 class="title mt-2">Seleziona un appello per iscriverti</h1>   

<table class="content-table">
<thead>

    <tr>

        <th>Codice Esame</th>  
        <th>Insegnamento </th>
        <th>Corso di Laurea </th>
        <th>Data di svolgimento </th>  
        <th>Luogo </th>
        <th> Azioni  </th>

    </tr>
</thead>

<tbody>
   
    <?php
        foreach($appelli as  $codice_esame=> $values) {

            $link = 'resoconto_iscrizione.php?id='.$credenziale. '&cod='.$codice_esame;

            ?>
            <tr> 
            <td><?php echo $values[0]; ?></td>  
            <td><?php echo $values[1]; ?></td>  
            <td><?php echo $values[2]; ?></td>  
            <td><?php echo $values[3]; ?></td>
            <td><?php echo $values[4]; ?></td>
            <td><a class="button is-link is-small" href=<?php echo $link ?>>Iscriviti </a></td>


       <?php 
       } 
       ?>
</tdbody>
</table>
</body>