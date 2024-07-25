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

$id_blog = isset($_GET['id_blog']) ? intval($_GET['id_blog']) : null;
$id_utente = isset($_GET["id_utente"]) ? intval($_GET['id_utente']) : null;

$select_coautore_query = $mysqli->prepare("SELECT IdCoautore 
    FROM Coautore 
    NATURAL JOIN Blog 
    WHERE IdBlog = ?
");
$select_coautore_query->bind_param("i", $id_blog);
$select_coautore_query->execute();
$result = $select_coautore_query->get_result();

$id_coautore = null;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_coautore = $row["IdCoautore"];
    }
}
$select_coautore_query->close();

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$itemsPerPage = 5;
$offset = ($page - 1) * $itemsPerPage;

$select_query_seguaci = $mysqli->prepare("SELECT Utente.Username, Utente.FotoProfilo 
    FROM Utente
    JOIN FollowBlog ON Utente.IdUtente = FollowBlog.IdUtente
    WHERE FollowBlog.IdBlog = ?
    LIMIT ? OFFSET ?");
if (!$select_query_seguaci) {
    die("Preparazione query fallita: " . $mysqli->error);
}
$select_query_seguaci->bind_param("iii", $id_blog, $itemsPerPage, $offset);
$select_query_seguaci->execute();
$result = $select_query_seguaci->get_result();

if ($id_utente_loggato != $id_utente && $id_utente != $id_coautore) {
    $back_link = "blog_profile.php?blog_id=" . $id_blog;
} else {
    $back_link = "visualizza_blog.php?blog_id=" . $id_blog;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoDicoDaProfilo</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style_seguaci_blog.css">
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
                echo "Non ci sono seguaci";
            }
            ?>
        </div>
        <?php

        $prevPage = $page - 1;
        $nextPage = $page + 1;

        echo "<div class='pagination'>";
        if ($page > 1) {
            echo "<a href='?page=$prevPage&id_blog=$id_blog&id_utente=$id_utente'>&#9664; Indietro</a>";
        }
        if ($result->num_rows > 0) {
            echo "<a href='?page=$nextPage&id_blog=$id_blog&id_utente=$id_utente'>Avanti &#9654;</a>";
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