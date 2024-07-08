<?php
session_start();
include('connessione.php');

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        if (!isset($_POST["blog"]) || !isset($_POST["utente"])) {
            throw new Exception("Compila tutti i campi");
        }

        $blog = mysqli_real_escape_string($mysqli, $_POST["blog"]);
        $utente = mysqli_real_escape_string($mysqli, $_POST["utente"]);

        $delete_query = "DELETE FROM Coautore WHERE IdCoautore = $utente AND IdBlog = $blog";
        
        if ($mysqli->query($delete_query) === TRUE) {
            $response['success'] = true;
            $response['message'] = "Coautore eliminato con successo.";
        } else {
            throw new Exception("Errore durante l'eliminazione");
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
} else {
    $response['message'] = "Richiesta non valida";
}

echo json_encode($response);
?>
