<?php
session_start();
include("connessione.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $id_utente = $_SESSION["idUtente"];
        
        if ($action === 'update_title_blog') {
            try {
                if (isset($_POST['new_title_blog'])) {
                    $new_title_blog = $_POST['new_title_blog'];
                    $update_query = $mysqli->prepare("UPDATE Blog SET TitoloBlog = ? WHERE IdUtente = ?");
                    $update_query->bind_param("si", $new_title_blog, $id_utente);
                    $update_query->execute();

                    if ($update_query->affected_rows > 0) {
                        echo 'success';
                    } else {
                        echo 'failure';
                    }
                    $update_query->close();
                } else {
                    echo 'failure';
                }
            } catch (Exception $e) {
                echo 'failure';
            }
            $mysqli->close();
            exit;
        }

        if ($action === 'update_description_blog') {
            try {
                if (isset($_POST['new_description_blog'])) {
                    $new_description = $_POST['new_description_blog'];
                    $update_query = $mysqli->prepare("UPDATE Blog SET Descrizione = ? WHERE IdUtente = ?");
                    $update_query->bind_param("si", $new_description, $id_utente);
                    $update_query->execute();

                    if ($update_query->affected_rows > 0) {
                        echo 'success';
                    } else {
                        echo 'failure';
                    }
                    $update_query->close();
                } else {
                    echo 'failure';
                }
            } catch (Exception $e) {
                echo 'failure';
            }
            $mysqli->close();
            exit;
        }

        if ($action === 'update_image') {
            try {
                if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] == 0) {
                    $file_tmp = $_FILES['new_image']['tmp_name'];
                    $file_name = basename($_FILES['new_image']['name']);
                    $target_dir = "immagini/";
                    $target_file = $target_dir . $file_name;

                    if (move_uploaded_file($file_tmp, $target_file)) {
                        $update_query = $mysqli->prepare("UPDATE Blog SET Immagine = ? WHERE IdUtente = ?");
                        $update_query->bind_param("si", $target_file, $id_utente);
                        $update_query->execute();

                        if ($update_query->affected_rows > 0) {
                            echo "success|$target_file";
                        } else {
                            echo 'failure';
                        }
                        $update_query->close();
                    } else {
                        echo 'failure';
                    }
                } else {
                    echo 'failure';
                }
            } catch (Exception $e) {
                echo 'failure';
            }
            $mysqli->close();
            exit;
        }

        if ($action === 'delete_image') {
            try {
                $default_image = 'immagini/placeholder.png';
                $update_query = $mysqli->prepare("UPDATE Blog SET Immagine = ? WHERE IdUtente = ?");
                $update_query->bind_param("si", $default_image, $id_utente);
                $update_query->execute();

                if ($update_query->affected_rows > 0) {
                    echo "success|$default_image";
                } else {
                    echo 'failure';
                }
                $update_query->close();
            } catch (Exception $e) {
                echo 'failure';
            }
            $mysqli->close();
            exit;
        }

        // Se l'azione non corrisponde a nessuna delle azioni gestite
        echo 'failure';
        exit;
    } else {
        echo 'failure';
        exit;
    }
} else {
    $_SESSION['errore'] = "Richiesta non valida";
    header("Location: visualizza_blog.php");
    exit;
}
?>
