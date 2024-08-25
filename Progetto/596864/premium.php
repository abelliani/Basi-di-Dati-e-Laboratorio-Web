<?php
session_start();
include("connessione.php");
if (!isset($_SESSION["idUtente"])) {
    $_SESSION['errore'] = 'Utente non loggato. Effettua il login per continuare.';
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LoDicoDaPremium</title>
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

            p {
                font-size: 15px;
                text-align: right;
            }

            .formbox-premium h2 {
                text-align: center;
                padding-top: 40px;
            }
            .error-message {
                color: red;
                font-size: 12px;
                margin-top: 10px;
            }
        </style>

        <script>
            $(document).ready(function(){
                $("#FormAbbonamento").validate({
                    rules: {
                        "tipo": {
                            required: true
                        },
                        "credit-card": {
                            required: true,
                            creditcard: true  // Usa la regola predefinita per la validazione delle carte di credito
                        },
                        "intestatario": {
                            required: true
                        }
                    },
                    messages: {
                        "tipo": {
                            required: "Seleziona un tipo di abbonamento."
                        },
                        "credit-card": {
                            required: "Inserisci il numero della carta di credito.",
                            creditcard: "Inserisci un numero di carta di credito valido."
                        },
                        "intestatario": {
                            required: "Inserisci il nome dell'intestatario."
                        }
                    }
                });
            });
        </script>
    </head>
    <body>
        <?php
            include("header.php");
        ?>

       <div class="formbox-premium">
            <h2>Abbonamento</h2>
            <form id="FormAbbonamento" class="form" action="abbonamento.php" method="post">
                <select id="tipo" name="tipo" placeholder="Tipo" class="input">
                    <option value="default" selected disabled hidden>Tipo</option>
                    <option value="mensile">Mensile</option>
                    <option value="annuale">Annuale</option>
                </select>
                <br><br>
                <input type="text" id="credit-card" name="credit-card" placeholder="Numero Carta" class="input">
                <i class='bx bxs-credit-card'></i>
                <input type="text" id="intestatario" name="intestatario" placeholder="Intestatario" class="input">
                <i class='bx bxs-user' ></i>
                <br><br>
                <input type="submit" class="submit" value="Abbonati">
                <div id="error-message" class="error-message">
                    <?php 
                        if (isset($_SESSION["error_message"])) {
                            echo $_SESSION["error_message"]; 
                            unset($_SESSION["error_message"]);
                        }
                    ?>
                </div>

                <p>
                    Mensile: 4,99€ |
                    Annuale: 49,99€
                </p>
            </form>
        </div>
        <?php include("footer.php"); ?>
    </body>
</html>