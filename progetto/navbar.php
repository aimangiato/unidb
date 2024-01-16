<!-- component to be included by other files -->
<?php 
    session_start();
?>
<head>
</head>
<body class="has-background-dark has-text-light">
<nav class="c-navbar navbar is-spaced">
  <div class="navbar-brand">
    <a class="navbar-item" href="<?php echo $_SESSION['home']?>">
      <h1 class="title">UniDB</h1>
    </a>
  </div>

  <div class="navbar-menu">
    <div class="navbar-start ml-4">
      <a class="navbar-item" href="<?php echo $_SESSION['home']?>">Home</a>
    </div>
    <div class="navbar-start ml-4">
      <p class="navbar-item">
        Autenticato come <?= $_SESSION['usertype']  ?>:
        <strong>&nbsp;<?= $_SESSION['email'] ?></strong>
      </p>
    </div>

    <div class="navbar-end">
      <div class="navbar-item">
        <div class="buttons">
          <a class="button is-link" href="logout.php">
            <strong>Logout</strong>
          </a>
        </div>
      </div>
    </div>
  </div>
</nav>
<body class="has-background-dark has-text-light">