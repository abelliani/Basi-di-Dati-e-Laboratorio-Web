<?php
session_start();
include("connessione.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_utente = $_SESSION["idUtente"];
    
    try {
        if (!isset($_POST["blog"]) || !isset($_POST["titolo_post"]) || !isset($_POST["testo"])) {
            throw new Exception("Compila tutti i campi");
        }

        $blog = mysqli_real_escape_string($mysqli, $_POST["blog"]);
        $titolo_post = mysqli_real_escape_string($mysqli, $_POST["titolo_post"]);
        $testo = mysqli_real_escape_string($mysqli, $_POST["testo"]);
        $datetime = date("Y-m-d H:i:s");

        $check_query = "SELECT COUNT(*) AS count FROM Post WHERE IdUtente = '$id_utente' AND IdBlog = '$blog' AND TitoloPost = '$titolo_post'";
        $result = $mysqli->query($check_query);
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            throw new Exception("Hai già creato un post con questo nome.");
        }

        $estensioni_file = array('jpg', 'jpeg', 'png', 'gif');
        $valid_files = true;

        // controllo se il file esiste
        if (isset($_FILES['immagine']['name'][0]) && $_FILES['immagine']['name'][0] != '') {
            // controllo il numero di immagini
            if (count($_FILES['immagine']['name']) > 5) {
                throw new Exception("Puoi caricare un massimo di 5 immagini per post.");
            }

            foreach ($_FILES['immagine']['name'] as $key => $immagine) {
                $immagine_estensione = strtolower(pathinfo($immagine, PATHINFO_EXTENSION));
                if (!in_array($immagine_estensione, $estensioni_file)) {
                    $valid_files = false;
                    break;
                }
            }
        }

        if (!$valid_files) {
            throw new Exception("File non consentito");
        }

        // inserisco il post solo se la validazione = true
        $insert_query = "INSERT INTO Post(IdUtente, IdBlog, TitoloPost, Testo, Data) VALUES ($id_utente, '$blog', '$titolo_post', '$testo', '$datetime')";
        if ($mysqli->query($insert_query) === TRUE) {
            $id_post = $mysqli->insert_id;

            // controllo se esiste l'immagine
            if (isset($_FILES['immagine']['name'][0]) && $_FILES['immagine']['name'][0] != '') {
                foreach ($_FILES['immagine']['name'] as $key => $immagine) {
                    $immagine_temp = $_FILES['immagine']['tmp_name'][$key];
                    $target_file = "immagini/" . basename($immagine);

                    // Salvo il file nella directory
                    if (move_uploaded_file($immagine_temp, $target_file)) {
                        // inserisco il nome del file nel db
                        $insert_query_immagine = "INSERT INTO Immagine(IdPost, Immagine) VALUES ('$id_post', '$target_file')";
                        if (!$mysqli->query($insert_query_immagine)) {
                            throw new Exception("Errore durante il caricamento dell'immagine: " . $mysqli->error);
                        }
                    } else {
                        throw new Exception("Errore durante il caricamento del file");
                    }
                }
            }

            $message = "Post creato con successo";
            $_SESSION['message'] = $message;
            header("Location: creapost.php");
            exit();
        } else {
            throw new Exception("Errore: " . $mysqli->error);
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $_SESSION['message'] = $message;
        header("Location: creapost.php");
        exit();
    }
} else {
    $message = "Richiesta non valida";
    $_SESSION['message'] = $message;
    header("Location: creapost.php");
    exit();
}
