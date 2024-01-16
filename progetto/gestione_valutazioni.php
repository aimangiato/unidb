<?php 


include_once("header.php");
require_once("funzioni.php");
require("navbar.php");

$error_msg = "";
$success_msg = "";

$db = open_pg_connection();

$sql = "WITH insegnamento_carriera AS(
    SELECT c.matricola, i.codice_i, i.nome, c.codice_esame, c.voto 
    FROM unidb.insegnamento i INNER JOIN unidb.carriera_esame c on i.codice_i = c.codice_i
    WHERE docente = (
        SELECT codice_docente
        FROM unidb.docente
        WHERE email = '{$_SESSION['email']}'
        )
), carriera_studente AS(
    SELECT s.matricola, s.nome, cognome, email
    FROM unidb.studente s
    )
SELECT ic.codice_i, ic.nome as nomecorso, data_esame,  ic.codice_esame, ic.voto, cs.matricola, cs.nome, cs.cognome, cs.email
FROM insegnamento_carriera ic INNER JOIN carriera_studente cs on ic.matricola = cs.matricola INNER JOIN unidb.esame e on ic.codice_esame = e.codice_esame
ORDER BY ic.codice_esame
        ";
$result = pg_query($db, $sql);

if (!$result) {
    $error_msg = "Errore nel caricamento delle valutazioni degli studenti. Riprova più tardi";

}else {

    $valutazioni = array();

    $i = 0;

    while ($row = pg_fetch_assoc($result)) {
        $esame_insegnamento = $row["codice_esame"] . " - " . $row["nomecorso"];
        $data = $row["data_esame"];
        $matricola = $row["matricola"];
        $studente = $row["nome"] . " ". $row["cognome"];
        $email = $row["email"];
        $voto = $row["voto"];

        $valutazioni[$i] = array($esame_insegnamento, $data, $matricola, $studente, $email, $voto);
        $i++;
    }
}

close_pg_connection($db);
?>

<?php
/*
MODIFICA OPPORTUNAMENTE QUANTO SEGUE
*/

if(!empty($error_msg)) {
        ?>
            <div class="notification is-danger is-light mt-6">
                <a><?php echo $error_msg; ?> </a>
            </div>
<?php } else{
    ?>

<h1 class="title mt-2">Gestione valutazioni</h1>
<h2 class="subtitle">Valutazioni date a studenti iscritti ad appelli dei tuoi insegnamenti</h2>

<table class="content-table">
<thead>

    <tr>
        <th>Insegnamento</th>
        <th>Data</th>
        <th>Matricola</th>
        <th>Nome</th> 
        <th>Email</th> 
        <th>Voto</th>
        <th>Azioni</th>

    </tr>

</thead>

<tbody>

<?php

        //separa le valutazioni per esame
        $separator = "";

        $link= "valuta_studente.php?";
        for ($i = 0; $i < count($valutazioni); $i++) {
            $dati = array(
                'esame_insegnamento' => $valutazioni[$i][0],
                'matricola_studente' => $valutazioni[$i][2] . " - " . $valutazioni[$i][3],
                'voto' => $valutazioni[$i][5]
            );

            $nomeins = explode(" - ", $valutazioni[$i][0])[1];

            if ($nomeins != $separator) {
                $separator = $nomeins;
                ?>
            <tr>
                <td> <td class="has-text-centered has-text-weight-bold" colspan="100"><?php echo $valutazioni[$i][0] ?>    -   Appello del <?php echo $valutazioni[$i][1] ?></td>
            </tr>
        <?php
            }
        ?>
        <tr>
            <td><?php echo $valutazioni[$i][0]?></td>
            <td><?php echo $valutazioni[$i][1]?></td>
            <td><?php echo $valutazioni[$i][2]?></td>
            <td><?php echo $valutazioni[$i][3]?></td>
            <td><?php echo $valutazioni[$i][4]?></td>
            <td><?php echo $valutazioni[$i][5]?></td>
            <td><a class="button is-link is-small" href="<?php echo $link . http_build_query($dati) ?> ">Modifica</td>
        </tr>
        <?php
        }
        


?>
</tbody>
</table>

<?php
} 
?>


