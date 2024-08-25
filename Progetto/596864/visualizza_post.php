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
$id_post = isset($_GET['post_id']) ? $_GET['post_id'] : (isset($_POST['post_id']) ? $_POST['post_id'] : null);

if (!isset($id_post)) {
    header("Location: visualizza_blog.php");
}

$query = "SELECT * FROM Post WHERE IdPost = $id_post";
$result = $mysqli->query($query);

if ($result->num_rows > 0) { 
    $post = $result->fetch_assoc();
    $post_id = $post["IdPost"];
    $id_blog = $post["IdBlog"];
    $titolo_post = $post['TitoloPost'];
    $testo = $post['Testo'];
    $n_like = $post['N_Like'];
    $n_view = $post['N_View'];
    $n_commenti = $post['N_Commenti'];
} else {
    echo "ID del post non specificato.";
}

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$itemsPerPage = 1;
$offset = ($page - 1) * $itemsPerPage;

$query_immagini = "SELECT * FROM Immagine WHERE IdPost = $id_post  LIMIT $itemsPerPage OFFSET $offset";
$result_img = $mysqli->query($query_immagini);

$check_feed_query = $mysqli->prepare("SELECT * FROM Feedback WHERE IdUtente = ? AND IdPost = ? AND Tipo = 1");
$check_feed_query->bind_param("ii", $id_utente, $id_post);
$check_feed_query->execute();
$is_feed = $check_feed_query->get_result()->num_rows > 0;

$check_view_query = $mysqli->prepare("SELECT * FROM Feedback WHERE IdUtente = ? AND IdPost = ?");
$check_view_query->bind_param("ii", $id_utente, $id_post);
$check_view_query->execute();
$result_view = $check_view_query->get_result();

