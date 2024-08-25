<?php
session_start();
if (!isset($_SESSION["idUtente"])) {
    $_SESSION['errore'] = 'Utente non loggato. Effettua il login per continuare.';
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LoDicoDaPost</title>
        <link rel="stylesheet" href="style/style.css">
        <link rel="stylesheet" href="style/style_creapost.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
        <script>
            $(document).ready(function(){
                $("#FormPost").validate({
                    rules: {
                        blog: {
                            required: true
                        },
                        titolo_post: {
                            required: true,
                            maxlength: 30
                        },
                        testo: {
                            required: true,
                            minlength: 1
                        },
                        immagine: {
                            TipoFile: ["jpg", "jpeg", "png", "gif"]
                        }
                    },
                    messages: {
                        blog: {
                            required: "inserisci un blog"
                        },
                        titolo_post: {
                            required: "inserisci un titolo",
                            maxlength: "Titolo troppo lungo"
                        },
                        testo: {
                            required: "inserisci una descrizione",
                            minlength: "devi inserire almeno un carattere"
                        },
                    },
                    submitHandler: function(form) {
                        form.submit();
                    }
                });
                $(document).ready(function(){
                    $.validator.addMethod("TipoFile", function(value, element, param) {
                        var files = element.files;
                        if (files.length === 0) return true; // Nessun file selezionato

                        for (var i = 0; i < files.length; i++) {
                            var fileExtension = files[i].name.split('.').pop().toLowerCase();
                            if ($.inArray(fileExtension, param) === -1) {
                                return false;
                            }
                        }
                        return true;
                    }, "Tipo del file non accettato");
                    
                    $(document).ready(function() {
                        var maxChars = 255; // Numero massimo di caratteri

                        // Aggiorna il contatore inizialmente
                        $('#charCount').text(maxChars + ' caratteri rimanenti');

                        // Aggiungi un event listener per l'input sul campo di testo
                        $('#testo').on('input', function() {
                            var currentLength = $(this).val().length;
                            var charsRemaining = maxChars - currentLength;

                            if (charsRemaining < 0) {
                                charsRemaining = 0;
                                $(this).val($(this).val().substring(0, maxChars)); // Troncamento del testo a maxChars caratteri
                            }

                            $('#charCount').text(charsRemaining + ' caratteri rimanenti');
                        });
                    });
                });
            })
        </script>
    </head>
    <body>
        <?php
            include("header.php");
            include("connessione.php");
            $id_utente = $_SESSION["idUtente"];
            $select_query_blog = "SELECT IdBlog, TitoloBlog FROM Blog WHERE IdUtente = $id_utente";
            $result_blog = $mysqli->query($select_query_blog);

            $select_query_coautore = "SELECT Blog.IdBlog, Blog.TitoloBlog FROM Blog JOIN Coautore ON Blog.IdBlog = Coautore.IdBlog WHERE Coautore.IdCoautore = $id_utente";
            $result_coautore = $mysqli->query($select_query_coautore);

            $options = '';

            if ($result_blog->num_rows > 0) {
                while ($row = $result_blog->fetch_assoc()) {
                    $options .= '<option value="' . $row['IdBlog'] . '">' . $row['TitoloBlog'] . '</option>';
                }
                $result_blog->free();
            }

            if ($result_coautore->num_rows > 0) {
                while ($row = $result_coautore->fetch_assoc()) {
                    $options .= '<option value="' . $row['IdBlog'] . '">' . $row['TitoloBlog'] . '</option>';
                }
                $result_coautore->free();
            }
        ?>
        <main>
        <div class="formbox-post">
            <h2>Crea Post</h2>
            <form id="FormPost" enctype="multipart/form-data" class="form" action="new_post.php" method="post">
                <select id="blog" name="blog" placeholder="blog" class="input">
                    <option value="default" selected disabled hidden>Blog</option>
                    <?php echo $options; ?>
                </select>
                <br><br>
                <input type="text" id="titolo_post" name="titolo_post" placeholder="Titolo" class="input">
                <br><br>
                <textarea id="testo" name="testo" placeholder="Testo" rows="4" cols="50"></textarea>
                <div id="charCount">255 caratteri rimanenti</div>
                <br>
                <input type="file" id="immagine" name="immagine[]" placeholder="Immagine" class="input" multiple>
                <span font-size="">File consentiti: jpg, jpeg, png, gif</span>
                <br>
                <input type="submit" class="submit" value="Crea Post">
                <?php if (!empty($_SESSION['message'])) {
                    $message = $_SESSION['message'];
                    $message_class = strpos($message, 'successo') !== false ? 'success' : 'error';
                ?>
                <p class="message <?php echo $message_class; ?>"><?php echo $message; ?></p>
                <?php unset($_SESSION['message']); // resetto il messaggio ?>
                <?php } ?>
                <div class='back-link'>
                    <a href='profilo.php?'>Torna al profilo</a>
                </div>
            </form>
        </div>
        </main>
        <?php
        include("footer.php");
        ?>
    </body>
</html>