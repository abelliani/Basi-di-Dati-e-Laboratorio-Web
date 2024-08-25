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
$user = $_GET['username'];
$premium = $_SESSION["premium_status"];
if (!$user) {
    $_SESSION['errore'] = "Nessun utente trovato";
    header("Location: ricerca.php");
    exit();
}
$select_query = "SELECT IdUtente, Username, FotoProfilo, N_Seguaci, N_Seguiti FROM Utente WHERE Username = '$user'";
$result = $mysqli->query($select_query);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_utente = $row["IdUtente"];
        $username = $row['Username'];
        $immagine = $row['FotoProfilo'];
        $n_seguaci = $row['N_Seguaci'];
        $n_seguiti = $row['N_Seguiti'];
    }
} else {
    $_SESSION['errore'] = "Utente non trovato.";
    header("Location: ricerca.php");
    exit();
}

$select_blog_query = 
    "SELECT 
        B.IdBlog, 
        B.TitoloBlog, 
        B.N_Follow, 
        GROUP_CONCAT(C.Tema SEPARATOR ', ') AS Temi
    FROM 
        Blog AS B
    NATURAL JOIN Associa
    NATURAL JOIN Categoria AS C
    WHERE 
        B.IdUtente = $id_utente
    GROUP BY 
        B.IdBlog";
$result_blog = $mysqli->query($select_blog_query);

$select_coautore_query = 
    "SELECT 
        B.IdBlog, 
        B.TitoloBlog, 
        B.N_Follow, 
        GROUP_CONCAT(C.Tema SEPARATOR ', ') AS Temi
    FROM 
        Coautore AS CO
    NATURAL JOIN Blog AS B
    NATURAL JOIN Associa
    NATURAL JOIN Categoria AS C
    WHERE 
        CO.IdCoautore = $id_utente
    GROUP BY 
        B.IdBlog";
$result_coautore = $mysqli->query($select_coautore_query);

$check_follow_query = "SELECT * FROM FollowUtente WHERE IdUtenteSeguace = $id_utente_loggato AND IdUtenteSeguito = $id_utente";
$is_following = $mysqli->query($check_follow_query)->num_rows > 0;

$check_premium = "SELECT * FROM Premium WHERE IdUtente = $id_utente";
$result_premium = $mysqli->query($check_premium);

?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LoDicoDaProfilo</title>
        <link rel="stylesheet" href="style/style.css">
        <link rel="stylesheet" href="style/style_user_profile.css">
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
        <script>
            $(document).ready(function() {
                // Funzione per gestire la sottomissione del form di follow/unfollow
                function handleFormSubmit(formSelector) {
                    $(document).on('submit', formSelector, function(e) {
                        e.preventDefault();
                        var form = $(this);
                        var formData = form.serialize();
                        var actionUrl = form.attr('action');

                        $.post(actionUrl, formData, function(response) {
                            // Aggiorna il contenuto di seguaci e il form di follow/unfollow
                            $('#follow-unfollow-container').html(response.html);

                            // Aggiorna direttamente il numero di seguaci
                            $('#n_follow').html('<p>' + response.followers + '</p>');
                        }, 'json');
                    });
                }

                // Gestisci la sottomissione del form di follow
                handleFormSubmit('#follow-form');

                // Gestisci la sottomissione del form di unfollow
                handleFormSubmit('#unfollow-form');
            });

        </script>
</head>
<body>
    <?php include("header.php"); ?>
    <main>
    <div class="container">
        <h3>Profilo:</h3>
        <div class="profile-header">
            <div class="profile-info">
                <img src="<?php echo ($immagine); ?>" alt="Foto Profilo" class="round-image" id="profile-image">
                <div id="username-info"><?php echo ($username); ?>
                    <?php if ($result_premium->num_rows > 0) : ?>
                        <i class='bx bxs-star'></i>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($id_utente_loggato != $id_utente) : ?>
                <div id="follow-unfollow-container">
                    <?php if ($is_following) : ?>
                        <form action="follow_utente.php" method="post" id="unfollow-form">
                            <input type="hidden" name="action" value="unfollow">
                            <input type="hidden" name="user_id" value="<?php echo $id_utente; ?>">
                            <button type="submit" class="unfollow-button">Unfollow</button>
                        </form>
                    <?php else : ?>
                        <form action="follow_utente.php" method="post" id="follow-form">
                            <input type="hidden" name="action" value="follow">
                            <input type="hidden" name="user_id" value="<?php echo $id_utente; ?>">
                            <button type="submit" class="follow-button">Follow</button>
                        </form>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <div class="stats-container">
                <div class="stat-box" id="seguaci-box">
                <?php if ($premium == "attivo") : ?>
                    <a href="seguaci.php?id=<?php echo $id_utente; ?>"><h4>Seguaci</h4></a>
                    <div id="n_follow"><p><?php echo ($n_seguaci); ?></p></div>
                <?php else : ?>
                    <h4>Seguaci</h4>
                    <div id="n_follow"><p><?php echo ($n_seguaci); ?></p></div>
                <?php endif; ?>
                </div>
                <div class="stat-box" id="seguiti-box">
                <?php if ($premium == "attivo") : ?>
                    <a href="seguiti.php?id=<?php echo $id_utente; ?>"><h4>Seguiti</h4></a>
                    <p><?php echo ($n_seguiti); ?></p>
                <?php else : ?>
                    <h4>Seguiti</h4>
                    <p><?php echo ($n_seguiti); ?></p>
                <?php endif; ?>
                </div>
            </div>
        </div>
        <hr>
        <h3>Blog Autore:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Titolo</th>
                        <th>Seguaci</th>
                        <th>Categorie</th>
                        <th>Gestisci</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result_blog->num_rows > 0): ?>
                    <?php while ($blog = $result_blog->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo ($blog['TitoloBlog']) ?></td>
                            <td><?php echo ($blog['N_Follow']) ?></td>
                            <td><?php echo ($blog['Temi']) ?></td>
                            <td>
                                <form method="post" action="blog_profile.php">
                                    <input type="hidden" name="blog_id" value="<?= $blog['IdBlog'] ?>">
                                    <button type="submit" class="edit-button">Visualizza</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Nessun blog trovato.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            <hr>
            <h3>Blog Coautore:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Titolo</th>
                        <th>Seguaci</th>
                        <th>Categorie</th>
                        <th>Gestisci</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    if ($result_coautore->num_rows > 0) : ?>
                        <?php while ($blog = $result_coautore->fetch_assoc()) : ?>
                            <tr>
                                <td> <?php echo $blog['TitoloBlog'] ?> </td>
                                <td> <?php echo $blog['N_Follow'] ?> </td>
                                <td> <?php echo $blog['Temi'] ?> </td>
                                <td>
                                <form method='post' action='blog_profile.php'>
                                <input type='hidden' name='blog_id' value='<?php echo $blog['IdBlog'] ?>'>
                                <button type='submit' class='edit-button'>Visualizza</button>
                                </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan='4'>Nessun blog trovato.</td>
                            </tr>
                    <?php endif; ?>
            </tbody>
        </table>
    <hr>
    </div>
    </main>
    <?php include("footer.php"); ?>
</body>
</html>