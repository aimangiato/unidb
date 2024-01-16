<?php


include_once("header.php");
require_once("funzioni.php");
require("navbar.php");

$db = open_pg_connection();

$sql = "SELECT codice_esame, nome, data_esame, luogo 
        FROM unidb.esame INNER JOIN unidb.insegnamento ON esame.codice_i = insegnamento.codice_i
        WHERE docente = (
            SELECT codice_docente
            FROM unidb.docente
            WHERE email = '{$_SESSION['email']}'
            )
        ";
   $result = pg_query($db, $sql);

   $appelli = array();

   while ($row = pg_fetch_assoc($result)) {
    $codice = $row['codice_esame'];
    $nome = $row['nome'];
    $data = $row['data_esame'];
    $luogo = $row['luogo'];

    $appelli[$codice] = array($codice, $nome, $data, $luogo);
    
   }


   close_pg_connection($db);

?>

<h1 class="title mt-2">Gestione appelli d'Esame</h1>

<a class="block button is-link is-outlined is-fullwidth" href="appello_nuovo.php">Crea un nuovo appello</a>


<table class="content-table">
<thead>

    <tr>

        <th>Esame</th>  
        <th>Data</th>
        <th>Luogo</th>  
        <th colspan="2">Azioni</th>

    </tr>
</thead>

<tbody>

<?php
        foreach($appelli as  $codice_esame=> $values) {

            $dati = array(
                'codice' => $values[0],
                'nome' => $values[1],
                'data' => $values[2],
                'luogo' => $values[3],
            );

            $link1 = 'appello_modifica.php?' . http_build_query($dati); 
            $link2 = 'appello_elimina.php?cod=' . $values[0];
    ?>
        <tr> 
            <td><?php echo $values[0] . ' - ' . $values[1]; ?></td>  
            <td><?php echo $values[2]; ?></td>  
            <td><?php echo $values[3]; ?></td>  
            <td><a class="button is-link is-small" href=<?php echo $link1 ?>>Modifica </a></td>
            <td><a class="button is-link is-small" href=<?php echo $link2 ?>>Elimina </a></td>
        </tr>


    <?php 
    } 

    ?>
</tbody>
</table>