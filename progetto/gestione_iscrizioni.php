<?php



include_once("header.php");
require_once("funzioni.php");
require("navbar.php");

/*
IN QUESTA PAGINA E' MOSTRATA UNA TABELLA DIVISA PER ESAMI, SOTTO CIASUN
ESAME SONO RIPORTATI GLI STUDENTI (PER NOME, COGNOME E MATRICOLA) ISCRITTI
A QUELL'ESAME.

*/ 
$error_msg = "";
$success_msg = "";


$db = open_pg_connection();

$sql = "WITH prof_insegnamenti_ex AS (
    SELECT data_esame, i.nome as nomecorso, codice_esame
    FROM unidb.insegnamento i INNER JOIN unidb.esame e ON i.codice_i = e.codice_i
    WHERE docente = (
        SELECT codice_docente
        FROM unidb.docente
        WHERE email = '{$_SESSION['email']}'
        )   
    ), studenti_iscritti AS (
        SELECT s.matricola, nome, cognome, email, codice_esame
        FROM unidb.studente s INNER JOIN unidb.iscrizione_esame i ON s.matricola = i.matricola
    ) 

    SELECT  p.codice_esame, nomecorso, data_esame, matricola, nome, cognome, email
    FROM prof_insegnamenti_ex p INNER JOIN studenti_iscritti s ON p.codice_esame = s.codice_esame
    ORDER BY p.codice_esame
        ";

$result = pg_query($db, $sql);

if (!$result) {
    $error_msg = "errore nel caricamento degli studenti iscritti. Riprova più tardi";
}else {

    $iscrizioni = array();

    while ($row = pg_fetch_assoc($result)) {
        $insegnamento = $row['codice_esame'] . " - " . $row['nomecorso'];
        $data = $row['data_esame'];
        $matricola= $row['matricola'];
        $studente= $row['nome'] . " " . $row['cognome'];
        $email = $row['email'];

        $iscrizioni[$insegnamento] = array($insegnamento, $data, $matricola, $studente, $email);
    }
}

close_pg_connection($db);


?>

<h1 class="title mt-2">Studenti iscritti ai tuoi appelli</h1>

<?php
if(!empty($error_msg)) {
        ?>
            <div class="notification is-danger is-light mt-6">
                <a><?php echo $error_msg; ?> </a>
            </div>
<?php } else{
    ?>

<table class="content-table">
<thead>

    <tr>

        <th>Matricola</th>
        <th>Nome</th> 
        <th>Email</th> 
        <th>Azioni</th>

    </tr>

</thead>

<tbody>

<?php
        //separa gli studenti per esame a cui sono iscritti
        $separator = "";

        foreach($iscrizioni as  $insegnamento=> $values) {
            $dati = array(
                'esame_insegnamento' => $values[0],
                'matricola_studente' => $values[2] . " - " . $values[3]
            );
            $link1 = 'valuta_studente.php?' . http_build_query($dati);
        
    ?> 
            <?php if($values[0] != $separator) {
                $separator = $values[0];
                ?>
            <tr>
                <td class="has-text-centered has-text-weight-bold" colspan="100"><?php echo $separator ?>    -   Appello del <?php echo $values[1] ?></td>
            </tr>

            <?php
            } ?>
        <tr>
            <td><?php echo $values[2]; ?></td>  
            <td><?php echo $values[3]; ?></td>
            <td><?php echo $values[4]; ?></td>
            <td><a class="button is-link is-small" href=<?php echo $link1 ?>>Valuta </a></td>
        </tr>


    <?php 
    } 

    ?>
</tbody>
</table>

<?php
} ?>

