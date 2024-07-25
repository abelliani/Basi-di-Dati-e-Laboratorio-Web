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
$id_utente = isset($_GET['id']) ? intval($_GET['id']) : 0;

$select_username_query = "SELECT Username FROM Utente WHERE IdUtente = ?";
$stmt_username = $mysqli->prepare($select_username_query);
$stmt_username->bind_param("i", $id_utente);
$stmt_username->execute();
$stmt_username->bind_result($username);
$stmt_username->fetch();
$stmt_username->close();

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$itemsPerPage = 5;
$offset = ($page - 1) * $itemsPerPage;

$select_query_seguiti = "SELECT Username, FotoProfilo 
                         FROM Utente 
                         JOIN FollowUtente 
                         ON Utente.IdUtente = FollowUtente.IdUtenteSeguito 
                         WHERE IdUtenteSeguito <> $id_utente 
                         AND IdUtenteSeguace = $id_utente 
                         LIMIT $itemsPerPage OFFSET $offset";
$result = $mysqli->query($select_query_seguiti);

if ($id_utente_loggato != $id_utente){
    $back_link = "user_profile.php?username=" . $username;
} else {
    $back_link = "profilo.php";
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoDicoDaSeguiti</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style_seguiti.css">
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
                echo "<a href='user_profile.php?username=" . $row['Username'] . "' class='view-profile-button'>Visualizza</a>";
                echo "</div>";
            }
        } else {
            echo "Non ci sono seguiti";
        }
        ?>
    </div>
    <?php

    $prevPage = $page - 1;
    $nextPage = $page + 1;

    echo "<div class='pagination'>";
    if ($page > 1) {
        echo "<a href='?page=$prevPage&id=$id_utente'>&#9664; Indietro</a>";
    }
    if ($result->num_rows > 0) {
        echo "<a href='?page=$nextPage&id=$id_utente'>Avanti &#9654;</a>";
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