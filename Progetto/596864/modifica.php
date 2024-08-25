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
$select_query = "SELECT Username, Email, FotoProfilo FROM Utente WHERE IdUtente = $id_utente";
$result = $mysqli->query($select_query);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $username = $row['Username'];
        $email = $row['Email'];
        $immagine = $row['FotoProfilo'];
    }
}
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LoDicoDaModifica</title>
        <link rel="stylesheet" href="style/style.css">
        <link rel="stylesheet" href="style/style_modifica.css">
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    </head>
    <body>
        <?php include("header.php"); ?>
        <main> <div class="container">
            <form method="post" action="gestione_dati.php" enctype="multipart/form-data">
                <h3>Username:</h3>
                <div id=username-info><?php echo $username; ?></div>
                <div class="buttons-container">
                    <button type="button" class="edit-button" id="edit-username-btn">Modifica</button>
                </div>
                <div id="edit-username-section" style="display: none;">
                    <input type="text" id="new-username" name="new_username" value="<?php echo $username; ?>">
                    <button type="button" class="edit-button" id="save-username-btn">Salva</button>
                    <button type="button" class="edit-button" id="cancel-username-btn">Annulla</button>
                    <input type="hidden" name="action" value="update_username">
                </div>
                <hr>
                <h3>Email:</h3>
                <div id="email-info"><?php echo $email; ?></div>
                <div class="buttons-container">
                    <button type="button" class="edit-button" id="edit-email-btn">Modifica</button>
                </div>
                <div id="edit-email-section" style="display: none;">
                    <input type="email" id="new-email" name="new_email" value="<?php echo $email; ?>">
                    <button type="button" class="edit-button" id="save-email-btn">Salva</button>
                    <button type="button" class="edit-button" id="cancel-email-btn">Annulla</button>
                    <input type="hidden" name="action" value="update_email">
                </div>
                <hr>
                <h3>Foto Profilo:</h3>
                <img src="<?php echo $immagine; ?>" alt="Foto Profilo" class="round-image" id="profile-image">
                <div class="buttons-container">
                    <button type="button" class="edit-button" id="edit-image-btn">Modifica</button>
                    <button type="button" class="delete-button" id="delete-image-btn">Elimina</button>
                </div>
                <div id="edit-image-section" style="display: none;">
                    <input type="file" id="new-image" name="new_image" accept="image/*">
                    <button type="button" class="edit-button" id="save-image-btn">Aggiungi</button>
                    <button type="button" class="edit-button" id="cancel-image-btn">Annulla</button>
                    <input type="hidden" name="action" value="update_image">
                </div>
                <hr>
                <h3>Elimina account:</h3>
                <div class="buttons-container">
                    <div id="delete_user_message" class="errore">
                        <?php echo isset($errore) ? $errore : ''; ?>
                    </div>
                    <button class="delete-button" name="delete_user" id="delete-user">Elimina</button>
                    <input type="hidden" name="action" value="delete_user">
                </div>
            </form>
        </div>
        <script>
            document.querySelectorAll('input').forEach(input => {
            input.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });
        });

        document.getElementById('save-username-btn').addEventListener('click', function() {
            var newUsername = document.getElementById('new-username').value.trim();
            if (newUsername === '') {
                alert('Il campo username non può essere vuoto.');
                return;
            }
            updateUserData('update_username', 'new_username', newUsername);
        });

        document.getElementById('save-email-btn').addEventListener('click', function() {
            var newEmail = document.getElementById('new-email').value.trim();
            if (newEmail === '') {
                alert('Il campo email non può essere vuoto.');
                return;
            }
            updateUserData('update_email', 'new_email', newEmail);
        });

        document.getElementById('delete-user').addEventListener('click', function(event) {
            if (!confirm('Sei sicuro di voler eliminare questo utente?')) {
                event.preventDefault();
            }
        });

        document.getElementById('edit-username-btn').addEventListener('click', function() {
            document.getElementById('username-info').style.display = 'none';
            document.getElementById('edit-username-section').style.display = 'block';
        });

        document.getElementById('cancel-username-btn').addEventListener('click', function() {
            document.getElementById('username-info').style.display = 'block';
            document.getElementById('edit-username-section').style.display = 'none';
            document.getElementById('new-username').value = '<?php echo $username; ?>';
        });

        document.getElementById('edit-email-btn').addEventListener('click', function() {
            document.getElementById('email-info').style.display = 'none';
            document.getElementById('edit-email-section').style.display = 'block';
        });

        document.getElementById('cancel-email-btn').addEventListener('click', function() {
            document.getElementById('email-info').style.display = 'block';
            document.getElementById('edit-email-section').style.display = 'none';
            document.getElementById('new-email').value = '<?php echo $email; ?>';
        });

        document.getElementById('edit-image-btn').addEventListener('click', function() {
            document.getElementById('profile-image').style.display = 'none';
            document.getElementById('edit-image-section').style.display = 'block';
        });

        document.getElementById('cancel-image-btn').addEventListener('click', function() {
            document.getElementById('profile-image').style.display = 'block';
            document.getElementById('edit-image-section').style.display = 'none';
            document.getElementById('new-image').value = '';
        });

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

        document.getElementById('delete-image-btn').addEventListener('click', function() {
            if (confirm('Sei sicuro di voler eliminare questa foto?')) {
                updateUserData('delete_image', 'image', '');
            }
        });

        function updateUserData(action, fieldName, value) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'gestione_dati.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = xhr.responseText.trim();
                    var parts = response.split('|');
                    if (parts[0] === 'success') {
                        if (action === 'update_username') {
                            document.getElementById('username-info').innerText = value;
                            document.getElementById('username-info').style.display = 'block';
                            document.getElementById('edit-username-section').style.display = 'none';
                        } else if (action === 'update_email') {
                            document.getElementById('email-info').innerText = value;
                            document.getElementById('email-info').style.display = 'block';
                            document.getElementById('edit-email-section').style.display = 'none';
                        } else if (action === 'delete_image') {
                            var defaultImagePath = parts[1];
                            document.getElementById('profile-image').src = defaultImagePath;  // Usa il percorso dell'immagine di default
                            document.getElementById('profile-image').style.display = 'block';
                        }
                    } else {
                        alert('Si è verificato un errore durante l\'aggiornamento dei dati.');
                    }
                }
            };
            xhr.send('action=' + action + '&' + fieldName + '=' + value);
        }

        function updateImageData(formData) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'gestione_dati.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = xhr.responseText.trim();  // Rimuovi eventuali spazi bianchi
                    var parts = response.split('|');
                    if (parts[0] === 'success') {
                        var imagePath = parts[1];
                        document.getElementById('profile-image').src = imagePath;  // Usa il percorso della nuova immagine
                        document.getElementById('profile-image').style.display = 'block';
                        document.getElementById('edit-image-section').style.display = 'none';
                    } else {
                        alert('Si è verificato un errore durante l\'aggiornamento dell\'immagine.');
                    }
                }
            };
            xhr.send(formData);
        }
        </script>
        </main>
        <?php include("footer.php"); ?>
    </body>
</html>