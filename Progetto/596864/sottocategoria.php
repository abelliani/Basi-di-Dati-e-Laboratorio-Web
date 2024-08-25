<?php
session_start();
if (!isset($_SESSION["idUtente"])) {
    $_SESSION['errore'] = 'Utente non loggato. Effettua il login per continuare.';
    header("Location: login.php");
    exit;
}

include("connessione.php");

if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
    $blog_id = intval($_POST['blogId']);
    $id_utente = $_SESSION["idUtente"];
    
    $select_query_categoria = "SELECT C.IdCategoria, C.Tema 
    FROM Categoria AS C 
    JOIN Sottocategoria AS SC ON C.IdCategoria = SC.IdSottocategoria 
    JOIN Associa ON SC.IdCategoria = Associa.IdCategoria 
    JOIN Blog AS B ON Associa.IdBlog = B.IdBlog 
    WHERE B.IdBlog = $blog_id AND B.IdUtente = $id_utente 
    ORDER BY C.Tema ASC";
    
    $result_categoria = $mysqli->query($select_query_categoria);
    $options_categoria = "<option value='default' selected disabled hidden>Sottocategoria</option>";
    
    if ($result_categoria->num_rows > 0) {
        while ($row = $result_categoria->fetch_assoc()) {
            $options_categoria .= '<option value="' . $row['IdCategoria'] . '">' . $row['Tema'] . '</option>';
        }
        $result_categoria->free();
    }
    
    echo $options_categoria;
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoDicoDaSottocategoria</title>
    <link rel="stylesheet" href="style/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <style>            
        .error {
            color: red;
            margin-top: 5px;
            display: block;
            font-size: 11px;
        }

        .success {
            color: green;
            font-size: 11px;
        }

        #categoria {
            overflow-y: auto;
        }
    </style>
    <script>
        $(document).ready(function() {
            $('#blog').change(function() {
                var blogId = $(this).val();
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: { ajax: '1', blogId: blogId },
                    success: function(response) {
                        $('#categoria').html(response);
                    }
                });
            });
        });
    </script>
</head>
<body>
    <?php
        include("header.php");
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
    ?>
    <div class="formbox-categoria">
        <h2>Aggiungi Sottocategoria</h2>
        <form id="FormCategoria" class="form" action="new_categoria.php" method="post">
            <select id="blog" name="blog" placeholder="blog" class="input">
                <option value="default" selected disabled hidden>Blog</option>
                <?php echo $options_blog; ?>
            </select>
            <br><br>
            <select id="categoria" name="categoria" placeholder="categoria" class="input">
                <option value="default" selected disabled hidden>Sottocategoria</option>
            </select>
            <br><br>
            <input type="submit" class="submit" value="Aggiungi Sottocategoria">
            <?php if (!empty($_SESSION['message'])) {
                $message = $_SESSION['message'];
                $message_class = strpos($message, 'successo') !== false ? 'success' : 'error';
            ?>
            <p class="message <?php echo $message_class; ?>"><?php echo $message; ?></p>
            <?php unset($_SESSION['message']); ?>
            <?php } ?>
        </form>
    </div>
    <?php include("footer.php"); ?>
</body>
</html>
