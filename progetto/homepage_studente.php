
<?php include_once('header.php');
    require('navbar.php')
?>
<div class="container is-max-desktop box mt-6">

<div class="block">
      <p class="title is-2 is-link">Buongiorno, <?php echo explode(".", $_SESSION['email'])[0]; ?></p>
      <p class="subtitle is-4">Servizi accessibili come <?= $_SESSION["usertype"]; ?>:</p>
    </div>

    <form>
        <ul>


        
            <?php $link = 'change_pwd.php?id=' . $_SESSION['email']; ?>
            <li><a class="block button is-link is-outlined is-fullwidth" href= "<?php echo $link?>">Modifica Password</a></li>



            <?php $link = 'iscrizione_esame.php?id=' . $_SESSION['email']; ?>
            <li><a class="block button is-link is-outlined is-fullwidth" href= "<?php echo $link?>">Iscrizione esami</a></li>


            <?php $link = 'carriera_completa.php?id=' . $_SESSION['email']; ?>
            <li><a class="block button is-link is-outlined is-fullwidth" href= "<?php echo $link?>">Visualizza Carriera completa</a></li>

            <?php $link = 'carriera_valida.php?id=' . $_SESSION['email']; ?>
            <li><a class="block button is-link is-outlined is-fullwidth" href= "<?php echo $link?>">Visualizza Carriera valida</a></li>

            <li><a class="block button is-link is-outlined is-fullwidth" href="cdl.php">Visualizza altri Corsi di Laurea</a></li>
        </ul>
</form>
</div>
