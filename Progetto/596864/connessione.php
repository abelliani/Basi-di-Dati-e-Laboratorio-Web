<?php
    $mysqli = new mysqli('localhost', 'root', '', 'PROGETTO596864');
    if ($mysqli->connect_error){
        die ('Connessione fallita:' . $mysqli->connect_errno . ' - ' . $mysqli->connect_error);
    }
?>