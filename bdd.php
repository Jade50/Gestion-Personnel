<?php

    $dsn = 'mysql:host=127.0.0.1;dbname=gurdil;charset=utf8';
    $username = 'root';
    $password = '';

    $bdd = new PDO($dsn, $username , $password, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

?>