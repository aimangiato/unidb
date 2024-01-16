
<?php


include_once("header.php");

require("navbar.php");



?>



<div class="container is-max-desktop box">

<div class="block">
      <p class="title is-2 is-link">Buongiorno, <?php echo explode(".", $_SESSION['email'])[0]; ?></p>
      <p class="subtitle is-4">Servizi accessibili come <?= $_SESSION["usertype"]; ?>:</p>
    </div>
        <ul>



            <?php $link = 'change_pwd.php'; ?>
            <li><a  class="block button is-link is-outlined is-fullwidth" href= "<?php echo $link?>">Modifica Password</a></li>

            <?php $link = 'gestione_insegnamenti.php'?>
            <li><a class="block button is-link is-outlined is-fullwidth" href= "<?php echo $link?>">Gestione Insegnamenti</a></li>

            <?php $link = 'gestione_appelli.php' ?>
            <li><a class="block button is-link is-outlined is-fullwidth" href= "<?php echo $link?>">Gestione appelli d'Esame</a></li>

            <?php $link = 'gestione_iscrizioni.php' ?>
            <li><a class="block button is-link is-outlined is-fullwidth" href= "<?php echo $link?>">Gestione Iscrizioni</a></li>

            
            <?php $link = 'gestione_valutazioni.php' ?>
            <li> <a class="block button is-link is-outlined is-fullwidth" href="<?php echo $link?>">Gestione Valutazioni</a></li>
                

        </ul>
</div>
</div> 