<?php
session_start();
include('connessione.php');
echo 

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST"){
    $premium_status = $_SESSION["premium_status"];
    try {
        if (!isset($_POST["blog"]) || !isset($_POST["utente"])){
            throw new Exception("Compila tutti i campi");
        }
        $blog = mysqli_real_escape_string($mysqli, $_POST["blog"]);
        $utente = mysqli_real_escape_string($mysqli, $_POST["utente"]);

        if ($premium_status == "scaduto" || $premium_status == "none") { 
            $count_query = 
            "SELECT COUNT(*) AS count 
            FROM Coautore 
            WHERE IdBlog = $blog
            ";
            $result = $mysqli->query($count_query);
            $row = $result->fetch_assoc();

            if ($row['count'] >= 3) {
                throw new Exception("Gli utenti standard non possono aggiungere più di 3 coautori.");
            }
        }
        $check_query = "SELECT COUNT(*) AS count FROM Coautore WHERE IdCoautore = '$utente' AND IdBlog = '$blog'";
        $result = $mysqli->query($check_query);
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            throw new Exception("Hai già aggiunto questo coautore al blog.");
        } else {
            $insert_query = "INSERT INTO Coautore(IdCoautore, IdBlog) VALUES ($utente, $blog)";
        }
        
        if ($mysqli->query($insert_query) === TRUE) {
                $message = "Coautore aggiunto con successo.";
                $_SESSION['message'] = $message;
                header("Location: newcoautore.php");
                exit();
        } else {
                throw new Exception("Errore durante l'associazione");
            }           
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $_SESSION['message'] = $message;
        header("Location: newcoautore.php");
        exit();
    }
} else {
    $message = "Richiesta non valida";
    $_SESSION['message'] = $message;
    header("Location: newcoautore.php");
    exit();
}
?>