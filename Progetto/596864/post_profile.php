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
$id_utente = $_SESSION["idUtente"];
$premium = $_SESSION["premium_status"];
$id_post = isset($_POST['post_id']) ? $_POST['post_id'] : (isset($_GET['post_id']) ? $_GET['post_id'] : null);
$titolo = isset($_GET['titolo_post']) ? $_GET['titolo_post'] : null;

if (!$titolo && !$id_post) {
    $_SESSION['errore'] = "Nessun post trovato";
    exit();
}

if ($id_post) {
    $query = $mysqli->prepare("SELECT * FROM Post WHERE IdPost = ?");
    $query->bind_param("i", $id_post);
} else {
    $query = $mysqli->prepare("SELECT * FROM Post WHERE TitoloPost = ?");
    $query->bind_param("s", $titolo);
}

$query->execute();
$result = $query->get_result();
if ($result->num_rows > 0) { 
    $post = $result->fetch_assoc();
    $post_id = $post["IdPost"];
    $id_blog = $post["IdBlog"];
    $titolo_post = $post['TitoloPost'];
    $testo = $post['Testo'];
    $n_like = $post['N_Like'];
    $n_view = $post['N_View'];
    $n_commenti = $post['N_Commenti'];
}  else {
    $_SESSION['errore'] = "Post non trovato.";
    header("Location: ricerca.php");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$itemsPerPage = 1;
$offset = ($page - 1) * $itemsPerPage;

$query_immagini = "SELECT * FROM Immagine WHERE IdPost = $post_id  LIMIT $itemsPerPage OFFSET $offset";
$result_img = $mysqli->query($query_immagini);

$check_feed_query = $mysqli->prepare("SELECT * FROM Feedback WHERE IdUtente = ? AND IdPost = ? AND Tipo = 1");
$check_feed_query->bind_param("ii", $id_utente, $post_id);
$check_feed_query->execute();
$is_feed = $check_feed_query->get_result()->num_rows > 0;

$check_view_query = $mysqli->prepare("SELECT * FROM Feedback WHERE IdUtente = ? AND IdPost = ?");
$check_view_query->bind_param("ii", $id_utente, $post_id);
$check_view_query->execute();
$result_view = $check_view_query->get_result();

if ($result_view->num_rows == 0) {
    $insert_view_query = $mysqli->prepare("INSERT INTO Feedback(IdUtente, IdPost, Tipo) VALUES (?, ?, 0)");
    $insert_view_query->bind_param("ii", $id_utente, $post_id);
    $insert_view_query->execute();
}

?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LoDicoDaPost</title>
        <link rel="stylesheet" href="style/style.css">
        <link rel="stylesheet" href="style/style_post_profile.css">
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#show-comments-form').submit(function(e) {
                    e.preventDefault();
                    var post_id = $(this).find('input[name="post_id"]').val();
                    var $button = $(this).find('button[type="submit"]');

                    // Controlla se il contenitore del commento è ancora visibile
                    if ($('#comments-container').hasClass('hidden')) {
                        // Esegui una richiesta AJAX per ottenere i commenti dal server
                        $.get('visualizza_commenti.php', { post_id: post_id }, function(response) {
                            // Mostra i commenti nella sezione nascosta e cambia il testo del pulsante
                            $('#comments-container').html(response).removeClass('hidden');
                            $button.text('Nascondi').addClass('hide-comments-button');;
                        });
                    } else {
                        // Nascondi i commenti se sono già visibili e cambia il testo del pulsante
                        $('#comments-container').addClass('hidden').html('');
                        $button.text('Visualizza').removeClass('hide-comments-button');;
                    }
                });
            });

            $(document).ready(function() {
                var maxChars = 255; // Numero massimo di caratteri

                // Aggiorna il contatore inizialmente
                $('#charCount').text(maxChars + ' caratteri rimanenti');

                // Aggiungi un event listener per l'input sul campo di testo
                $('#post-commento').on('input', function() {
                        var currentLength = $(this).val().length;
                        var charsRemaining = maxChars - currentLength;

                        if (charsRemaining < 0) {
                            charsRemaining = 0;
                            $(this).val($(this).val().substring(0, maxChars)); // Troncamento del testo a maxChars caratteri
                        }

                    $('#charCount').text(charsRemaining + ' caratteri rimanenti');
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                function handleFormSubmit(formSelector) {
                    $(document).on('submit', formSelector, function(e) {
                        e.preventDefault();
                        var form = $(this);
                        var formData = form.serialize();
                        var actionUrl = form.attr('action');

                        $.get(actionUrl, formData, function(response) {
                            if (response.error) {
                                alert('Errore: ' + response.error);
                            } else {
                                $('#like-unlike-container').html(response.html);
                                $('#n_like').html('<h4>' + response.N_Like + ' mi piace ' + '</h4>');
                            }
                        }, 'json');
                    });
                }

                // Gestisci la sottomissione del form di like
                handleFormSubmit('#like-form');

                // Gestisci la sottomissione del form di unlike
                handleFormSubmit('#unlike-form');
            });

            $(document).ready(function() {
                // Funzione per gestire l'invio del modulo del commento
                $('#comment-form').submit(function(e) {
                    e.preventDefault();
                    var form = $(this);
                    var formData = form.serialize();
                    var actionUrl = form.attr('action');

                    $.post(actionUrl, formData, function(response) {
                        if (response.error) {
                            alert('Errore: ' + response.error);
                        } else {
                            // Se il commento è stato aggiunto con successo, svuota il textarea
                            $('#post-commento').val('');

                            // Aggiorna l'area dei commenti
                            $('#comment-section').html(response.html);

                            // Aggiorna il numero di commenti
                            $('#n_commenti').html('<h4>' + response.N_Commenti + ' commenti</h4>');
                        }
                    }, 'json');
                });
            });
        </script>
    </head>
    <body>
        <?php include("header.php"); ?>
        <main>
        <div class="container">
            <h3>Post:</h3>
            <div class="post-header">
                <div id="post-info">
                    <div id="post-title"><?php echo $titolo_post; ?></div>
                    <br>
                    <?php 
                    $prevPage = $page - 1;
                    $nextPage = $page + 1;
                    $defaultImage = 'immagini/placeholder.png';  // percorso dell'immagine
                    
                    if ($result_img->num_rows > 0) {
                        while ($img = $result_img->fetch_assoc()) {
                            $immagine = $img["Immagine"];
                            $id_img = $img["IdImmagine"];
                            echo '<img src="' . $immagine . '" alt="Immagine Post" class="post-image" id="post-image" data-id="' . $id_img . '">';
                        } 
                    
                        echo "<div class='pagination'>";
                        if ($page > 1) {
                            echo "<a href='?page=$prevPage&post_id=$post_id'>&#9664; Indietro</a>";
                        }
                        if ($result_img->num_rows > 0) { 
                            echo "<a href='?page=$nextPage&post_id=$post_id'>Avanti &#9654;</a>";
                        }
                        echo "</div>";
                    } else {
                        echo '<img src="' . $defaultImage . '" alt="Immagine Post" class="post-image" id="post-image">';
                        echo "<div class='pagination'>";
                        if ($page > 1) {
                            echo "<a href='?page=$prevPage&post_id=$post_id'>&#9664; Indietro</a>";
                        }
                        echo "</div>";
                    }    
                    ?>          
                </div>
            </div>               
            <hr>
            <h3>Descrizione:</h3>
            <textarea id="post-description" rows="4" cols="50" readonly><?php echo $testo; ?></textarea>
            <hr>
            <h3>Lascia un Feed:</h3>
            <div class="desc-header">
            <div id="like-unlike-container">
                <?php if ($is_feed) : ?>
                <form action="feedback.php" method="get" id="unlike-form">
                    <input type="hidden" name="action" value="unlike">
                    <input type="hidden" name="id_utente" value="<?php echo $id_utente; ?>">
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    <button type="submit" class="unlike-button">Non mi piace</button>
                </form>
                <?php else : ?>
                <form action="feedback.php" method="get" id="like-form">
                    <input type="hidden" name="action" value="like">
                    <input type="hidden" name="id_utente" value="<?php echo $id_utente; ?>">
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    <button type="submit" class="like-button">Mi piace</button>
                </form>
                <?php endif; ?>
            </div>
            <form id="comment-form" method="post" action="new_commento.php">
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <textarea name="commento" id="post-commento" rows="4" cols="50" required></textarea>
                <div id="charCount">255 caratteri rimanenti</div>
                <button type="submit" class="edit-button">Aggiungi</button>
            </form>
                <form id="show-comments-form" action="visualizza_commenti.php" method="get">
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    <button type="submit" class="show-comments-button">Visualizza</button>
                </form>
                <div class="stats-container">
                    <div class="stat-box">
                    <?php if ($premium == "attivo") : ?>
                        <a href="vedi_feedback.php?id_post=<?php echo $post_id; ?>&id_utente=<?php echo $id_utente; ?>&action=like"><div id="n_like"><h4><?php echo $n_like; ?> mi piace</h4></div></a>
                    <?php else : ?>
                        <div id="n_like"><h4><?php echo $n_like; ?> mi piace</h4></div>
                    <?php endif; ?>
                    </div>
                    <div class="stat-box">
                        <?php if ($premium == "attivo") : ?>
                        <a href="vedi_feedback.php?id_post=<?php echo $post_id; ?>&id_utente=<?php echo $id_utente; ?>&action=views"><h4><?php echo $n_view; ?> visualizzazioni</h4></a>
                        <?php else : ?>
                            <h4><?php echo $n_view; ?> visualizzazioni</h4>
                        <?php endif; ?>
                    </div>
                    <div class="stat-box">
                    <div id="n_commenti"><h4><?php echo $n_commenti; ?> commenti</h4></div>
                    </div>
                </div>
            </div>
            <br>
            <div class="comments-container hidden" id="comments-container"></div>
        </div>  
    
        <script>
            document.querySelectorAll('input').forEach(input => {
                input.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                    }
                });
            });
        </script>   
        <hr>
        <div class="back-link">
            <a href="blog_profile.php?blog_id=<?php echo $id_blog; ?>">Torna indietro</a>
        </div>
    </main>
        <?php include("footer.php"); ?>
    </body>
</html>