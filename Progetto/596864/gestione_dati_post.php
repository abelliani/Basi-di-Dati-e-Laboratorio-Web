<?php
session_start();
include("connessione.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'update_title_post') {
            $id_post = $_POST['post_id'];
            try {
                $new_title_post = $_POST['new_title_post'];
                $update_query = $mysqli->prepare("UPDATE Post SET TitoloPost = ? WHERE IdPost = ?");
                $update_query->bind_param("si", $new_title_post, $id_post);
                $update_query->execute();

                if ($update_query->affected_rows > 0) {
                    echo 'success';
                } else {
                    echo 'failure';
                }
                $update_query->close();
                $mysqli->close();
                exit;
            } catch (Exception $e) {
                echo 'failure';
                exit;
            }
        }

        if ($action === 'update_description_post') {
            $id_post = $_POST['post_id'];
            try {
                $new_testo = $_POST['new_description_post'];
                $update_query = $mysqli->prepare("UPDATE Post SET Testo = ? WHERE IdPost = ?");
                $update_query->bind_param("si", $new_testo, $id_post);
                $update_query->execute();

                if ($update_query->affected_rows > 0) {
                    echo 'success';
                } else {
                    echo 'failure';
                }
                $update_query->close();
                $mysqli->close();
                exit;
            } catch (Exception $e) {
                echo 'failure';
                exit;
            }
        }

        if ($action === 'update_image') {
            $id_post = $_POST['post_id'];

            // Controllo del numero di immagini esistenti per il post
            $check_query = $mysqli->prepare("SELECT COUNT(*) AS count FROM Immagine WHERE IdPost = ?");
            $check_query->bind_param("i", $id_post);
            $check_query->execute();
            $result = $check_query->get_result();
            $row = $result->fetch_assoc();
            $current_image_count = $row['count'];
            $check_query->close();

            // Numero massimo di immagini
            $max_images = 5;

            // Gestione dell'aggiornamento dell'immagine
            if (isset($_FILES['new_image']) && !empty($_FILES['new_image']['name'][0])) {

                $new_image_count = count($_FILES['new_image']['name']);
            
                // Verifica se il numero totale di immagini supera il limite
                if ($current_image_count + $new_image_count > $max_images) {
                    echo 'failure';
                    exit;
                }

                $target_dir = "immagini/";
                $upload_success = true;

                for ($i = 0; $i < $new_image_count; $i++) {
                    $file_tmp = $_FILES['new_image']['tmp_name'][$i];
                    $file_name = basename($_FILES['new_image']['name'][$i]);
                    $target_file = $target_dir . $file_name;

                    if (move_uploaded_file($file_tmp, $target_file)) {
                        $update_query = $mysqli->prepare("INSERT INTO Immagine(IdPost, Immagine) VALUES (?, ?)");
                        if (!$update_query) {
                            echo 'failure';
                            exit;
                        }
                        $update_query->bind_param("is", $id_post, $target_file);
                        $update_query->execute();

                        if ($update_query->affected_rows <= 0) {
                            $upload_success = false;
                        }
                        $update_query->close();
                    } else {
                        $upload_success = false;
                    }
                }

                $mysqli->close();

                if ($upload_success) {
                    echo 'success';
                } else {
                    echo 'failure';
                }
                exit;
            } else {
                echo 'failure';
                exit;
            }
        }

        if ($action === 'delete_image') {
            // Gestione dell'eliminazione dell'immagine
            try {
                $id_img = $_POST['img_id'];
                $update_query = $mysqli->prepare("DELETE FROM Immagine WHERE IdImmagine = ?");
                $update_query->bind_param("i", $id_img);
                $update_query->execute();

                if ($update_query->affected_rows > 0) {
                    echo 'success';
                } else {
                    echo 'failure';
                }
                $update_query->close();
                $mysqli->close();
                exit;
            } catch (Exception $e) {
                echo 'failure';
                exit;
            }
        }
    }
} else {
    $_SESSION['errore'] = "Richiesta non valida";
    header("Location: visualizza_post.php");
    exit;
}
?>
