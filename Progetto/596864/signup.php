<?php 
include('connessione.php');

if ($_SERVER["REQUEST_METHOD"] == "POST"){

    try {
        if (!isset($_POST["username"]) || !isset($_POST["email"]) || !isset($_POST["password"]) || !isset($_POST["c_password"])){
            throw new Exception("Compila tutti i campi");
        }

        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $c_password = $_POST["c_password"];

        if ($password != $c_password){
            throw new Exception("Le password non coincidono");
        }

        $hashed_password = hash('sha256', $password);

        $insert_query = $mysqli->prepare("INSERT INTO Utente(username, email, password) VALUES (?, ?, ?)");
        $insert_query->bind_param('sss', $username, $email, $hashed_password);
        $insert_query->execute();
        $insert_query->close();
        $mysqli->close();
        header("Location: login.php");
        exit();

    } catch (Exception $e){
        echo $e->getMessage();
    }   
    $mysqli->close(); 
} else {
    echo "Richiesta non valida";
}