<?php
session_start();
include("connessione.php");

if (!isset($_SESSION["idUtente"])) {
    $_SESSION['errore'] = 'Utente non loggato. Effettua il login per continuare.';
    header("Location: login.php");
    exit;
}

$id_utente = $_SESSION["idUtente"];
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 5;
$offset = ($page - 1) * $itemsPerPage;

// Recupera i post dei blog che l'utente segue
$select_post_query = $mysqli->prepare("SELECT Utente.Username, Utente.FotoProfilo, Post.TitoloPost AS TitoloPost
                      FROM Utente 
                      NATURAL JOIN Post 
                      WHERE Post.IdUtente <> ? AND Post.IdBlog IN (SELECT IdBlog FROM FollowBlog WHERE IdUtente = ?)
                      ORDER BY Post.Data DESC");
$select_post_query->bind_param("ii", $id_utente, $id_utente);
$select_post_query->execute();
$result_post = $select_post_query->get_result();
$total_posts = $result_post->num_rows;

// Recupera i blog degli utenti che l'utente segue
$select_blog_query = $mysqli->prepare("SELECT Utente.Username, Utente.FotoProfilo, Blog.TitoloBlog AS TitoloBlog
                      FROM Utente 
                      NATURAL JOIN Blog 
                      WHERE Blog.IdUtente IN (SELECT IdUtenteSeguito FROM FollowUtente WHERE IdUtenteSeguace = ?)
                      AND Blog.IdBlog NOT IN (SELECT IdBlog FROM FollowBlog WHERE IdUtente = ?)");
$select_blog_query->bind_param("ii", $id_utente, $id_utente);
$select_blog_query->execute();
$result_blog = $select_blog_query->get_result();
$total_blogs = $result_blog->num_rows;

// Recupera gli utenti consigliati
$select_utenti = $mysqli->prepare("SELECT Utente.Username, Utente.Fotoprofilo AS Foto 
                                   FROM Utente 
                                   WHERE Utente.IdUtente <> ? AND Utente.IdUtente NOT IN (SELECT IdUtenteSeguito FROM FollowUtente WHERE IdUtenteSeguace = ?) 
                                   ORDER BY N_Seguaci DESC");
$select_utenti->bind_param("ii", $id_utente, $id_utente);
$select_utenti->execute();
$result_utenti = $select_utenti->get_result();
$total_utenti = $result_utenti->num_rows;

$total_elements = $total_posts + $total_blogs + $total_utenti;
$total_pages = ceil($total_elements / $itemsPerPage);

$current_offset = 0;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoDicoDaHomepage</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style_homepage.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
</head>
<body>
    <?php include("header.php"); ?>
    <main>
    <!-- Visualizzazione dei post -->
    <?php if ($result_post->num_rows > 0): ?>       
        <?php 
        $displayed = 0;
        while ($post = $result_post->fetch_assoc()): 
            if ($displayed >= $offset && $displayed < $offset + $itemsPerPage):
        ?>
            <div class="post">
                <h3>Post</h3>
                <h4><?php echo $post['TitoloPost']; ?></h4>
                <div class='post-info'>
                    <img class="round-image" src="<?php echo $post['FotoProfilo']; ?>" alt="Foto Profilo">
                    <a href='user_profile.php?username=<?php echo $post['Username']; ?>'>
                        <?php echo $post['Username']; ?>
                    </a>
                </div>
                <a href='post_profile.php?titolo_post=<?php echo $post['TitoloPost']?>' class='view-profile-button'>Visualizza</a>
            </div>
        <?php 
            endif;
            $displayed++;
        endwhile; 
        $current_offset = $displayed;
        ?>
    <?php endif; ?>
    <br><br>
    <!-- Visualizzazione dei nuovi blog -->
    <?php if ($current_offset < $offset + $itemsPerPage && $result_blog->num_rows > 0): ?>
        <?php 
        $displayed = 0;
        while ($blog = $result_blog->fetch_assoc()): 
            if ($displayed >= $offset - $current_offset && $displayed < $offset + $itemsPerPage - $current_offset):
        ?>
            <div class="blog">
                <h3>Nuovo Blog</h3>
                <h4><?php echo $blog['TitoloBlog']; ?></h4>
                <div class='blog-info'>
                    <img class="round-image" src="<?php echo $blog['FotoProfilo']; ?>" alt="Foto Profilo">
                    <a href='user_profile.php?username=<?php echo $blog['Username']; ?>'>
                        <?php echo $blog['Username']; ?>
                    </a>
                </div>
                <a href='blog_profile.php?titolo_blog=<?php echo $blog['TitoloBlog']?>' class='view-profile-button'>Visualizza</a>
            </div>
        <?php 
            endif;
            $displayed++;
        endwhile; 
        $current_offset += $displayed;
        ?>
    <?php endif; ?>
    <br><br>
    <!-- Visualizzazione degli utenti consigliati -->
    <?php if ($current_offset < $offset + $itemsPerPage && $result_utenti->num_rows > 0): ?>
        <?php 
        $displayed = 0;
        while ($utente = $result_utenti->fetch_assoc()): 
            if ($displayed >= $offset - $current_offset && $displayed < $offset + $itemsPerPage - $current_offset):
        ?>
            <div class="utenti">
                <h3>Utente Consigliato</h3>
                <div class='utenti-info'>
                    <img class="round-image" src='<?php echo $utente['Foto']; ?>' alt="Foto Profilo">
                    <p><?php echo $utente['Username']; ?></p>
                </div>
                <a href='user_profile.php?username=<?php echo $utente['Username']?>' class='view-profile-button'>Visualizza</a>
            </div>
        <?php 
            endif;
            $displayed++;
        endwhile; 
        ?>
    <?php endif; ?>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>">&#9664; Indietro</a>
        <?php endif; ?>
        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>">Avanti &raquo;</a>
        <?php endif; ?>
    </div>
    </main>
    <?php include("footer.php"); ?>
</body>
</html>
