<?php
session_start();
if (!isset($_SESSION["idUtente"])) {
    $_SESSION['errore'] = 'Utente non loggato. Effettua il login per continuare.';
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<meta lang="it">
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LoDicoDaCoautore</title>
        <link rel="stylesheet" href="style/style.css">
        <link rel="stylesheet" href="style/style_newcoautore.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    </head>
    <script>
       $(document).ready(function() {
            // Funzione per gestire la sottomissione del form di eliminazione
            $(document).on('submit', '.delete-form', function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = form.serialize();
                var actionUrl = form.attr('action');
                var row = form.closest('tr');

                $.post(actionUrl, formData, function(response) {
                    // Assumi che il server ritorni un JSON con { success: true }
                    if (response.success) {
                        // Rimuovi la riga dalla tabella
                        row.remove();

                        // Controlla se ci sono altre righe nella tabella
                        if ($('tbody tr').length === 0) {
                            $('tbody').html('<tr><td colspan="3">Nessun coautore trovato.</td></tr>');
                        }
                    } else {
                        // Gestisci l'errore (opzionale)
                        alert(response.message);
                    }
                }, 'json');
            });
        });
    </script>
    <body>
        <?php
            include("header.php");
            include("connessione.php");
            $id_utente = $_SESSION["idUtente"];
            $select_query_blog = "SELECT IdBlog, TitoloBlog FROM Blog WHERE IdUtente = $id_utente ORDER BY TitoloBlog ASC";
            $result_blog = $mysqli->query($select_query_blog);

            $options_blog = '';
            if ($result_blog->num_rows > 0) {
                while ($row = $result_blog->fetch_assoc()) {
                    $options_blog .= '<option value="' . $row['IdBlog'] . '">' . $row['TitoloBlog'] . '</option>';
                }
                $result_blog->free();
            }
            $select_query_utente = "SELECT IdUtente, Username FROM Utente WHERE IdUtente <> $id_utente AND IdUtente IN (SELECT IdUtenteSeguito FROM FollowUtente WHERE IdUtenteSeguace = $id_utente) ORDER BY Username ASC";
            $result_utente = $mysqli->query($select_query_utente);
            $options_utente = "";
            if ($result_utente->num_rows > 0) {
                while ($row = $result_utente->fetch_assoc()) {
                    $options_utente .= '<option value="' . $row['IdUtente'] . '">' . $row['Username'] . '</option>';
                }
                $result_utente->free();
            }
            $select_coautore = 
            "SELECT U.IdUtente, U.Username, B.TitoloBlog, B.IdBlog
            FROM Utente AS U
            JOIN Coautore AS C ON U.IdUtente = C.IdCoautore
            JOIN Blog AS B ON C.IdBlog = B.IdBlog
            WHERE B.IdUtente = $id_utente
            ORDER BY U.Username, B.TitoloBlog";
            $result_coautore = $mysqli->query($select_coautore);
        ?>
        <main>
        <div class="formbox-coautore">
            <h2>Aggiungi Coautore</h2>
            <form id="FormCoautore" class="form" action="new_coautore.php" method="post">
                <select id="blog" name="blog" placeholder="Blog" class="input">
                    <option value="default" selected disabled hidden>Blog</option>
                    <?php echo $options_blog; ?>
                </select>
                <br><br>
                <select id="utente" name="utente" placeholder="utente" class="input">
                    <option value="default" selected disabled hidden>Utente</option>
                    <?php echo $options_utente; ?>
                </select>
                <br><br>
                <input type="submit" class="submit" value="Aggiungi Coautore">
                <?php if (!empty($_SESSION['message'])) {
                    $message = $_SESSION['message'];
                    $message_class = strpos($message, 'successo') !== false ? 'success' : 'error';
                ?>
                <p class="message <?php echo $message_class; ?>"><?php echo $message; ?></p>
                <?php unset($_SESSION['message']); // Resetto il messaggio ?>
                <?php } ?>
            </form>
        </div>
        <hr>
        <div class="container">
            <h3>Lista Coautori:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Blog</th>
                        <th>Gestisci</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_coautore->num_rows > 0) : ?>
                        <?php while ($coautore = $result_coautore->fetch_assoc()) : ?>
                            <tr id="row-<?php echo $coautore['IdUtente']; ?>">
                                <td> <?php echo $coautore['Username']; ?> </td>
                                <td> <?php echo $coautore['TitoloBlog']; ?> </td>
                                <td>
                                    <form method='post' action='del_coautore.php' class='delete-form'>
                                        <input type='hidden' name='blog' value='<?php echo $coautore['IdBlog']; ?>'>
                                        <input type='hidden' name='utente' value='<?php echo $coautore['IdUtente']; ?>'>
                                        <button type='submit' class='delete-button'>Elimina</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan='3'>Nessun coautore trovato.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        </main>
        <?php include("footer.php"); ?>
    </body>
</html>