if ($result_view->num_rows == 0) {
    $insert_view_query = $mysqli->prepare("INSERT INTO Feedback(IdUtente, IdPost, Tipo) VALUES (?, ?, 0)");
    $insert_view_query->bind_param("ii", $id_utente, $id_post);
    $insert_view_query->execute();
    header("Location: visualizza_post.php?post_id=$id_post");
}
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LoDicoDaPost</title>
        <link rel="stylesheet" href="style/style.css">
        <link rel="stylesheet" href="style/style_visualizza_post.css">
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

                    // Controllo se il contenitore del commento è ancora visibile
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
            <form method="post" action="gestione_dati_post.php" enctype="multipart/form-data">
                <h3>Post:</h3>
                <div class="post-header">
                    <div id="post-info">
                        <div id="post-title"><?php echo $titolo_post; ?></div>
                        <div class="buttons-container">
                            <button type="button" class="edit-button" id="edit-title-btn">Modifica</button>
                        </div>
                        <div id="edit-title_post-section" style="display: none;">
                            <input type="text" id="new_title_post" name="new_title_post" value="<?php echo $titolo_post; ?>">
                            <button type="button" class="edit-button" id="save-title_post-btn">Salva</button>
                            <button type="button" class="edit-button" id="cancel-title_post-btn">Annulla</button>
                            <input type="hidden" name="action" value="update_title_post">
                        </div>
                        <br>
                        <?php 
                        $prevPage = $page - 1;
                        $nextPage = $page + 1;
                        $defaultImage = 'immagini/placeholder.png';  // Path to your default image
                        
                        if ($result_img->num_rows > 0) {
                            while ($img = $result_img->fetch_assoc()) {
                                $immagine = $img["Immagine"];
                                $id_img = $img["IdImmagine"];
                                echo '<div class="image-container" id="image-container-' . $id_img . '">';
                                echo '<img src="' . $immagine . '" alt="Immagine Post" class="post-image" id="post-image-' . $id_img . '">';
                                echo '<div class="buttons-container">';
                                echo '<button type="button" class="delete-button" data-img-id="' . $id_img . '">Elimina</button>';
                                echo '</div>';
                                echo '</div>';
                            }
                        
                            echo "<div class='pagination'>";
                            if ($page > 1) {
                                echo "<a href='?page=$prevPage&post_id=$id_post'>&#9664; Indietro</a>";
                            }
                            if ($result_img->num_rows > 0) { 
                                echo "<a href='?page=$nextPage&post_id=$id_post'>Avanti &#9654;</a>";
                            }
                            echo "</div>";
                        } else {
                            echo '<img src="' . $defaultImage . '" alt="Immagine Post" class="post-image" id="post-image">';
                            echo "<div class='pagination'>";
                            if ($page > 1) {
                                echo "<a href='?page=$prevPage&post_id=$id_post'>&#9664; Indietro</a>";
                            }
                            echo "</div>";
                        }          
                        ?>          
                    </div>
                </div>                    
                <div class="buttons-container">
                    <button type="button" class="edit-button" id="edit-image-btn">Modifica</button>
                </div>                   
                <div id="edit-image-section" style="display: none;">
                    <input type="file" id="new-image" name="new_image[]" accept="image/*" multiple>
                    <button type="button" class="edit-button" id="save-image-btn">Aggiungi</button>
                    <button type="button" class="edit-button" id="cancel-image-btn">Annulla</button>
                    <input type="hidden" name="action" value="update_image">
                </div>
                <hr>
                <h3>Descrizione:</h3>
                <textarea id="post-description" rows="4" cols="50" readonly><?php echo $testo; ?></textarea>        
                <div class="buttons-container">
                    <button type="button" class="edit-button" id="edit-description-btn">Modifica</button>
                </div>
                <div id="edit-description-section" style="display: none;">
                    <textarea id="new_description_post" name="new_description_post" rows="4" cols="50"><?php echo $testo; ?></textarea>
                    <button type="button" class="edit-button" id="save-description_post-btn">Salva</button>
                    <button type="button" class="edit-button" id="cancel-description_post-btn">Annulla</button>
                    <input type="hidden" name="action" value="update_description_post">
                </div> 
            </form>
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
                <input type="hidden" name="post_id" value="<?php echo $id_post; ?>">
                <textarea name="commento" id="post-commento" rows="4" cols="50" required></textarea>
                <div id="charCount">255 caratteri rimanenti</div>
                <button type="submit" class="edit-button">Aggiungi</button>
            </form>
            <form id="show-comments-form" action="visualizza_commenti.php" method="get">
                <input type="hidden" name="post_id" value="<?php echo $id_post; ?>">
                <button type="submit" class="show-comments-button">Visualizza</button>
            </form>
            <div class="stats-container">
                <div class="stat-box">
                    <?php if ($premium == "attivo") : ?>
                        <a href="vedi_feedback.php?id_post=<?php echo $id_post; ?>&id_utente=<?php echo $id_utente; ?>&action=like"><div id="n_like"><h4><?php echo $n_like; ?> mi piace</h4></div></a>
                    <?php else : ?>
                        <div id="n_like"><h4><?php echo $n_like; ?> mi piace</h4></div>
                    <?php endif; ?>
                </div>
                <div class="stat-box">
                    <?php if ($premium == "attivo") : ?>
                        <a href="vedi_feedback.php?id_post=<?php echo $id_post; ?>&id_utente=<?php echo $id_utente; ?>&action=views"><h4><?php echo $n_view; ?> visualizzazioni</h4></a>
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
        <script>
            document.querySelectorAll('input').forEach(input => {
                input.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                    }
                });
            });

             // Event listener per modificare titolo post
            document.getElementById('edit-title-btn').addEventListener('click', function() {
                document.getElementById('post-title').style.display = 'none';
                document.getElementById('edit-title_post-section').style.display = 'block';
            });

             // Event listener per annullare modifica titolo post
            document.getElementById('cancel-title_post-btn').addEventListener('click', function() {
                document.getElementById('post-title').style.display = 'block';
                document.getElementById('edit-title_post-section').style.display = 'none';
                document.getElementById('new_title_post').value = '<?php echo $titolo_post; ?>'; // Ripristina il valore originale
            });

            // Event listener per salvare modifica titolo post
            document.getElementById('save-title_post-btn').addEventListener('click', function() {
                var newTitlePost = document.getElementById('new_title_post').value.trim(); // Rimuove spazi bianchi iniziali e finali
                if (newTitlePost === '') {
                    alert('Il campo titolo non può essere vuoto.');
                    return;
                }
                updateUserData('update_title_post', 'new_title_post', newTitlePost);
            });

            // Event listener per modificare descrizione post
            document.getElementById('edit-description-btn').addEventListener('click', function() {
                document.getElementById('post-description').style.display = 'none';
                document.getElementById('edit-description-section').style.display = 'block';
            });

            // Event listener per annullare modifica descrizione post
            document.getElementById('cancel-description_post-btn').addEventListener('click', function() {
                document.getElementById('post-description').style.display = 'block';
                document.getElementById('edit-description-section').style.display = 'none';
                document.getElementById('new_description_post').value = '<?php echo $testo; ?>'; // Ripristina il valore originale
            });

            // Event listener per salvare modifica descrizione post
            document.getElementById('save-description_post-btn').addEventListener('click', function() {
                var newDescriptionPost = document.getElementById('new_description_post').value.trim(); // Rimuove spazi bianchi iniziali e finali
                if (newDescriptionPost === '') {
                    alert('Il campo descrizione non può essere vuoto.');
                    return;
                }
                updateUserData('update_description_post', 'new_description_post', newDescriptionPost);
            });

            // Gestione del click sul pulsante "Modifica"
            document.getElementById('edit-image-btn').addEventListener('click', function() {
                var editImageSection = document.getElementById('edit-image-section');
                if (editImageSection) {
                    editImageSection.style.display = 'block';
                }
            });

            // Gestione del click sul pulsante "Annulla"
            document.getElementById('cancel-image-btn').addEventListener('click', function() {
                var editImageSection = document.getElementById('edit-image-section');
                var newImageInput = document.getElementById('new-image');
                if (editImageSection) {
                    editImageSection.style.display = 'none';
                }
                if (newImageInput) {
                    newImageInput.value = ''; // Clear the file input
                }
            });

            // Gestione del click sul pulsante "Aggiungi"
            document.getElementById('save-image-btn').addEventListener('click', function() {
                var newImages = document.getElementById('new-image').files;
                if (newImages.length > 0) {
                    var formData = new FormData();
                    for (var i = 0; i < newImages.length; i++) {
                        formData.append('new_image[]', newImages[i]);
                    }
                    formData.append('action', 'update_image');
                    formData.append('post_id', <?php echo $id_post; ?>); // Assicurati che $id_post sia definito correttamente nel contesto PHP

                    updateImageData(formData);
                } else {
                    alert('Seleziona almeno un\'immagine prima di salvare.');
                }
            });

            // Funzione per gestire l'aggiornamento delle immagini
            function updateImageData(formData) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'gestione_dati_post.php', true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        if (xhr.responseText.includes('success')) {
                            // Se l'aggiornamento dell'immagine è avvenuto con successo, nascondi il form di modifica
                            var editImageSection = document.getElementById('edit-image-section');
                            if (editImageSection) {
                                editImageSection.style.display = 'none';
                            }   alert('Immagine inserita con successo.');
                        } else {
                            alert('Si è verificato un errore durante l\'aggiornamento dell\'immagine.');
                        }
                    }
                };

                // Invia la richiesta con i dati del modulo FormData
                xhr.send(formData);
            }

            function updateUserData(action, fieldName, value) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'gestione_dati_post.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        if (xhr.responseText.includes('success')) {
                            if (action === 'update_title_post') {
                                document.getElementById('post-title').innerText = value;
                                document.getElementById('post-title').style.display = 'block';
                                document.getElementById('edit-title_post-section').style.display = 'none';
                            } else if (action === 'update_description_post') {
                                document.getElementById('post-description').innerText = value;
                                document.getElementById('post-description').style.display = 'block';
                                document.getElementById('edit-description-section').style.display = 'none';
                            }
                        } else {
                            alert('Si è verificato un errore durante l\'aggiornamento dei dati.');
                        }
                    }
                };
                var data = fieldName + '=' + encodeURIComponent(value) + '&action=' + action + '&post_id=' + encodeURIComponent(<?php echo $id_post; ?>);
                xhr.send(data);
            }

            // Gestione del clic sul pulsante "Elimina" dell'immagine
            document.querySelectorAll('.delete-button').forEach(function(button) {
                button.addEventListener('click', function() {
                    var imgId = this.getAttribute('data-img-id');
                    if (confirm('Sei sicuro di voler eliminare questa immagine?')) {
                        deleteImage(imgId);
                    }
                });
            });

            function deleteImage(imgId) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'gestione_dati_post.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        console.log('Server response: ' + xhr.responseText); // Aggiunto per il debug
                        if (xhr.responseText.trim() === 'success') {
                            alert('Immagine eliminata con successo.');
                            var imageContainer = document.getElementById('image-container-' + imgId);
                            if (imageContainer) {
                                imageContainer.parentNode.removeChild(imageContainer);
                            } 
                        } else {
                            alert('Si è verificato un errore durante l\'eliminazione dell\'immagine.');
                        }
                    }
                };
                xhr.send('action=delete_image&img_id=' + encodeURIComponent(imgId));
            }

        </script>
        <hr>
        <div class="back-link">
            <a href="visualizza_blog.php?blog_id=<?php echo $id_blog; ?>">Torna indietro</a>
        </div>
        </main>
        <?php include("footer.php"); ?>
    </body>
</html>