<?php
session_start();
include("connessione.php");

$id_utente_loggato = $_SESSION['idUtente']; 

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["post_id"])) {
    $id_post = $_GET["post_id"];

    $post_creator_query = "SELECT IdUtente FROM Post WHERE IdPost = ?";
    $stmt_post_creator = $mysqli->prepare($post_creator_query);
    $stmt_post_creator->bind_param("i", $id_post);
    $stmt_post_creator->execute();
    $result_post_creator = $stmt_post_creator->get_result();
    $post = $result_post_creator->fetch_assoc();

    $select_query = "SELECT
        Utente.IdUtente AS CommentUserId,
        Utente.Username AS NomeUtente,
        Utente.FotoProfilo AS FotoProfilo,
        Commento.IdCommento AS IdCommento,
        Commento.Testo AS TestoCommento,
        Commento.Data AS DataCommento
        FROM Utente
        INNER JOIN Commento ON Utente.IdUtente = Commento.IdUtente
        INNER JOIN Post ON Commento.IdPost = Post.IdPost
        WHERE Commento.IdPost = ?
        ORDER BY Commento.Data DESC";

    $stmt = $mysqli->prepare($select_query);
    $stmt->bind_param("i", $id_post);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($commento = $result->fetch_assoc()) {
            $data_mysql = $commento['DataCommento'];
            $data_time = new DateTime($data_mysql);
            $data_format_italiano = $data_time->format('d/m/Y H:i:s');
            echo "<div class='comment' id='comment-" . htmlspecialchars($commento['IdCommento']) . "'>";
                echo "<div class='comment-header'>";
                    echo "<img class='round-image' src='" . htmlspecialchars($commento['FotoProfilo']) . "'>";
                    echo "<div class='username-date'>";
                        echo "<p><strong>Utente:</strong> " . htmlspecialchars($commento['NomeUtente']) . "</p>";
                        echo "<p><strong>Data:</strong> " . htmlspecialchars($data_format_italiano) . "</p>";
                    echo "</div>";
                echo "</div>";
                echo "<div class='comment-text' id='comment-text-" . htmlspecialchars($commento['IdCommento']) . "'>";
                    echo "<p><strong>Testo:</strong> " . htmlspecialchars($commento['TestoCommento']) . "</p>";
                echo "</div>";
                if ($id_utente_loggato == $post["IdUtente"] || $id_utente_loggato == $commento["CommentUserId"]){
                    echo "<div class='buttons-container'>";
                        echo "<button type='button' class='edit-button' onclick='editComment(" . $commento['IdCommento'] . ")'>Modifica</button>";
                        echo "<button type='button' class='delete-button' onclick='deleteComment(" . $commento['IdCommento'] . ")'>Elimina</button>";
                    echo "</div>";
                        echo "<div id='edit-comment-section-" . $commento['IdCommento'] . "' style='display: none;'>";
                            echo "<textarea id='new_comment_post_" . $commento['IdCommento'] . "' name='new_comment_post' rows='4' cols='50' oninput='updateCharCount(this, " . $commento['IdCommento'] . ")'>" . htmlspecialchars($commento['TestoCommento']) . "</textarea>";
                            echo "<div id='charCount_" . $commento['IdCommento'] . "'>255 caratteri rimanenti</div>";
                            echo "<button type='button' class='edit-button' onclick='saveComment(" . $commento['IdCommento'] . ")'>Salva</button>";
                            echo "<button type='button' class='edit-button' onclick='cancelEdit(" . $commento['IdCommento'] . ")'>Annulla</button>";
                            echo "<input type='hidden' id='comment_id_" . $commento['IdCommento'] . "' value='" . $commento['IdCommento'] . "'>";
                            echo "<input type='hidden' name='action' value='update_comment_post'>";
                        echo "</div>";
                    echo "</div>";
                }
            echo "</div>";
        }
    } else {
        echo "<p>Nessun commento disponibile per questo post.</p>";
    }
} else {
    echo "Richiesta non valida.";
}
?>

<script>
    function updateUserComment(action, commentId, fieldName, value) {
    var xhr = new XMLHttpRequest();
    var url = 'gestione_commenti.php?action=' + encodeURIComponent(action) +
              '&comment_id=' + encodeURIComponent(commentId) +
              '&' + fieldName + '=' + encodeURIComponent(value);
    xhr.open('GET', url, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                console.log(xhr.responseText);
                if (xhr.responseText.includes('success')) {
                    if (action === 'update_comment_post') {
                        document.getElementById('comment-text-' + commentId).innerText = value;
                        document.getElementById('edit-comment-section-' + commentId).style.display = 'none';
                        document.getElementById('comment-text-' + commentId).style.display = 'block';
                    } else if (action === 'delete_comment') {
                        document.getElementById('comment-' + commentId).remove();
                    }
                } else {
                    alert('Si è verificato un errore durante l\'aggiornamento dei dati.');
                }
            } else {
                alert('Si è verificato un errore durante la richiesta.');
            }
        }
    };
    xhr.send();
}


function editComment(commentId) {
    document.getElementById('comment-text-' + commentId).style.display = 'none';
    document.getElementById('edit-comment-section-' + commentId).style.display = 'block';
}

function cancelEdit(commentId) {
    document.getElementById('comment-text-' + commentId).style.display = 'block';
    document.getElementById('edit-comment-section-' + commentId).style.display = 'none';
    var originalText = document.getElementById('comment-text-' + commentId).innerText.trim();
    document.getElementById('new_comment_post_' + commentId).value = originalText;
}

function saveComment(commentId) {
    var newCommentPost = document.getElementById('new_comment_post_' + commentId).value.trim();
    if (newCommentPost === '') {
        alert('Il campo testo commento non può essere vuoto.');
        return;
    }
    updateUserComment('update_comment_post', commentId, 'new_comment_post', newCommentPost);
}


function deleteComment(commentId) {
    if (confirm('Sei sicuro di voler eliminare questo commento?')) {
        updateUserComment('delete_comment', commentId, 'delete_comment_id', commentId);
    }
}

function updateCharCount(textarea, commentId) {
    var maxLength = 255;
    var currentLength = textarea.value.length;
    if (currentLength > maxLength) {
        textarea.value = textarea.value.substring(0, maxLength);
        currentLength = maxLength;
    }
    var remainingChars = maxLength - currentLength;
    document.getElementById('charCount_' + commentId).innerText = remainingChars + " caratteri rimanenti";
}
</script>