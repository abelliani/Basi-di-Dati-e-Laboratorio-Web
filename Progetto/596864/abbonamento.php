<?php
include("connessione.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST"){
    if ($_SESSION["username"]){
        $username = $_SESSION["username"];
        $id_utente = $_SESSION["idUtente"];
        try {
            if (!isset($_POST["tipo"]) || !isset($_POST["credit-card"]) || !isset($_POST["intestatario"])){
                throw new Exception("Compila tutti i campi");
            } 

            $tipo = $_POST["tipo"];
            $credit_card = $_POST["credit-card"];
            $intestatario = $_POST["intestatario"];
            $data_pagamento = date("y-m-d");

            if (isset($_SESSION["premium_status"])){
                $premium_status = $_SESSION["premium_status"];
            }

            if ($premium_status === "attivo") {
                $_SESSION["error_message"] = "L'utente è già premium";
                header("Location: premium.php");
                exit();
            }

            $check_payment_query = "SELECT * FROM Pagamento WHERE N_Carta = ?";
            $stmt_check = $mysqli->prepare($check_payment_query);
            $stmt_check->bind_param("i", $credit_card);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                $_SESSION["error_message"] = "Una carta di credito con questo numero è già associata a un account";
                header("Location: premium.php");
                exit();
            }

            // Controllo se lo stato dell'abbonamento è "scaduto"
            if ($premium_status === "scaduto") {
                $update_query = $mysqli->prepare("UPDATE Pagamento SET N_Carta = ?, Intestatario = ?, DataPagamento = ? WHERE IdUtente = ?");
                $update_query->bind_param("sssi", $credit_card, $intestatario, $data_pagamento, $id_utente);

                if ($update_query->execute()) {
                    $data_scadenza = '';
                    if ($tipo === 'mensile') {
                        $data_scadenza = date('Y-m-d', strtotime('+1 month', strtotime($data_pagamento)));
                    } elseif ($tipo === 'annuale') {
                        $data_scadenza = date('Y-m-d', strtotime('+1 year', strtotime($data_pagamento)));
                    } else {
                        throw new Exception("Tipo di abbonamento non valido");
                    }

                    $insert_query_premium = $mysqli->prepare("INSERT INTO Premium(IdUtente, Tipo, ScadenzaAbbonamento) VALUES (?, ?, ?)");
                    $insert_query_premium->bind_param("iss", $id_utente, $tipo, $data_scadenza);

                    if (!$insert_query_premium->execute()) {
                        throw new Exception("Errore durante l'inserimento dell'abbonamento");
                    }
                    $update_query->close();
                    $insert_query_premium->close();

                    $_SESSION["premium_status"] = "attivo";
                    header("Location: profilo.php");
                    exit();
                } else {
                    throw new Exception("Errore durante l'aggiornamento del pagamento");
                }
            }

            // Controllo se lo stato dell'abbonamento è "none"
            if ($premium_status === "none") {
                $insert_query = $mysqli->prepare("INSERT INTO Pagamento(N_Carta, IdUtente, Intestatario, DataPagamento) VALUES (?, ?, ?, ?)");
                $insert_query->bind_param("siss", $credit_card, $id_utente, $intestatario, $data_pagamento);

                if ($insert_query->execute()) {
                    $data_scadenza = '';
                    if ($tipo === 'mensile') {
                        $data_scadenza = date('Y-m-d', strtotime('+1 month', strtotime($data_pagamento)));
                    } elseif ($tipo === 'annuale') {
                        $data_scadenza = date('Y-m-d', strtotime('+1 year', strtotime($data_pagamento)));
                    } else {
                        throw new Exception("Tipo di abbonamento non valido");
                    }

                    $insert_query_premium = $mysqli->prepare("INSERT INTO Premium(IdUtente, Tipo, ScadenzaAbbonamento) VALUES (?, ?, ?)");
                    $insert_query_premium->bind_param("iss", $id_utente, $tipo, $data_scadenza);

                    if (!$insert_query_premium->execute()) {
                        throw new Exception("Errore durante l'inserimento dell'abbonamento");
                    }
                    $insert_query->close();
                    $insert_query_premium->close();

                    $_SESSION["premium_status"] = "attivo";
                    header("Location: profilo.php");
                    exit();
                } else {
                    throw new Exception("Errore durante l'inserimento del pagamento");
                }
            }
            $mysqli->close();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    
    } else{
        echo "Utente non autorizzato";
    }
} else {
    echo "Richiesta non valida";
}