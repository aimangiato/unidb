<?php 

	include_once ('funzioni.php'); 
    require('header.php');

    
    //$logged = null;

    session_start();

    $logged = null;

    // controllo il login
?>
<!DOCTYPE html>
    <html>
    <head>
        <title>PORTALE ATENEO</title>
    </head>
    <body>
    <div class="container is-max-desktop mt-6">
    
    <form class="box p-6" action="login.php" method="POST">
        <h1 class="title">UniDB</h1>
        <legend class="title mt-2">

        Inserisci le tue credenziali di accesso
        
        </legend>

            <div class="field">
            <label class="label mt-5">Email</label>
            <input class="input" type="email" placeholder="email" name="email">
            </div>

            <div class="field">
            <label class="label mt-5">Password</label>
            <input class="input" type="password" placeholder="password" name="password">
            </div>

            <div class="field">
            <label class="label mt-5">Tipo utente</label>
            <select class = "input" placeholder = "utente" name = "utente">
                <option value = "studente">studente</option>
                <option value = "docente">docente</option>
                <option value = "segreteria">segreteria</option>
            </select>
            </div>
        
            <div class="field"> </div>
    <p class="control">
    <input class="button is-link is-fullwidth is-medium" type="submit" name="submit" value="Accedi">
    </p>

        <a href="http://localhost/progetti/registrati/">Hai dimenticato la password?</a></p>
    </form>
    
	
	<?php
    if (!empty($_SESSION['error'])) {
    ?>
    
	
	<div class="notification is-danger is-light mt-6" uk-alert>
        <p><?php echo $_SESSION['error']; ?></p>
        <?php unset($_SESSION['error']); ?>
    </div>
    <?php
    }
    ?>
    </div>
    </div>
   
	</div>

</body>
</html>