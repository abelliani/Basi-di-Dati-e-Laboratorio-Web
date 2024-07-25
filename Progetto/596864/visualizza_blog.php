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
$id_blog = isset($_POST['blog_id']) ? $_POST['blog_id'] : $_GET['blog_id'];
if (!isset($id_blog)) {
    header("Location: profilo.php");
}
    $query = "SELECT * FROM Blog WHERE IdBlog = $id_blog";
    $result = $mysqli->query($query);
    if ($result->num_rows > 0) {
        $blog = $result->fetch_assoc();
        $blog_id = $blog["IdBlog"];
        $titolo_blog = $blog['TitoloBlog'];
        $n_follow = $blog['N_Follow'];
        $descrizione = $blog['Descrizione'];
        $immagine = $blog['Immagine'];
    } else {
        echo "ID del blog non specificato.";
    }
$select_query_post = "SELECT * FROM Post NATURAL JOIN Blog WHERE IdBlog = $id_blog";
$result = $mysqli->query($select_query_post);

$select_coautore_query = "SELECT * FROM Post INNER JOIN Coautore ON Post.IdUtente = Coautore.IdCoautore AND Post.IdBlog = Coautore.IdBlog WHERE Coautore.IdBlog = $blog_id";
$result_coautore = $mysqli->query($select_coautore_query);

$check_follow_query = "SELECT * FROM FollowBlog WHERE IdUtente = $id_utente AND Idblog = $id_blog";
$is_following = $mysqli->query($check_follow_query)->num_rows > 0;


