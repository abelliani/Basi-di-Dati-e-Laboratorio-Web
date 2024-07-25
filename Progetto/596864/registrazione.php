<?php

?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LoDicoDaRegistrazione</title>
        <link rel="stylesheet" href="style/style.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
        <script>
            $(document).ready(function(){
                $("#FormRegistrazione").validate({
                    rules:{
                        username: {
                            required: true,
                            maxlength: 35
                        },
                        email:{
                            required: true,
                            email: true,
                            CaratteriEmail: true,
                            maxlength: 50
                        },
                        password:{
                            required: true,
                            minlength: 8,
                            CaratteriPassword: true
                        },
                        c_password: {
                            required: true,
                            minlength: 8,
                            CaratteriPassword: true,
                            equalTo: "#password",
                        }
                    },
                    messages:{
                        username: {
                            required: "Inserisci un username",
                            maxlength: "Username troppo lungo"
                        },
                        email: {
                            required: "Inserisci un'email",
                            email: "Inserisci un'email valida",
                            maxlength: "Email troppo lunga"
                        },
                        password: {
                            required: "Inserisci una password",
                            minlength: "La password deve essere lunga almeno 8 caratteri"                            
                        },
                        c_password: {
                            required: "Inserisci una password",
                            minlength: "La password deve essere lunga almeno 8 caratteri",
                            equalTo: "Le due password inserite non corrispondono"
                        }
                    },
                    errorPlacement: function(error, element) {
                        error.insertAfter(element);
                    },           
                });
                function check_username(username){
                    $.ajax({
                        type: "POST",
                        url: "check_username.php",
                        data:  {username: username},
                        success: function(response) {
                            if (response === "disponibile") {
                                $("#check-username").html("<span style='color: green;'>Username disponibile</span>");
                            } else {
                                $("#check-username").html("<span style='color: red;'>Username non disponibile</span>");
                            }
                        }
                    })
                }
                function check_email(email){
                    $.ajax({
                        type: "POST",
                        url: "check_email.php",
                        data:  {email: email},
                        success: function(response) {
                            if (response === "non_registrata") {
                                $("#check-email").html("<span style='color: green;'>Email disponibile</span>");
                            } else {
                                $("#check-email").html("<span style='color: red;'>Email non disponibile</span>");
                            }
                        }
                    })
                }
                $("#username").keyup(function(){
                    var username = $(this).val();
                    check_username(username);
                });

                $("#email").keyup(function(){
                    var email = $(this).val();
                    check_email(email);
                });
                $.validator.addMethod("CaratteriEmail", function(value, element) {
                    return /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i.test(value); 
                }, "Inserisci email valida");
                $.validator.addMethod("CaratteriPassword", function(value, element) {
                    return /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%&*])[a-zA-Z0-9!@#$%&*]+$/.test(value); 
                }, "La password deve contenere almeno una lettera maiuscola, un numero e un carattere speciale");
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

        <div class="formbox-registrazione">
            <h2>Registrazione</h2>
            <form id="FormRegistrazione" class="registrazione" action="signup.php" method="post">
                <input type="text" id="username" name="username" placeholder="Username" class="input">
                <div id="check-username" class="check-username"></div>
                <div id="username-error" class="error"></div>
                <i class='bx bxs-user' ></i>
                <input type="email" id="email" name="email" placeholder="Email" class="input">
                <div id="check-email" class="check-email"></div>
                <div id="email-error" class="error"></div>
                <i class='bx bxs-envelope' ></i>
                <input type="password" id="password" name="password" placeholder="Password" class="input">
                <div id="password-error" class="error"></div>
                <i class='bx bxs-lock-alt' ></i>
                <input type="password" id="c_password" name="c_password" placeholder="Conferma Password" class="input">
                <div id="c_password-error" class="error"></div>
                <i class='bx bxs-lock-alt' ></i>
                <input type="submit" class="submit" value="Registrati">
                <p>Hai già un account? <a href="login.php">Login</a></p>
                <div id="error-message" style="color: red;"></div>
            </form>
        </div>
        <?php
        include("footer.php");
        ?>
    </body>
</html>