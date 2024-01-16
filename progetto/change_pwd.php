

<?php 

    include_once('header.php');
    include_once('funzioni.php');
    require('navbar.php');

    $error_msg = '';
    $success_msg =''; 


    
    if( isset($_POST) && isset($_POST['email']) && isset($_POST['password'])) {
     
        $db = open_pg_connection();
        $pwd = md5($_POST['password']);
        $sql = "UPDATE unidb.users
                SET password = '{$pwd}'
                WHERE email = '{$_POST['email']}' AND (email = '{$_SESSION['email']}' OR '{$_SESSION['usertype']}' = 'segreteria')
        ";

        $result = pg_query($db, $sql);

        if (!$result) {
            $error_msg = "errore nella modifica della password; controlla le credenziali e riprova";
        }else {
            $success_msg = "Password modificata con successo!";
        }

    }
?>
<?php 
//messaggio di successo se la password è stata modificata con successo
if (!empty($success_msg)) {
?>
 <div class="notification is-success is-light mt-6">
              <a>  <?php echo $success_msg; ?> </a>
            </div>
<?php
}

?>

<?php 
//messaggio di errore se c'è stato un errore di inserimento delle credenziali oppure la query è fallita
if (!empty($error_msg)) {
?>
<div class="notification is-danger is-light mt-6" uk-alert>
    <a uk-close></a>
    <p><?php echo $error_msg; unset($error_msg) ?></p>

</div>
<?php
}
?>

<div class="uk-container uk-margin-bottom uk-margin-top">

<div class = "text-align-center">
<h1 class="title mt-2">Modifica la password d'accesso</h1>
<h2 class="subtitle">Solo la segreteria può modificare la password altrui</h2>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST"> 

    <div class="field">
    <label class="label mt-5">Conferma email ateneo</label>
    <input class ="input" type = "email" value="<?php echo $_SESSION['email'] ?>" placeholder="email" name= "email" required>
</div>

<div class="field">
    <label class="label mt-5">Nuova password</label>
    <input class ="input" type = "password" placeholder="nuova password" name="password" required>
</div>

<p class="control">
    <input class="button is-link is-fullwidth is-medium" type="submit" name="submit" value="Modifica">
    </p>

</form>

