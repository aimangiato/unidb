<?php
include_once('funzioni.php');
session_start();
session_destroy();
Redirect('redirector.php');
