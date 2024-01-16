<?php 


include_once("header.php");
require_once("funzioni.php");
require("navbar.php");

/*
IN QUESTA PAGINA E' MOSTRATA UNA TABELLA DIVISA PER CORSO DI LAUREA, IN CUI SONO MOSTRATE, SE ESISTONO,
TUTTE LE PROPEDEUTICITA DI OGNI CORSO
*/ 
$error_msg = "";
$success_msg = "";

$db = open_pg_connection();

/*
QUERY DI ELIMINAZIONE DELLA PROPEDEUTICITA
*/ 

if(isset($_GET["i_principale"]) && isset($_GET["propedeuticita"])) {

    $codice_i = explode(" - ", $_GET["i_principale"])[0];
    $propedeuticita = explode(" - ", $_GET["propedeuticita"])[0];

    $sql = "DELETE FROM unidb.propedeuticita
            WHERE codice_i = '{$codice_i}' AND propedeuticita = '{$propedeuticita}'
        ";

    $result = pg_query($db, $sql);

    if (!$result) {
        $error_msg = "Impossibile eliminare la propedeuticità" . pg_last_error($db);
    }else {
        $success_msg = "Propedeuticità eliminata con successo";
    }
    unset($_GET);
}

/* 
QUERY PER MOSTRARE LE PROPEDEUTICITA'
*/

$sql = "WITH nome_insegnamenti AS (
	SELECT
		i_principale.codice_cdl, p.codice_i, i_principale.nome AS insegnamento_principale,
		p.propedeuticita, i_propedeuticita.nome AS nomepropedeuticita
	FROM
		unidb.propedeuticita p
	INNER JOIN
		unidb.insegnamento i_principale ON p.codice_i = i_principale.codice_i
	INNER JOIN
		unidb.insegnamento i_propedeuticita ON p.propedeuticita = i_propedeuticita.codice_i
)
SELECT c.codice_cdl, c.nome as nomecdl, codice_i, insegnamento_principale, propedeuticita, nomepropedeuticita
FROM nome_insegnamenti n inner join unidb.cdl c on n.codice_cdl = c.codice_cdl
        ";

$result = pg_query($db, $sql);

if (!$result) {
    $error_msg = "Errore nel caricamento della propedeuticità, riprova più tardi";
}else {
    $propede = array();
    $i = 0;

    while ($row = pg_fetch_assoc($result)) {
        $cdl = $row["codice_cdl"] . " - " . $row["nomecdl"];
        $i_principale = $row["codice_i"] . " - ". $row["insegnamento_principale"];
        $propedeutico = $row["propedeuticita"] . " - ". $row["nomepropedeuticita"];

        $propede[$i] = array($cdl, $i_principale, $propedeutico);
        $i++;
    }
    close_pg_connection($db);
}

?>

<?php 
    if(!empty($error_msg)) {
        ?>
            <div class="notification is-danger is-light mt-6">
                <a><?php echo $error_msg; ?> </a>
            </div>
        <?php
    }if(!empty($success_msg)) {
        ?>
        <div class="notification is-success is-light mt-6">
              <a>  <?php echo $success_msg;?> </a>
            </div>
        <?php
    }
    ?>



<h1 class="title mt-2">Propedeuticità</h1>

<a class="block button is-link is-outlined is-fullwidth mt-2" href="form_propede.php">Crea una nuova propedeuticità</a>
<table class="content-table">
<thead>

    <tr>

        <th>Corso di Laurea</th>
        <th>Insegnamento </th>
        <th>Propedeuticità</th>  
        <th>Azioni</th>

    </tr>
</thead>

<tbody>
   
    <?php
       $separator = "";

       foreach($propede as  $i=> $values) {
           $dati = array(
               'i_principale' => $values[1],
               'propedeuticita' => $values[2]
           );
           $link2 = $_SERVER['PHP_SELF'] . '?' . http_build_query($dati);
       
   ?> 
           <?php if($values[0] != $separator) {
               $separator = $values[0];
               ?>
           <tr>
               <td class="has-text-centered has-text-weight-bold" colspan="100"><?php echo $separator ?></td>
           </tr>

           <?php
           } ?>
       <tr>
           <td><?php echo $values[0]; ?></td>  
           <td><?php echo $values[1]; ?></td>
           <td><?php echo $values[2]; ?></td>
           <td><a class="button is-link is-small" href=<?php echo $link2 ?>>Elimina </a></td>
       </tr>


   <?php 
   } 

   ?>
</tdbody>
</table>
</body>