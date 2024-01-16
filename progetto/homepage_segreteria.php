<?php include_once('header.php');
    require('navbar.php');
?>

<div class="container is-max-desktop box">
<div class="block">
      <p class="title is-2 is-link">Buongiorno, <?php echo explode(".", $_SESSION['email'])[0]; ?></p>
      <p class="subtitle is-4">Servizi accessibili come <?= $_SESSION["usertype"]; ?>:</p>
    </div>
        <ul>



            <?php $link = 'change_pwd.php'?>
            <li><a class="block button is-link is-outlined is-fullwidth" href= "<?php echo $link?>">Modifica Password</a></li>

            <?php $link = 'form_new_cdl.php' ?>
            <li><a class="block button is-link is-outlined is-fullwidth" href= "<?php echo $link?>">Crea un nuovo Corso di Laurea</a></li>

            <?php $link = 'gestione_insegnamenti_segre.php'?>
            <li><a class="block button is-link is-outlined is-fullwidth" href= "<?php echo $link?>">Gestione insegnamenti</a></li>

            
            <?php $link = 'gestione_docenti.php'?>
            <li> <a class="block button is-link is-outlined is-fullwidth" href="<?php echo $link?>"> Gestione Docenti</a></li>

            <?php $link = 'gestione_studenti.php' ?>
            <li> <a class="block button is-link is-outlined is-fullwidth" href="<?php echo $link?>"> Gestione Studenti</a></li>

            <?php $link = 'gestione_ex_studenti.php'?>
            <li> <a class="block button is-link is-outlined is-fullwidth" href="<?php echo $link?>"> Gestione ex-Studenti</a></li>

            <?php $link = 'gestione_propedeuticita.php' ?>
            <li> <a class="block button is-link is-outlined is-fullwidth" href="<?php echo $link?>"> Gestione Propedeuticità</a></li>

                

        </ul>
</div> 
