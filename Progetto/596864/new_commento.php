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
$post_id = $_POST['post_id'];
$commento = $_POST['commento'];
$datetime = date("Y-m-d H:i:s");

try {
    // Inserisci il commento nel database
    $insert_query = $mysqli->prepare("INSERT INTO Commento(IdUtente, IdPost, Testo, Data) VALUES (?, ?, ?, ?)");
    $insert_query->bind_param("iiss", $id_utente, $post_id, $commento, $datetime);
    $insert_query->execute();

    if ($insert_query->affected_rows > 0) {
        // Ottieni il numero aggiornato di commenti per il post
        $select_counts_query = "SELECT N_Commenti FROM Post WHERE IdPost = ?";
        $stmt = $mysqli->prepare($select_counts_query);
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->bind_result($n_comments);
        $stmt->fetch();
        $stmt->close();

        // Prepara la risposta JSON con l'HTML aggiornato dei commenti e il numero di commenti
        $response['success'] = true;
        $response['N_Commenti'] = $n_comments;
    } else {
        $response['success'] = false;
        $response['error'] = 'Errore durante l\'inserimento del commento.';
    }
} catch (Exception $e) {
    // Gestione delle eccezioni
    $response['success'] = false;
    $response['error'] = $e->getMessage();
}

// Restituisci la risposta JSON al client
echo json_encode($response);
exit;
?>
