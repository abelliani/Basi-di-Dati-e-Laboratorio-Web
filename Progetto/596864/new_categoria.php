<?php
session_start();
include('connessione.php');

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST"){
    $premium_status = $_SESSION["premium_status"];
    try {
        if (!isset($_POST["blog"]) || !isset($_POST["categoria"])){
            throw new Exception("Compila tutti i campi");
        }
        $blog = mysqli_real_escape_string($mysqli, $_POST["blog"]);
        $categoria = mysqli_real_escape_string($mysqli, $_POST["categoria"]);

        if ($premium_status == "scaduto" || $premium_status == "none") { 
            $count_query = 
            "SELECT COUNT(*) AS count 
            FROM Associa 
            WHERE IdBlog = $blog
            ";
            $result = $mysqli->query($count_query);
            $row = $result->fetch_assoc();

            if ($row['count'] >= 5) {
                throw new Exception("Gli utenti standard non possono aggiungere più di 5 categorie.");
            }
        }
        $check_query = "SELECT COUNT(*) AS count FROM Associa WHERE IdCategoria = '$categoria' AND IdBlog = '$blog'";
        $result = $mysqli->query($check_query);
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            throw new Exception("Hai già associato questa categoria al blog.");
        } else {
            $insert_query = "INSERT INTO Associa(IdBlog, IdCategoria) VALUES ($blog, $categoria)";
        }
        
        if ($mysqli->query($insert_query) === TRUE) {
                $message = "Sottocategoria aggiunta con successo.";
                $_SESSION['message'] = $message;
                header("Location: sottocategoria.php");
                exit();
        } else {
                throw new Exception("Errore durante l'associazione");
            }           
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $_SESSION['message'] = $message;
        header("Location: sottocategoria.php");
        exit();
    }
} else {
    $message = "Richiesta non valida";
    $_SESSION['message'] = $message;
    header("Location: sottocategoria.php");
    exit();
}