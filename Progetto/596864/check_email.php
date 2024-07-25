<?php
include 'connessione.php';

if (isset($_POST['email'])){
    $email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        exit;
    }
    $check_query = $mysqli->prepare("SELECT * FROM Utente WHERE email = ?");
    $check_query->bind_param('s', $email);
    $check_query->execute();
    $result_query = $check_query->get_result();
    if ($result_query->num_rows > 0) {
        echo "registrata";
    } else {
        echo "non_registrata";
    }
    $check_query->close();
    $mysqli->close();
} else {
    echo "richiesta non valida";
}