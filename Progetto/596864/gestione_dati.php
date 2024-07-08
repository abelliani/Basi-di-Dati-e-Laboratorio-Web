<?php
session_start();
include("connessione.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $id_utente = $_SESSION["idUtente"];
        
        if ($action === 'update_username') {
            try {
                $newUsername = $_POST['new_username'];
                $update_query = $mysqli->prepare("UPDATE Utente SET Username = ? WHERE IdUtente = ?");
                $update_query->bind_param("si", $newUsername, $id_utente);
                $update_query->execute();

                if ($update_query->affected_rows > 0) {
                    $update_query->close();
                    $mysqli->close();
                    echo 'success';
                    exit;
                } else {
                    echo 'failure';
                    exit;
                }
            } catch (Exception $e) {
                echo 'failure';
                exit;
            }
        }

        if ($action === 'update_email') {
            try {
                $newEmail = $_POST['new_email'];
                if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                    echo 'email non valida';
                    exit;
                }
                $update_query = $mysqli->prepare("UPDATE Utente SET Email = ? WHERE IdUtente = ?");
                $update_query->bind_param("si", $newEmail, $id_utente);
                $update_query->execute();

                if ($update_query->affected_rows > 0) {
                    $update_query->close();
                    $mysqli->close();
                    echo 'success';
                    exit;
                } else {
                    echo 'failure';
                    exit;
                }
            } catch (Exception $e) {
                echo 'failure';
                exit;
            }
        }

        if ($action === 'update_image') {
            if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] == 0) {
                $file_tmp = $_FILES['new_image']['tmp_name'];
                $file_name = basename($_FILES['new_image']['name']);
                $target_dir = "immagini/";
                $target_file = $target_dir . $file_name;

                if (move_uploaded_file($file_tmp, $target_file)) {
                    $update_query = $mysqli->prepare("UPDATE Utente SET FotoProfilo = ? WHERE IdUtente = ?");
                    $update_query->bind_param("si", $target_file, $id_utente);
                    $update_query->execute();

                    if ($update_query->affected_rows > 0) {
                        $update_query->close();
                        $mysqli->close();
                        echo "success|$target_file";
                        exit;
                    } else {
                        echo 'failure';
                        exit;
                    }
                } else {
                    echo 'failure';
                    exit;
                }
            } else {
                echo 'failure';
                exit;
            }
        }

        if ($action === 'delete_image') {
            try {
                $default_image = 'immagini/profile.png';
                $update_query = $mysqli->prepare("UPDATE Utente SET FotoProfilo = ? WHERE IdUtente = ?");
                $update_query->bind_param("si", $default_image, $id_utente);
                $update_query->execute();

                if ($update_query->affected_rows > 0) {
                    $update_query->close();
                    $mysqli->close();
                    echo "success|$default_image";
                    exit;
                } else {
                    echo 'failure';
                    exit;
                }
            } catch (Exception $e) {
                echo 'failure';
                exit;
            }
        }

        if ($action === 'delete_user') {
            try {
                $delete_query = $mysqli->prepare("DELETE FROM Utente WHERE IdUtente = ?");
                $delete_query->bind_param("i", $id_utente);
                $delete_query->execute();

                if ($delete_query->affected_rows > 0) {
                    session_destroy();
                    $delete_query->close();
                    $mysqli->close();
                    echo 'success';
                    session_destroy();
                    header('Location: registrazione.php');
                    exit;
                } else {
                    echo 'failure';
                    exit;
                }
            } catch (Exception $e) {
                echo 'failure';
                exit;
            }
        }
    }
} else {
    $_SESSION['errore'] = "Richiesta non valida";
    header("Location: modifica.php");
    exit;
}