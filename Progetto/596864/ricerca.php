<?php
session_start();
if (!isset($_SESSION["idUtente"])) {
    $_SESSION['errore'] = 'Utente non loggato. Effettua il login per continuare.';
    header("Location: login.php");
    exit;
}

$errore = '';

if (isset($_SESSION['errore'])) {
    $errore = $_SESSION['errore'];
    unset($_SESSION['errore']);
}


$id_utente = $_SESSION["idUtente"];
include('connessione.php');

// Verifica se è stata inviata una richiesta GET
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Verifica se sono stati passati i parametri necessari
    if (isset($_GET['type']) && isset($_GET['query'])) {
        $type = $_GET['type'];
        $query = $_GET['query'];

        // Esegui la query in base al tipo di ricerca
        if ($type === 'user') {
            $query = mysqli_real_escape_string($mysqli, $query);
            $sql = "SELECT Username FROM Utente WHERE IdUtente <> '$id_utente' AND Username LIKE '%$query%' LIMIT 10";
            $result = $mysqli->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="autocomplete-item">' . $row['Username'] . '</div>';
                }
            } else {
                echo '<div class="autocomplete-item">Nessun risultato trovato</div>';
            }
        } elseif ($type === 'post') {
            $query = mysqli_real_escape_string($mysqli, $query);

            // Aggiungi la condizione per la categoria nella query
            $sql = "SELECT TitoloPost FROM Post WHERE IdUtente <> '$id_utente' AND TitoloPost LIKE '%$query%'";
            
            $result = $mysqli->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="autocomplete-item">' . $row['TitoloPost'] . '</div>';
                }
            } else {
                echo '<div class="autocomplete-item">Nessun risultato trovato</div>';
            }
        } elseif ($type === 'blog') {

            $query = mysqli_real_escape_string($mysqli, $query);
            
            $sql = "SELECT TitoloBlog FROM Blog WHERE IdUtente <> '$id_utente' AND TitoloBlog LIKE '%$query%'";

            $result = $mysqli->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="autocomplete-item">' . $row['TitoloBlog'] . '</div>';
                }
            } else {
                echo '<div class="autocomplete-item">Nessun risultato trovato</div>';
            }
        }

        // Rilascia le risorse
        $mysqli->close();
        exit; // Termina lo script qui dopo aver gestito l'autocompletamento
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoDicoDaBlog</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style_ricerca.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#FormSearch").validate({
                rules: {
                    search_type: {
                        required: true
                    },
                    user_search: {
                        required: function(element) {
                            return $('#search_type').val() === 'user';
                        }
                    },
                    post_search: {
                        required: function(element) {
                            return $('#search_type').val() === 'post';
                        }
                    },
                    blog_search: {
                        required: function(element) {
                            return $('#search_type').val() === 'blog';
                        }
                    }
                },
                messages: {
                    search_type: {
                        required: "Seleziona il tipo di ricerca"
                    },
                    user_search: {
                        required: "Inserisci il nome utente da cercare"
                    },
                    post_search: {
                        required: "Inserisci il testo da cercare per il post"
                    },
                    blog_search: {
                        required: "Inserisci il testo da cercare per il blog"
                    },
                }, submitHandler: function(form) {
                        var searchType = $('#search_type').val();
                        var query = $('#' + searchType + '_search_input').val();
                        
                        if (searchType === 'user') {
                            window.location.href = 'user_profile.php?username=' + encodeURIComponent(query);
                        } else if (searchType === 'post') {
                            window.location.href = 'post_profile.php?titolo_post=' + encodeURIComponent(query);
                        } else if (searchType === 'blog') {
                            window.location.href = 'blog_profile.php?titolo_blog=' + encodeURIComponent(query);
                        }
                    },
                invalidHandler: function(event, validator) {
                    // Get the error message
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        var message = 'Compila correttamente tutti i campi richiesti.';
                        $("#error_message").html(message).show();
                    } else {
                        $("#error_message").hide();
                    }
                }
            });

            // Mostra/nascondi il campo di testo in base alla selezione del tipo di ricerca
            $('#search_type').on('change', function() {
                var selectedType = $(this).val();
                if (selectedType === 'user') {
                    $('#user_search').show();
                    $('#post_search').hide();
                    $('#blog_search').hide();
                } else if (selectedType === 'post') {
                    $('#user_search').hide();
                    $('#post_search').show();
                    $('#blog_search').hide();
                } else if (selectedType === 'blog') {
                    $('#user_search').hide();
                    $('#post_search').hide();
                    $('#blog_search').show();
                }
            });

            // Funzione per gestire l'autocompletamento
            function autocomplete(type, inputField, resultField) {
                $(inputField).keyup(function() {
                    var query = $(this).val();
                    if (query.length >= 2) {
                        $.ajax({
                            url: '<?php echo $_SERVER["PHP_SELF"]; ?>',
                            type: 'GET',
                            data: { type: type, query: query },
                            success: function(data) {
                                $(resultField).html(data);
                            }
                        });
                    } else {
                        $(resultField).empty();
                    }
                });

                // Gestione del clic sui risultati dell'autocompletamento
                $(resultField).on('click', '.autocomplete-item', function() {
                    var selectedValue = $(this).text().trim();
                    $(inputField).val(selectedValue); // Inserisci il valore nel campo di input
                    $(resultField).empty(); // Svuota i risultati dell'autocompletamento dopo la selezione
                });
            }

            // Autocompletamento per Utenti
            autocomplete('user', '#user_search_input', '#user_search_results');
            // Autocompletamento per Post
            autocomplete('post', '#post_search_input', '#post_search_results');
            // Autocompletamento per Blog
            autocomplete('blog', '#blog_search_input', '#blog_search_results');

            $('#FormSearch').submit(function(event) {
                if (!$(this).valid()) {
                    event.preventDefault(); // Previeni il comportamento predefinito di invio del form se non è valido
                    return;
                }

                var searchType = $('#search_type').val();
                var query = $('#' + searchType + '_search_input').val();
                
                if (searchType === 'user') {
                    window.location.href = 'user_profile.php?username=' + encodeURIComponent(query);
                } else if (searchType === 'post') {
                    window.location.href = 'post_profile.php?titolo_post=' + encodeURIComponent(query);
                } else if (searchType === 'blog') {
                    window.location.href = 'blog_profile.php?titolo_blog=' + encodeURIComponent(query);
                }
            });
        });
    </script>
    </head>
    <body>
        <?php include("header.php"); ?>
        <div class="formbox-research">
            <h2>Ricerca</h2>
            <form id="FormSearch" class="form" action="" method="get">
                <label for="search_type">Seleziona il tipo di ricerca:</label>
                <select id="search_type" name="search_type" class="input" required>
                    <option value="" selected disabled hidden>Scegli...</option>
                    <option value="user">Utente</option>
                    <option value="post">Post</option>
                    <option value="blog">Blog</option>
                </select>
                <br><br>
                <div id="user_search" style="display: none;">
                    <label for="user_search">Cerca Utente:</label>
                    <input type="text" id="user_search_input" name="user_search" class="input">
                    <div id="user_search_results"></div>
                </div>
                <div id="post_search" style="display: none;">
                    <label for="post_search">Cerca Post:</label>
                    <input type="text" id="post_search_input" name="post_search" class="input">
                    <div id="post_search_results"></div>
                </div>
                <div id="blog_search" style="display: none;">
                    <label for="blog_search">Cerca Blog:</label>
                    <input type="text" id="blog_search_input" name="blog_search" class="input">
                    <div id="blog_search_results"></div>
                </div>
                <br><br>
                <input type="submit" class="cerca" value="Cerca">
                <?php if ($errore): ?>
                <div id="error-message">
                    <?php echo $errore; ?>
                </div>
                <?php endif; ?>
            </form>
        </div>
        <?php include("footer.php"); ?>
    </body>
</html>