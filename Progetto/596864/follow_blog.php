<?php
session_start();
include("connessione.php");

if (!isset($_SESSION["idUtente"])) {
    $_SESSION['errore'] = 'Utente non loggato. Effettua il login per continuare.';
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["idUtente"];
$action = $_POST['action'];
$id_blog = $_POST['id_blog'];

if ($action === 'follow') {
    // Verifico se l'utente non sta già seguendo il blog
    $check_follow_query = "SELECT * FROM FollowBlog WHERE IdUtente = ? AND IdBlog = ?";
    $stmt = $mysqli->prepare($check_follow_query);
    $stmt->bind_param("ii", $id_utente, $id_blog);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['error' => 'Hai già seguito questo blog']);
        exit;
    }

    // Inserisco il follow nel database
    $insert_follow_query = "INSERT INTO FollowBlog (IdUtente, IdBlog) VALUES (?, ?)";
    $stmt = $mysqli->prepare($insert_follow_query);
    $stmt->bind_param("ii", $id_utente, $id_blog);
    $stmt->execute();
    $stmt->close();

    // Ottiengo il numero aggiornato di seguaci
    $count_followers_query = "SELECT N_Follow FROM Blog WHERE IdBlog = ?";
    $stmt = $mysqli->prepare($count_followers_query);
    $stmt->bind_param("i", $id_blog);
    $stmt->execute();
    $result = $stmt->get_result();
    $n_follow = $result->fetch_assoc()['N_Follow'];
    $stmt->close();

    // Restituisco il form HTML aggiornato per unfollow e il numero di seguaci
    $response = [
        'html' => '
        <form action="follow_blog.php" method="post" id="unfollow-form">
            <input type="hidden" name="action" value="unfollow">
            <input type="hidden" name="id_blog" value="' . $id_blog . '">
            <button type="submit" class="unfollow-button">Unfollow</button>
        </form>',
        'followers' =>  $n_follow
    ];

    echo json_encode($response);
} elseif ($action === 'unfollow') {
    // Elimino il follow dal database
    $delete_follow_query = "DELETE FROM FollowBlog WHERE IdUtente = ? AND IdBlog = ?";
    $stmt = $mysqli->prepare($delete_follow_query);
    $stmt->bind_param("ii", $id_utente, $id_blog);
    $stmt->execute();
    $stmt->close();

    // Ottiengo il numero aggiornato di seguaci
    $count_followers_query = "SELECT N_Follow FROM Blog WHERE IdBlog = ?";
    $stmt = $mysqli->prepare($count_followers_query);
    $stmt->bind_param("i", $id_blog);
    $stmt->execute();
    $result = $stmt->get_result();
    $n_follow = $result->fetch_assoc()['N_Follow'];
    $stmt->close();

    // Restituisco il form HTML aggiornato per follow e il numero di seguaci
    $response = [
        'html' => '
        <form action="follow_blog.php" method="post" id="follow-form">
            <input type="hidden" name="action" value="follow">
            <input type="hidden" name="id_blog" value="' . $id_blog . '">
            <button type="submit" class="follow-button">Follow</button>
        </form>',
        'followers' => $n_follow 
    ];

    echo json_encode($response);
}

?>
