<!DOCTYPE html>
<meta lang="it">
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LoDicoDaSbadato</title>
        <link rel="stylesheet" href="style/style.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
        <script>
            $(document).ready(function(){
                $("#FormForgot").validate({
                    rules:{
                        username: {
                            required: true
                        },
                        email:{
                            required: true,
                            email: true,
                            CaratteriEmail: true
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
                            required: "Inserisci un username"
                        },
                        email: {
                            required: "Inserisci un'email",
                            email: "Inserisci un'email valida"
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
                    submitHandler: function(form) {
                        form.submit()                       
                    }
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
                font-size: 15px;
            }
        </style>
    </head>
    <body>
        <header>
            <a href="home.php" class="logo"><h2>LoDicoDa</h2></a>
            <nav class="navigation">
                <button class="login" onclick="location.href='login.php';">Login</button>
                <button class="signup" onclick="location.href='registrazione.php';">Sign Up</button>
            </nav>
        </header>

        <div class="formbox-recpass">
            <h2>Reimposta Password</h2>
            <form id="FormForgot" class="recpass" action="reset_pass.php" method="post">
                <input type="text" id="username" name="username" placeholder="Username" class="input">
                <i class='bx bxs-user' ></i>
                <input type="email" id="email" name="email" placeholder="Email" class="input">
                <i class='bx bxs-envelope' ></i>
                <input type="password" id="password" name="password" placeholder="Nuova Password" class="input">
                <i class='bx bxs-lock-alt' ></i>
                <input type="password" id="c_password" name="c_password" placeholder="Conferma Password" class="input">
                <i class='bx bxs-lock-alt' ></i>
                <input type="submit" class="submit" value="Conferma">
            </form>
        </div>
        <?php
            include("footer.php");
        ?>
    </body>
</html>