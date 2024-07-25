<?php
session_start();
include("connessione.php");

$errore = '';

if (isset($_SESSION['errore'])) {
    $errore = $_SESSION['errore'];
    unset($_SESSION['errore']);
}

if (!isset($_SESSION["idUtente"])) {
    $_SESSION['errore'] = 'Utente non loggato. Effettua il login per continuare.';
    header("Location: login.php");
    exit;
}

$id_utente_loggato = $_SESSION["idUtente"];
$id_utente = isset($_GET['id_utente']) ? intval($_GET['id_utente']) : 0;
$id_post = isset($_GET['id_post']) ? intval($_GET['id_post']) : 0;
$action = isset($_GET["action"]) ? $_GET["action"] : '';

$select_titolo_post_query = "SELECT TitoloPost FROM Post WHERE IdPost = ?";
$stmt_titolo_post = $mysqli->prepare($select_titolo_post_query);
$stmt_titolo_post->bind_param("i", $id_post);
$stmt_titolo_post->execute();
$stmt_titolo_post->bind_result($titolo_post);
$stmt_titolo_post->fetch();
$stmt_titolo_post->close();

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 5;
$offset = ($page - 1) * $itemsPerPage;

// Prepara la query base per ottenere i follower o i visualizzatori
$select_query_feed = "SELECT Username, FotoProfilo 
                      FROM Utente 
                      JOIN Feedback ON Utente.IdUtente = Feedback.IdUtente
                      JOIN Post ON Feedback.IdPost = Post.IdPost
                      WHERE Post.IdPost = ?";

// Aggiungi condizione per il tipo di feedback (like o view)
if ($action == 'like') {
    $select_query_feed .= " AND Feedback.Tipo = 1";
} elseif ($action == "views") {
    $select_query_feed .= " AND Feedback.Tipo = 0";
}

// Aggiungi LIMIT e OFFSET per la paginazione
$select_query_feed .= " LIMIT ? OFFSET ?";

$stmt = $mysqli->prepare($select_query_feed);
$stmt->bind_param("iii", $id_post, $itemsPerPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($id_utente_loggato != $id_utente){
    $back_link = "post_profile.php?titolo_post=" . urlencode($titolo_post);
} else {
    $back_link = "visualizza_post.php?post_id=$id_post";
}
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LoDicoDaProfilo</title>
        <link rel="stylesheet" href="style/style.css">
        <link rel="stylesheet" href="style/style_vedi_feedback.css">
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    </head>
    <body>
        <?php include("header.php"); ?>
        <div class="follower-list">
            <?php 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='follower-row'>";
                    echo "<img class='round-image' alt='foto' src='" . $row['FotoProfilo'] . "'>";
                    echo "<div class='follower-info'>" . $row['Username'] . "</div>";
                    echo "<a href='user_profile.php?username=" . urlencode($row['Username']) . "' class='view-profile-button'>View Profile</a>";
                    echo "</div>";
                }
            } else {
                echo "Non ci sono interazioni";
            }
            ?>
        </div>
        <?php
        $prevPage = $page - 1;
        $nextPage = $page + 1;

        echo "<div class='pagination'>";
        if ($page > 1) {
            echo "<a href='?page=$prevPage&id_post=$id_post&action=$action'>&#9664; Indietro</a>";
        }
        if ($result->num_rows >= $itemsPerPage) {
            echo "<a href='?page=$nextPage&id_post=$id_post&action=$action'>Avanti &#9654;</a>";
        }
        echo "</div>";
        ?>
        <div class="back-link">
            <a href="<?php echo $back_link; ?>">Torna Indietro</a>
        </div>
        <?php include("footer.php"); ?>
    </body>
</html>

<?php
$result->close();
$mysqli->close();
?>