<?php
session_start();
include("connessione.php");

if (!isset($_SESSION["idUtente"])) {
    $_SESSION['errore'] = 'Utente non loggato. Effettua il login per continuare.';
    header("Location: login.php");
    exit;
}

if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
    include("connessione.php");
    $categoria_id = $_POST['CategoriaId'];
    $id_utente = $_SESSION["idUtente"];
    
    $select_query_categoria = "SELECT C.IdCategoria, C.Tema 
    FROM Categoria AS C 
    JOIN Sottocategoria AS SC ON C.IdCategoria = SC.IdSottocategoria  
    WHERE SC.IdCategoria = $categoria_id 
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




$select_query = "SELECT IdCategoria, Tema FROM Categoria ORDER BY Tema";
$result = $mysqli->query($select_query);

$options = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $options .= '<option value="' . $row['IdCategoria'] . '">' . $row['Tema'] . '</option>';
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoDicoDaBlog</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style_creablog.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#FormBlog").validate({
                rules: {
                    titolo_blog: {
                        required: true,
                        maxlength: 30
                    },
                    categoria: {
                        required: true
                    },
                    descrizione: {
                        required: true,
                        minlength: 1
                    },
                    immagine: {
                        TipoFile: ["jpg", "jpeg", "png", "gif"]
                    }
                },
                messages: {
                    titolo_blog: {
                        required: "inserisci un titolo",
                        maxlength: "Titolo troppo lungo"
                    },
                    categoria: {
                        required: "inserisci una categoria"
                    },
                    descrizione: {
                        required: "inserisci una descrizione",
                        minlength: "devi inserire almeno un carattere"
                    },
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            $.validator.addMethod("TipoFile", function(value, element, param) {
                var fileExtension = value.split('.').pop().toLowerCase();
                return this.optional(element) || $.inArray(fileExtension, param) !== -1;
            }, "Tipo del file non accettato");

            var maxChars = 255;
            $('#charCount').text(maxChars + ' caratteri rimanenti');
            $('#descrizione').on('input', function() {
                var currentLength = $(this).val().length;
                var charsRemaining = maxChars - currentLength;
                if (charsRemaining < 0) {
                    charsRemaining = 0;
                    $(this).val($(this).val().substring(0, maxChars));
                }
                $('#charCount').text(charsRemaining + ' caratteri rimanenti');
            });

            $('#categoria').change(function() {
                var CategoriaId = $(this).val();
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: { ajax: '1', CategoriaId: CategoriaId },
                    success: function(response) {
                        $('#sottocategoria').html(response);
                    }
                });
            });
        });
    </script>
</head>
<body>
    <?php include("header.php"); ?>
    <main>
        <div class="formbox-blog">
            <h2>Crea Blog</h2>
            <form id="FormBlog" enctype="multipart/form-data" class="form" action="new_blog.php" method="post">
                <input type="text" id="titolo_blog" name="titolo_blog" placeholder="Titolo" class="input">
                <br><br>
                <select id="categoria" name="categoria" placeholder="categoria" class="input">
                    <option value="default" selected disabled hidden>Categoria</option>
                    <?php echo $options; ?>
                </select>
                <br><br>
                <select id="sottocategoria" name="sottocategoria" placeholder="categoria" class="input">
                    <option value="default" selected disabled hidden>Sottocategoria</option>
                </select>
                <textarea id="descrizione" name="descrizione" placeholder="Descrizione" rows="4" cols="50"></textarea>
                <div id="charCount">255 caratteri rimanenti</div>
                <br>
                <input type="file" id="immagine" name="immagine" placeholder="Immagine" class="input">
                <span>File consentiti: jpg, jpeg, png, gif</span>
                <br>
                <input type="submit" class="submit" value="Crea Blog">
                <?php if (!empty($_SESSION['message'])) {
                    $message = $_SESSION['message'];
                    $message_class = strpos($message, 'successo') !== false ? 'success' : 'error';
                ?>
                <p class="message <?php echo $message_class; ?>"><?php echo $message; ?></p>
                <?php unset($_SESSION['message']); ?>
                <?php } ?>
                <div class='back-link'>
                    <a href='profilo.php?'>Torna al profilo</a>
                </div>
            </form>
        </div>
    </main>
    <?php
    include("footer.php");
    ?>
</body>
</html>
