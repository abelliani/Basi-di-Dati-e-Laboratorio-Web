<?php
session_start();
include("connessione.php");

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        $id_commento = $_GET['comment_id'];

        if (!$id_commento) {
            echo "Nessun commento trovato";
            exit;
        }

        if ($action === 'update_comment_post') {
            try {
                $new_comment_post = $_GET['new_comment_post'];
                $update_query = $mysqli->prepare("UPDATE Commento SET Testo = ? WHERE IdCommento = ?");
                $update_query->bind_param("si", $new_comment_post, $id_commento);
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
        } elseif ($action === 'delete_comment') {
            try {
                $delete_query = $mysqli->prepare("DELETE FROM Commento WHERE IdCommento = ?");
                $delete_query->bind_param("i", $id_commento);
                $delete_query->execute();

                if ($delete_query->affected_rows > 0) {
                    echo 'success';
                } else {
                    echo 'failure';
                }
                $delete_query->close();
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