?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LoDicoDaBlog</title>
        <link rel="stylesheet" href="style/style.css">
        <link rel="stylesheet" href="style/style_visualizza_blog.css">
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
        <script>
           $(document).ready(function() {
                $('.delete-button').on('click', function(event) {
                    event.preventDefault(); // Previene il comportamento predefinito del pulsante

                    var postId = $(this).closest('.delete-post-form').data('post-id');
                    var blogId = $(this).closest('.delete-post-form').find('input[name="blog_id"]').val();

                    if (confirm('Sei sicuro di voler eliminare questo post?')) {
                        $.ajax({
                            url: 'elimina_post.php',
                            type: 'POST',
                            data: { 
                                blog_id: blogId,
                                post_id: postId 
                            },
                            dataType: 'html',
                            success: function(response) {
                                if (response.trim() === 'success') {
                                    $('#post_' + postId).remove(); // Rimuove la riga del post dalla tabella

                                    // Controlla se ci sono altre righe nella tabella
                                    if ($('#autore-post tbody tr').length === 0) {
                                        $('#autore-post tbody').html('<tr><td colspan="4">Nessun post trovato.</td></tr>');
                                    }
                                } else {
                                    alert('Errore durante l\'eliminazione del post.');
                                }
                            },
                            error: function() {
                                alert('Errore durante la richiesta.');
                            }
                        });
                    }
                });
            });
        </script>
    </head>
    <body>
        <?php include("header.php"); ?>
        <main>
        <div class="container">
            <form method="post" action="gestione_dati_blog.php" enctype="multipart/form-data">
                <h3>Blog:</h3>
                <div class="blog-header">
                    <div id="blog-info">
                        <img src="<?php echo $immagine; ?>" alt="Immagine Blog" class="blog-image" id="blog-image">
                        <div id="blog-title"><?php echo $titolo_blog; ?></div>
                        <div class="buttons-container">
                            <button type="button" class="edit-button" id="edit-title-btn">Modifica</button>
                        </div>
                    </div>
                    <div class="stats-container">
                        <div class="stat-box">
                            <?php if ($premium == "attivo") : ?>
                                <a href="seguaci_blog.php?id_blog=<?php echo $id_blog; ?>&id_utente=<?php echo $id_utente; ?>"><h4>Seguaci</h4></a>
                                <p><?php echo $n_follow; ?></p>
                            <?php else : ?>
                                <h4>Seguaci</h4>
                                <p><?php echo $n_follow; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div id="edit-title_blog-section" style="display: none;">
                    <input type="text" id="new_title_blog" name="new_title_blog" value="<?php echo $titolo_blog; ?>">
                    <button type="button" class="edit-button" id="save-title_blog-btn">Salva</button>
                    <button type="button" class="edit-button" id="cancel-title_blog-btn">Annulla</button>
                    <input type="hidden" name="action" value="update_title_blog">
                </div>
                <div class="buttons-container">
                    <button type="button" class="edit-button" id="edit-image-btn">Modifica</button>
                    <button type="button" class="delete-image-button" id="delete-image-btn">Elimina</button>
                </div>
                    
                <div id="edit-image-section" style="display: none;">
                    <input type="file" id="new-image" name="new_image" accept="image/*">
                    <button type="button" class="edit-button" id="save-image-btn">Aggiungi</button>
                    <button type="button" class="edit-button" id="cancel-image-btn">Annulla</button>
                    <input type="hidden" name="action" value="update_image">
                </div>
                <hr>
                <h3>Descrizione:</h3>
                <textarea id="blog-description" rows="4" cols="50" readonly><?php echo $descrizione; ?></textarea>
                <div class="buttons-container">
                    <button type="button" class="edit-button" id="edit-description-btn">Modifica</button>
                </div>
                <div id="edit-description-section" style="display: none;">
                    <textarea id="new_description_blog" name="new_description_blog" rows="4" cols="50"><?php echo $descrizione; ?></textarea>
                    <button type="button" class="edit-button" id="save-description_blog-btn">Salva</button>
                    <button type="button" class="edit-button" id="cancel-description_blog-btn">Annulla</button>
                    <input type="hidden" name="action" value="update_description_blog">
                </div>
            </form>
            <script>
                document.querySelectorAll('input').forEach(input => {
                    input.addEventListener('keydown', function(event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                        }
                    });
                });

                // Event listener per salvare modifica titolo blog
                document.getElementById('save-title_blog-btn').addEventListener('click', function() {
                    var newTitleBlog = document.getElementById('new_title_blog').value.trim();
                    if (newTitleBlog === '') {
                        alert('Il campo titolo non può essere vuoto.');
                        return;
                    }
                    updateUserData('update_title_blog', 'new_title_blog', newTitleBlog);
                });

                // Event listener per salvare modifica descrizione blog
                document.getElementById('save-description_blog-btn').addEventListener('click', function() {
                    var newDescriptionBlog = document.getElementById('new_description_blog').value.trim();
                    if (newDescriptionBlog === '') {
                        alert('Il campo descrizione non può essere vuoto.');
                        return;
                    }
                    updateUserData('update_description_blog', 'new_description_blog', newDescriptionBlog);
                });

                // Event listener per modificare titolo blog
                document.getElementById('edit-title-btn').addEventListener('click', function() {
                    document.getElementById('blog-title').style.display = 'none';
                    document.getElementById('edit-title_blog-section').style.display = 'block';
                });

                // Event listener per annullare modifica titolo blog
                document.getElementById('cancel-title_blog-btn').addEventListener('click', function() {
                    document.getElementById('blog-title').style.display = 'block';
                    document.getElementById('edit-title_blog-section').style.display = 'none';
                    document.getElementById('new_title_blog').value = '<?php echo $titolo_blog; ?>';
                });

                // Event listener per modificare descrizione blog
                document.getElementById('edit-description-btn').addEventListener('click', function() {
                    document.getElementById('blog-description').style.display = 'none';
                    document.getElementById('edit-description-section').style.display = 'block';
                });

                // Event listener per annullare modifica descrizione blog
                document.getElementById('cancel-description_blog-btn').addEventListener('click', function() {
                    document.getElementById('blog-description').style.display = 'block';
                    document.getElementById('edit-description-section').style.display = 'none';
                    document.getElementById('new_description_blog').value = '<?php echo $descrizione; ?>';
                });

                // Event listener per modificare immagine blog
                document.getElementById('edit-image-btn').addEventListener('click', function() {
                    document.getElementById('blog-image').style.display = 'none';
                    document.getElementById('edit-image-section').style.display = 'block';
                });

                // Event listener per annullare modifica immagine blog
                document.getElementById('cancel-image-btn').addEventListener('click', function() {
                    document.getElementById('blog-image').style.display = 'block';
                    document.getElementById('edit-image-section').style.display = 'none';
                    document.getElementById('new-image').value = ''; // Clear the file input
                });

                // Event listener per salvare immagine blog
                document.getElementById('save-image-btn').addEventListener('click', function() {
                    var newImage = document.getElementById('new-image').files[0];
                    if (newImage) {
                        var formData = new FormData();
                        formData.append('new_image', newImage);
                        formData.append('action', 'update_image');
                        updateImageData(formData);
                    } else {
                        alert('Seleziona un\'immagine prima di salvare.');
                    }
                });

                // Event listener per eliminare immagine blog
                document.getElementById('delete-image-btn').addEventListener('click', function() {
                    if (confirm('Sei sicuro di voler eliminare questa foto?')) {
                        updateUserData('delete_image', 'image', '');
                    }
                });

                function updateUserData(action, fieldName, value) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'gestione_dati_blog.php', true);
                    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            var response = xhr.responseText.trim();
                            var parts = response.split('|');
                            if (parts[0] === 'success') {
                                if (action === 'update_title_blog') {
                                    document.getElementById('blog-title').innerText = value;
                                    document.getElementById('blog-title').style.display = 'block';
                                    document.getElementById('edit-title_blog-section').style.display = 'none';
                                } else if (action === 'update_description_blog') {
                                    document.getElementById('blog-description').innerText = value;
                                    document.getElementById('blog-description').style.display = 'block';
                                    document.getElementById('edit-description-section').style.display = 'none';
                                } else if (action === 'delete_image') {
                                    var defaultImagePath = parts[1];
                                    document.getElementById('blog-image').src = defaultImagePath;  // Usa il percorso dell'immagine di default
                                    document.getElementById('blog-image').style.display = 'block';
                                }
                            } else {
                                alert('Si è verificato un errore durante l\'aggiornamento dei dati.');
                            }
                        }
                    };
                    var data = fieldName + '=' + encodeURIComponent(value) + '&action=' + action;
                    xhr.send(data);
                }


                function updateImageData(formData) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'gestione_dati_blog.php', true);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            var response = xhr.responseText.trim();  // Rimuovi eventuali spazi bianchi
                            var parts = response.split('|');
                            if (parts[0] === 'success') {
                                var imagePath = parts[1];
                                document.getElementById('blog-image').src = imagePath;  // Usa il percorso della nuova immagine
                                document.getElementById('blog-image').style.display = 'block';
                                document.getElementById('edit-image-section').style.display = 'none';
                            } else {
                                alert('Si è verificato un errore durante l\'aggiornamento dell\'immagine.');
                            }
                        }
                    };
                    xhr.send(formData);
                }
            </script>
            <hr>
            <h3>Post Autore:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Titolo</th>
                        <th>Data</th>
                        <th>Gestisci</th>
                        <th>Elimina</th>
                    </tr>
                </thead>
                <tbody id="autore-post">
                    <?php if ($result->num_rows > 0) : ?>
                        <?php while ($post = $result->fetch_assoc()) : ?>
                            <?php $data_mysql = $post['Data'];
                            $data_time = new DateTime($data_mysql);
                            $data_format_italiano = $data_time->format('d/m/Y H:i:s'); ?>
                            <tr id="post_<?php echo $post['IdPost']; ?>">
                                <td> <?php echo $post['TitoloPost'] ?> </td>
                                <td> <?php echo $data_format_italiano ?> </td>
                                <td>
                                <form method='get' action='visualizza_post.php'>
                                <input type='hidden' name='post_id' value='<?php echo $post['IdPost'] ?>'>
                                <button type='submit' class='edit-button'>Visualizza</button>
                                </form>
                                </td>
                                <td>
                                <form class='delete-post-form' data-post-id="<?php echo $post['IdPost']; ?>">
                                    <input type='hidden' name='blog_id' value='<?php echo $id_blog ?>'>
                                    <input type='hidden' name='post_id' value='<?php echo $post['IdPost'] ?>'>
                                    <button type='button' class='delete-button'>Elimina</button>
                                </form>
                                </td>
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
            <h3>Post Coautore:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Titolo</th>
                        <th>Data</th>
                        <th>Gestisci</th>
                        <th>Elimina</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_coautore->num_rows > 0) : ?>
                        <?php while ($post = $result_coautore->fetch_assoc()) : ?>
                            <?php $utente = $post["IdUtente"];
                            $data_mysql = $post['Data'];
                            $data_time = new DateTime($data_mysql);
                            $data_format_italiano = $data_time->format('d/m/Y H:i:s'); ?>
                            <tr>
                                <td> <?php echo $post['TitoloPost'] ?> </td>
                                <td> <?php echo $data_format_italiano ?> </td>
                                <td>
                                <form method='post' action='post_profile.php'>
                                <input type='hidden' name='post_id' value='<?php echo $post['IdPost'] ?>'>
                                <button type='submit' class='edit-button'>Visualizza</button>
                                </form>
                                </td>
                                <td>
                                <form method='post' action='elimina_post.php' onsubmit='return confirm(\"Sei sicuro di voler eliminare questo post?\");'>
                                <input type='hidden' name='blog_id' value='<?php echo $id_blog ?>'>
                                <input type='hidden' name='post_id' value='<?php echo $post['IdPost'] ?>'>
                                <button type='submit' class='delete-button'>Elimina</button>
                                </form>
                                </td>
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