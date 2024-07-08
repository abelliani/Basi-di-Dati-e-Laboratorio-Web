<?php
session_start();

$errore = '';
if (isset($_SESSION['errore'])) {
    $errore = $_SESSION['errore'];
    unset($_SESSION['errore']);
}
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LoDicoDaLogin</title>
        <link rel="stylesheet" href="style/style.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
        <script>
            $(document).ready(function(){
                $("#FormLogin").validate({
                    rules: {
                        username: { 
                            required: true
                        },
                        password: {
                            required: true
                        }
                    },
                    messages: {
                        username: {
                            required: "inserisci un username"
                        },
                        password: {
                            required: "inserisci una password"
                        }
                    }
                });
            });
        </script>
        <style>
            .error {
                color: red;
                margin-top: 5px;
                display: block;
                font-size: 12px;
            }
        </style>
    </head>
    <body>
        <header>
            <a href="home.php" class="logo"><h2>LoDicoDa</h2></a>
            <nav class="navigation">
                <button class="login" onclick="location.href='login.php';">Accedi</button>
                <button class="signup" onclick="location.href='registrazione.php';">Registrati</button>
            </nav>
        </header>

       <div class="formbox-login">
            <h2>Login</h2>
            <form id="FormLogin" class="form" action="signin.php" method="post">
                <input type="text" id="username" name="username" placeholder="Username" class="input">
                <i class='bx bxs-user'></i>
                <br>
                <input type="password" id="password" name="password" placeholder="Password" class="input">
                <i class='bx bxs-lock-alt' ></i>
                <a class="forgot" href="forgot.php">Hai dimenticato la Password?</a>
                <br><br>
                <input type="submit" class="submit" value="Login">
                <p>Non hai un account? <a href="registrazione.php">Registrati</a></p>
                <?php if ($errore): ?>
                <div class="error"><?php echo $errore; ?></div>
                <?php endif; ?>
            </form>
        </div>
        <?php
        include("footer.php");
        ?>
    </body>
</html>