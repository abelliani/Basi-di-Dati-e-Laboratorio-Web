<?php
session_start();
include("connessione.php");

if (!isset($_SESSION["idUtente"])) {
    $_SESSION['errore'] = 'Utente non loggato. Effettua il login per continuare.';
    header("Location: login.php");
    exit;
}

$id_utente_loggato = $_SESSION["idUtente"];
$action = $_POST['action'];
$user_id = $_POST['user_id'];

if ($action == 'follow') {
    $insert_follow_query = "INSERT INTO FollowUtente (IdUtenteSeguace, IdUtenteSeguito) VALUES (?, ?)";
    $stmt = $mysqli->prepare($insert_follow_query);
    $stmt->bind_param("ii", $id_utente_loggato, $user_id);
    $stmt->execute();
    $stmt->close();

    // Ottengo il numero aggiornato di seguaci
    $count_followers_query = "SELECT N_Seguaci FROM Utente WHERE IdUtente = ?";
    $stmt = $mysqli->prepare($count_followers_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $followers = $result->fetch_assoc()['N_Seguaci'];
    $stmt->close();

} elseif ($action == 'unfollow') {
    $delete_follow_query = "DELETE FROM FollowUtente WHERE IdUtenteSeguace = ? AND IdUtenteSeguito = ?";
    $stmt = $mysqli->prepare($delete_follow_query);
    $stmt->bind_param("ii", $id_utente_loggato, $user_id);
    $stmt->execute();
    $stmt->close();

    // Ottengo il numero aggiornato di seguaci
    $count_followers_query = "SELECT N_Seguaci FROM Utente WHERE IdUtente = ?";
    $stmt = $mysqli->prepare($count_followers_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $followers = $result->fetch_assoc()['N_Seguaci'];
    $stmt->close();
}

// Costruisco la risposta JSON
$response = [
    'html' => '',
    'followers' => $followers
];

// Genero l'HTML per il form di follow/unfollow in base allo stato attuale
if ($action == 'follow') {
    $response['html'] = '
    <form action="follow_utente.php" method="post" id="unfollow-form">
        <input type="hidden" name="action" value="unfollow">
        <input type="hidden" name="user_id" value="' . $user_id . '">
        <button type="submit" class="unfollow-button">Unfollow</button>
    </form>';
} elseif ($action == 'unfollow') {
    $response['html'] = '
    <form action="follow_utente.php" method="post" id="follow-form">
        <input type="hidden" name="action" value="follow">
        <input type="hidden" name="user_id" value="' . $user_id . '">
        <button type="submit" class="follow-button">Follow</button>
    </form>';
}

echo json_encode($response);

?>
