<?php
session_start();
include("connessione.php");

if (!isset($_SESSION["idUtente"])) {
    $_SESSION['errore'] = 'Utente non loggato. Effettua il login per continuare.';
    header("Location: login.php");
    exit;
}

if (isset($_POST['post_id']) && isset($_POST['blog_id'])) {
    $id_post = $_POST['post_id'];
    $id_blog = $_POST['blog_id'];

    // Eliminazione del post
    $delete_post_query = "DELETE FROM Post WHERE IdPost = ?";
    $stmt = $mysqli->prepare($delete_post_query);
    $stmt->bind_param("i", $id_post);

    if ($stmt->execute()) {
        echo 'success'; // Invia 'success' se l'eliminazione ha avuto successo
    } else {
        echo 'failure'; // Invia 'failure' se c'è stato un errore durante l'eliminazione
    }

    $stmt->close();
    exit;
} else {
    $_SESSION['errore'] = "Richiesta non valida.";
    header("Location: visualizza_blog.php"); // Redirect alla pagina del blog o altro
    exit;
}
?>
