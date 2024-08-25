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
$premium = $_SESSION["premium_status"];
$blog_id = isset($_POST['blog_id']) ? $_POST['blog_id'] : (isset($_GET['blog_id']) ? $_GET['blog_id'] : null);
$titolo = isset($_GET['titolo_blog']) ? $_GET['titolo_blog'] : null;
$id_utente_loggato = isset($_SESSION['idUtente']) ? $_SESSION['idUtente'] : null;

if (!$titolo && !$blog_id) {
    $_SESSION['errore'] = "Nessun blog trovato";
    exit();
}

if ($blog_id) {
    $query = $mysqli->prepare("SELECT * FROM Blog WHERE IdBlog = ?");
    $query->bind_param("i", $blog_id);
} else {
    $query = $mysqli->prepare("SELECT * FROM Blog WHERE TitoloBlog = ?");
    $query->bind_param("s", $titolo);
}
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $blog = $result->fetch_assoc();
    $id_utente = $blog["IdUtente"];
    $id_blog = $blog['IdBlog'];
    $titolo_blog = $blog['TitoloBlog'];
    $n_follow = $blog['N_Follow'];
    $descrizione = $blog['Descrizione'];
    $immagine = $blog['Immagine'];
} else {
    $_SESSION['errore'] = "Blog non trovato.";
    header("Location: ricerca.php");
    exit();
}

// ottengo post del blog
$select_query_post = $mysqli->prepare("SELECT * FROM Post NATURAL JOIN Blog WHERE IdBlog = ?");
$select_query_post->bind_param("i", $id_blog);
$select_query_post->execute();
$result_post = $select_query_post->get_result();

// ottengo coautore
$select_coautore_query = $mysqli->prepare("SELECT *
FROM Post
INNER JOIN Coautore ON Post.IdUtente = Coautore.IdCoautore AND Post.IdBlog = Coautore.IdBlog WHERE Coautore.IdBlog = $id_blog");
$select_coautore_query->execute();
$result_coautore = $select_coautore_query->get_result();

// controllo se l'utente è follow del blog
$check_follow_query = $mysqli->prepare("SELECT * FROM FollowBlog WHERE IdUtente = ? AND IdBlog = ?");
$check_follow_query->bind_param("ii", $id_utente_loggato, $id_blog);
$check_follow_query->execute();
$is_following = $check_follow_query->get_result()->num_rows > 0;

$query->close();
$select_query_post->close();
$select_coautore_query->close();
$check_follow_query->close();
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LoDicoDaBlog</title>
        <link rel="stylesheet" href="style/style.css">
        <link rel="stylesheet" href="style/style_blog_profile.css">
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
                <h3>Blog:</h3>
                    <div class="blog-header">
                        <div id="blog-info">
                        <img src="<?php echo $immagine; ?>" alt="Immagine Blog" class="blog-image" id="blog-image">
                        <div id="blog-title"><?php echo $titolo_blog; ?></div>
                        <?php if ($id_utente_loggato != $id_utente) : ?>
                            <div id="follow-unfollow-container">
                                <?php if ($is_following) : ?>
                                    <form action="follow_blog.php" method="post" id="unfollow-form">
                                        <input type="hidden" name="action" value="unfollow">
                                        <input type="hidden" name="id_blog" value="<?php echo $id_blog; ?>">
                                        <button type="submit" class="unfollow-button">Unfollow</button>
                                    </form>
                                <?php else : ?>
                                    <form action="follow_blog.php" method="post" id="follow-form">
                                        <input type="hidden" name="action" value="follow">
                                        <input type="hidden" name="id_blog" value="<?php echo $id_blog; ?>">
                                        <button type="submit" class="follow-button">Follow</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?></div>
                        <div class="stats-container">
                            <div class="stat-box">
                            <?php if ($premium == "attivo") : ?>
                                <a href="seguaci_blog.php?id_blog=<?php echo $id_blog; ?>&id_utente=<?php echo $id_utente; ?>"><h4>Seguaci</h4></a>
                                <div id="n_follow"><p><?php echo $n_follow; ?></p></div>
                                <?php else : ?>
                                    <h4>Seguaci</h4>
                                    <div id="n_follow"><p><?php echo $n_follow; ?></p></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

            <hr>
            <h3>Post Autore:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Titolo</th>
                        <th>Data</th>
                        <th>Gestisci</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result_post->num_rows > 0) : ?>
                        <?php while ($post = $result_post->fetch_assoc()) : ?>
                            <?php $data_mysql = $post['Data'];
                            $data_time = new DateTime($data_mysql);
                            $data_format_italiano = $data_time->format('d/m/Y H:i:s'); ?>
                            <tr>
                                <td> <?php echo $post['TitoloPost'] ?> </td>
                                <td> <?php echo $data_format_italiano ?> </td>
                                <td>
                                    <form method='get' action='post_profile.php'>
                                    <input type='hidden' name='post_id' value='<?php echo $post['IdPost'] ?>'>
                                    <button type='submit' class='edit-button'>Visualizza</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">Nessun post trovato.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <hr>
            <h3>Post Coautore:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Titolo</th>
                        <th>Data</th>
                        <th>Gestisci</th>
                        <?php if ($result_coautore->num_rows > 0) : ?>
                            <th>Elimina</th>
                            <?php while ($post = $result_coautore->fetch_assoc()) : ?>
                                <?php if ($post['IdUtente'] == $id_utente_loggato) : ?>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_coautore->num_rows > 0) : ?>
                        <?php while ($post = $result_coautore->fetch_assoc()) : ?>
                            <?php 
                            $data_mysql = $post['Data'];
                            $data_time = new DateTime($data_mysql);
                            $data_format_italiano = $data_time->format('d/m/Y H:i:s');
                            ?>
                            <tr>
                                <td><?php echo ($post['TitoloPost']) ?></td>
                                <td><?php echo ($data_format_italiano) ?></td>
                                <td>
                                    <?php if ($post['IdUtente'] == $id_utente_loggato) : ?>
                                        <form method='post' action='visualizza_post.php'>
                                            <input type='hidden' name='post_id' value='<?= $post['IdPost'] ?>'>
                                            <button type='submit' class='edit-button'>Visualizza</button>
                                        </form>
                                    <?php else: ?>
                                        <form method='post' action='post_profile.php' style='display:inline;'>
                                            <input type='hidden' name='post_id' value='<?= $post['IdPost'] ?>'>
                                            <button type='submit' class='edit-button'>Visualizza</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <?php if ($post['IdUtente'] == $id_utente_loggato) : ?>
                                    <td>
                                        <form method='post' action='elimina_post.php' style='display:inline;' onsubmit='return confirm("Sei sicuro di voler eliminare questo post?");'>
                                            <input type='hidden' name='blog_id' value='<?= $id_blog ?>'>
                                            <input type='hidden' name='post_id' value='<?= $post['IdPost'] ?>'>
                                            <button type='submit' class='delete-button'>Elimina</button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Nessun post trovato.</td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
            <hr>
            <div class="back-link">
                <a href="javascript:history.back()">Torna indietro</a>
            </div>
        </div>
        </main>
        <?php include("footer.php"); ?>
    </body>
</html>