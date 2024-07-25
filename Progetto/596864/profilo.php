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
$premium = $_SESSION["premium_status"];

$select_query = "SELECT * FROM Utente WHERE IdUtente = $id_utente";
$result = $mysqli->query($select_query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row["IdUtente"];
        $username = $row['Username'];
        $immagine = $row['FotoProfilo'];
        $n_seguaci = $row['N_Seguaci'];
        $n_seguiti = $row['N_Seguiti'];
    }
}

$select_blog_query = 
    "SELECT 
        B.IdBlog, 
        B.TitoloBlog, 
        B.N_Follow, 
        GROUP_CONCAT(C.Tema SEPARATOR ', ') AS Temi
    FROM 
        Blog AS B
    NATURAL JOIN Associa
    NATURAL JOIN Categoria AS C
    WHERE 
        B.IdUtente = $id_utente
    GROUP BY 
        B.IdBlog";
$result_blog = $mysqli->query($select_blog_query);

$select_coautore_query = 
    "SELECT 
        B.IdBlog, 
        B.TitoloBlog, 
        B.N_Follow, 
        GROUP_CONCAT(C.Tema SEPARATOR ', ') AS Temi
    FROM 
        Coautore AS CO
    NATURAL JOIN Blog AS B
    NATURAL JOIN Associa
    NATURAL JOIN Categoria AS C
    WHERE 
        CO.IdCoautore = $id_utente
    GROUP BY 
        B.IdBlog";
$result_coautore = $mysqli->query($select_coautore_query);
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LoDicoDaProfilo</title>
        <link rel="stylesheet" href="style/style.css">
        <link rel="stylesheet" href="style/style_profilo.css">
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.delete-button').on('click', function(event) {
                    event.preventDefault(); // Previene il comportamento predefinito del pulsante

                    var blogId = $(this).closest('.delete-blog-form').data('blog-id');

                    if (confirm('Sei sicuro di voler eliminare questo blog?')) {
                        $.ajax({
                            url: 'elimina_blog.php',
                            type: 'POST',
                            data: { blog_id: blogId },
                            dataType: 'html',
                            success: function(response) {
                                if (response.trim() === 'success') {
                                    $('#blog_' + blogId).remove(); // Rimuove la riga del blog dalla tabella

                                    // Controlla se ci sono altre righe nella tabella
                                    if ($('#autore tbody tr').length === 0) {
                                        $('#autore tbody').html('<tr><td colspan="5">Nessun blog trovato.</td></tr>');
                                    }

                                } else {
                                    alert('Errore durante l\'eliminazione del blog.');
                                }
                            },
                            error: function() {
                                alert('Errore durante la richiesta.');
                            }
                        });
                    }
                });
            });
        </script>


    </head>
    <body>
        <?php include("header.php"); ?>
        <main>
        <div class="container">
            <h3>Profilo:</h3>
            <div class="profile-header">
                <div class="profile-info">
                    <img src="<?php echo $immagine; ?>" alt="Foto Profilo" class="round-image" id="profile-image">
                    <div id="username-info"><?php echo $username; ?>
                        <?php if ($_SESSION['premium_status'] == 'attivo') : ?>
                            <i class='bx bxs-star'></i>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="stats-container">
                    <div class="stat-box" id="seguaci-box">
                    <?php if ($premium == "attivo") : ?>
                        <a href="seguaci.php?id=<?php echo $id; ?>"><h4>Seguaci</h4></a>
                        <p><?php echo $n_seguaci; ?></p>
                        <?php else : ?>
                            <h4>Seguaci</h4>
                            <p><?php echo $n_seguaci; ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="stat-box" id="seguiti-box">
                    <?php if ($premium == "attivo") : ?>
                        <a href="seguiti.php?id=<?php echo $id; ?>"><h4>Seguiti</h4></a>
                        <p><?php echo $n_seguiti; ?></p>
                        <?php else : ?>
                            <h4>Seguiti</h4>
                            <p><?php echo $n_seguiti; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <hr>
            <h3>Blog Autore:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Titolo</th>
                        <th>Seguaci</th>
                        <th>Categorie</th>
                        <th>Gestisci</th>
                        <th>Elimina</th>
                    </tr>
                </thead>
                <tbody id="autore">
                <?php if ($result_blog->num_rows > 0): ?>
                    <?php while ($blog = $result_blog->fetch_assoc()): ?>
                        <tr id="blog_<?php echo $blog['IdBlog']; ?>">
                            <td><?php echo ($blog['TitoloBlog']) ?></td>
                            <td><?php echo ($blog['N_Follow']) ?></td>
                            <td><?php echo ($blog['Temi']) ?></td>
                            <td>
                            <form method="post" action="visualizza_blog.php">
                                    <input type="hidden" name="blog_id" value="<?= $blog['IdBlog'] ?>">
                                    <button type="submit" class="edit-button">Visualizza</button>
                            </form>
                            </td>
                            <td>
                            <form class="delete-blog-form" data-blog-id="<?php echo $blog['IdBlog']; ?>">
                                <button type="button" class="delete-button">Elimina</button>
                            </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nessun blog trovato.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            <hr>
            <h3>Blog Coautore:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Titolo</th>
                        <th>Seguaci</th>
                        <th>Categorie</th>
                        <th>Gestisci</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_coautore->num_rows > 0) : ?>
                        <?php while ($blog = $result_coautore->fetch_assoc()) : ?>
                            <tr>
                                <td> <?php echo $blog['TitoloBlog'] ?> </td>
                                <td> <?php echo $blog['N_Follow'] ?> </td>
                                <td> <?php echo $blog['Temi'] ?> </td>
                                <td>
                                <form method='post' action='blog_profile.php'>
                                <input type='hidden' name='blog_id' value='<?php echo $blog['IdBlog'] ?>'>
                                <button type='submit' class='edit-button'>Visualizza</button>
                                </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan='4'>Nessun blog trovato.</td>
                            </tr>
                    <?php endif; ?>                    
                </tbody>
            </table>
            <hr>
        </div>
        </main>
        <?php include("footer.php"); ?>
    </body>
</html>
