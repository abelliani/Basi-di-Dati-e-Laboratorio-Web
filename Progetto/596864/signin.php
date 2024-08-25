<?php
session_start();
include('connessione.php');

if ($_SERVER["REQUEST_METHOD"] === "POST"){

    try {
        if(!isset($_POST["username"]) || !isset($_POST["password"])){
            throw new Exception("Compila tutti i campi");
        }

        $username = $_POST["username"];
        $password = $_POST["password"];
        $hashed_password = hash('sha256', $password);

        $select_query = $mysqli->prepare("SELECT * FROM Utente WHERE username = ? and password = ?");
        $select_query->bind_param('ss', $username, $hashed_password);
        $select_query->execute();
        $result = $select_query->get_result();
        if($result-> num_rows === 1) {
            $row = $result->fetch_assoc();
            $id_utente = $row['IdUtente'];

            $_SESSION["username"] = $username;
            $_SESSION["idUtente"] = $id_utente;
            
            $data_corrente = date('Y-m-d');
            $check_date_query = $mysqli->prepare("SELECT ScadenzaAbbonamento FROM Premium WHERE IdUtente = ?");
            $check_date_query->bind_param("i", $id_utente);
            $check_date_query->execute();
            $check_date_query->bind_result($scadenza_abbonamento);
            
            if ($check_date_query->fetch()) {
                if ($scadenza_abbonamento < $data_corrente) {

                    $delete_premium_query = $mysqli->prepare("DELETE FROM Premium WHERE IdUtente = ?");
                    $delete_premium_query->bind_param("i", $id_utente);
                    $delete_premium_query->execute();
                    $delete_premium_query->close();

                    // Imposta lo stato dell'abbonamento nella sessione
                    $_SESSION["premium_status"] = "scaduto";
                } else {
                    // L'abbonamento è ancora valido
                    $_SESSION["premium_status"] = "attivo";
                }
            } else {
                // L'utente non ha un abbonamento premium
                $_SESSION["premium_status"] = "none";
            }
            $check_date_query->close();
            $mysqli->close();
            header("Location: homepage.php");
            exit();
        } else {
            throw new Exception("Credenziali errate");
        }
    } catch (Exception $e){
        $_SESSION['errore'] = $e->getMessage();
        header("Location: login.php");
        exit();
    }
} else {
    echo "Richiesta non valida";
}