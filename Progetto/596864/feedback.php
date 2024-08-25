<?php
session_start();
include("connessione.php");

header('Content-Type: application/json');

$response = array();

if (!isset($_SESSION["idUtente"])) {
    $response['error'] = 'Utente non loggato. Effettua il login per continuare.';
    echo json_encode($response);
    exit;
}

$id_utente = $_SESSION["idUtente"];
$action = $_GET['action'];
$id_post = $_GET['post_id'];

try {
    if ($action == 'like') {
        // Esegui l'aggiornamento del feedback per il like
        $update_feedback_query = "UPDATE Feedback SET Tipo = 1 WHERE IdUtente = ? AND IdPost = ?";
        $stmt = $mysqli->prepare($update_feedback_query);
        $stmt->bind_param("ii", $id_utente, $id_post);
        if ($stmt->execute()) {
            // Se l'aggiornamento ha avuto successo, ottieni il numero aggiornato di like
            $like_count_query = "SELECT N_Like FROM Post WHERE IdPost = ?";
            $stmt = $mysqli->prepare($like_count_query);
            $stmt->bind_param("i", $id_post);
            $stmt->execute();
            $stmt->bind_result($n_like);
            $stmt->fetch();
            $stmt->close();

            // Prepara la risposta JSON per il like
            $response['success'] = true;
            $response['html'] = '
                <form action="feedback.php" method="get" id="unlike-form">
                    <input type="hidden" name="action" value="unlike">
                    <input type="hidden" name="id_utente" value="' . $id_utente . '">
                    <input type="hidden" name="post_id" value="' . $id_post . '">
                    <button type="submit" class="unlike-button">Non mi piace</button>
                </form>';
            $response['N_Like'] = $n_like;
        } else {
            // Se c'è un errore durante il like
            $response['success'] = false;
            $response['error'] = 'Errore durante il like';
        }
    } elseif ($action == 'unlike') {
        // Esegui l'aggiornamento del feedback per l'unlike
        $update_feedback_query = "UPDATE Feedback SET Tipo = 0 WHERE IdUtente = ? AND IdPost = ?";
        $stmt = $mysqli->prepare($update_feedback_query);
        $stmt->bind_param("ii", $id_utente, $id_post);
        if ($stmt->execute()) {
            // Se l'aggiornamento ha avuto successo, ottieni il numero aggiornato di like
            $like_count_query = "SELECT N_Like FROM Post WHERE IdPost = ?";
            $stmt = $mysqli->prepare($like_count_query);
            $stmt->bind_param("i", $id_post);
            $stmt->execute();
            $stmt->bind_result($n_like);
            $stmt->fetch();
            $stmt->close();

            // Prepara la risposta JSON per l'unlike
            $response['success'] = true;
            $response['html'] = '
                <form action="feedback.php" method="get" id="like-form">
                    <input type="hidden" name="action" value="like">
                    <input type="hidden" name="id_utente" value="' . $id_utente . '">
                    <input type="hidden" name="post_id" value="' . $id_post . '">
                    <button type="submit" class="like-button">Mi piace</button>
                </form>';
            $response['N_Like'] = $n_like;
        } else {
            // Se c'è un errore durante l'unlike
            $response['success'] = false;
            $response['error'] = 'Errore durante il dislike';
        }
    } else {
        // Se l'azione non è supportata
        $response['success'] = false;
        $response['error'] = 'Azione non supportata';
    }
} catch (Exception $e) {
    // Se c'è un'eccezione generica
    $response['success'] = false;
    $response['error'] = $e->getMessage();
}

// Restituisci la risposta JSON al client
echo json_encode($response);
exit;
?>
