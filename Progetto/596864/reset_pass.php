<?php 
include 'connessione.php';

if ($_SERVER["REQUEST_METHOD"] == "POST"){

    try {
        if (!isset($_POST["username"]) || !isset($_POST["email"]) || !isset($_POST["password"]) || !isset($_POST["c_password"])){
            throw new Exception("Compila tutti i campi");
        } else {
            $username = $_POST["username"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $c_password = $_POST["c_password"];

            $select_query = $mysqli->prepare("SELECT * FROM utente WHERE username = ? and email = ?");
            $select_query->bind_param('ss', $username, $email);
            $select_query->execute();
            $result = $select_query->get_result();
            if (!$result){
                throw new Exception("Dati non corretti");
            }

            if ($password != $c_password){
                throw new Exception("Le password non coincidono");
            }

            $hashed_password = hash('sha256', $password);

            $update_query = $mysqli->prepare("UPDATE Utente SET password = ? WHERE username = ? and email = ?");
            $update_query->bind_param('sss', $hashed_password, $username, $email);
            $update_query->execute();
            $update_query->close();
            $mysqli->close();
            header("Location: home.php");
            exit();
        }

    } catch (Exception $e){
        echo $e->getMessage();
        $update_query->close();
        $mysqli->close();
    }    
} else {
    echo "Richiesta non valida";
}