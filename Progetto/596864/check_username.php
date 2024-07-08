<?php
include 'connessione.php';

if (isset($_POST['username'])){
    $username = $_POST['username'];
    $check_query = $mysqli->prepare("SELECT * FROM Utente WHERE username = ?");
    $check_query->bind_param('s', $username);
    $check_query->execute();
    $result_query = $check_query->get_result();
    if ($result_query->num_rows > 0) {
        echo "non_disponibile";
    } else {
        echo "disponibile";
    }
    $check_query->close();
    $mysqli->close();
} else {
    echo "richiesta non valida";
}