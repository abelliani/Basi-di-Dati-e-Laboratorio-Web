<?php
session_start();
include("connessione.php");

if (!isset($_SESSION["idUtente"])) {
    echo 'Utente non loggato. Effettua il login per continuare.';
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['blog_id'])) {
    $id_blog = $_POST['blog_id'];

    // Elimina il blog dal database
    $delete_query = $mysqli->prepare("DELETE FROM Blog WHERE IdBlog = ?");
    $delete_query->bind_param("i", $id_blog);

    if ($delete_query->execute()) {
        echo 'success'; // Se l'eliminazione ha successo
    } else {
        echo 'Errore durante l\'eliminazione del blog: ' . $mysqli->error;
    }

    $delete_query->close();
} else {
    echo 'Richiesta non valida.';
}
?>
