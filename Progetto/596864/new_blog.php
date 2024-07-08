<?php
session_start();
include('connessione.php');

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST"){
    $id_utente = $_SESSION["idUtente"];
    try {
        if (!isset($_POST["titolo_blog"]) || !isset($_POST["categoria"]) || !isset($_POST["descrizione"])){
            throw new Exception("Compila tutti i campi");
        }
        $titolo_blog = mysqli_real_escape_string($mysqli, $_POST["titolo_blog"]);
        $categoria = mysqli_real_escape_string($mysqli, $_POST["categoria"]); //restituisce l'id
        $descrizione = mysqli_real_escape_string($mysqli, $_POST["descrizione"]);

        $check_query = "SELECT COUNT(*) AS count FROM Blog WHERE IdUtente = '$id_utente' AND TitoloBlog = '$titolo_blog'";
        $result = $mysqli->query($check_query);
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            throw new Exception("Hai già creato un blog con questo nome.");
        }
        $immagine = "";
        if (isset($_FILES["immagine"]) && $_FILES["immagine"]["error"] == UPLOAD_ERR_OK){
            $tmp_name = $_FILES["immagine"]["tmp_name"];
            $name = basename($_FILES["immagine"]["name"]);
            $immagine = "immagini/" . $name;
            // Sposta l'immagine nella cartella desiderata
            if (!move_uploaded_file($tmp_name, $immagine)) {
                throw new Exception("Errore durante il caricamento dell'immagine.");
            } 
        } 
        if ($immagine) {
            $insert_query = "INSERT INTO Blog(IdUtente, TitoloBlog, Descrizione, Immagine) VALUES ($id_utente, '$titolo_blog', '$descrizione', '$immagine')";
        } else {
            $insert_query = "INSERT INTO Blog(IdUtente, TitoloBlog, Descrizione) VALUES ($id_utente ,'$titolo_blog', '$descrizione')";
        }

        if ($mysqli->query($insert_query) === TRUE) {
            $id_blog = $mysqli->insert_id;
            if (isset($_POST["sottocategoria"])){
                $sottocategoria = $_POST["sottocategoria"];
                $insert_query_associa = "INSERT INTO Associa(IdBlog, IdCategoria) VALUES ($id_blog, $categoria), ($id_blog, $sottocategoria)";
            } else {
                $insert_query_associa = "INSERT INTO Associa(IdBlog, IdCategoria) VALUES ($id_blog, $categoria)";
            }
                if ($mysqli->query($insert_query_associa) === TRUE) {
                    $message = "Blog creato con successo.";
                    $_SESSION['message'] = $message;
                    header("Location: creablog.php");
                    exit();
                } else {
                    throw new Exception("Errore durante l'associazione");
                }           
        } else {
            throw new Exception("Errore: " . $mysqli->error);
        }
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $_SESSION['message'] = $message;
        header("Location: creablog.php");
        exit();
    }
} else {
    $message = "Richiesta non valida";
    $_SESSION['message'] = $message;
    header("Location: creablog.php");
    exit();
}