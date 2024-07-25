<!DOCTYPE html>
<meta lang="it">
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style/style.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    </head>
    <style>
        .navigation {
            position: relative
        }
        .navigation .menu {
            width: 200px;
            height: 50px;
            background: transparent;
            border: 2px solid rgb(57, 57, 57);
            outline: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.1em;
            color: white;
            font-weight: 500;
            margin-left: 40px;
            transition: .5s;
        }

        .navigation .menu:hover {
            background: rgb(57, 57, 57);
            color: #fff6e6;
        }

        .sidebar {
            display: none;
            position: absolute;
            right: 0;
            background-color: rgb(57, 57, 57);
            z-index: 1;
            padding: 10px;
            width: 200px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            padding: 10px 0;
            text-align: right;
        }

        .sidebar a i {
            margin-right: 10px; 
        }

        .sidebar a:hover {
            background-color: #eee;
            color: black;
        }

        .navigation:hover .sidebar {
            display: block;
        }
    </style>
    <script>
        $(document).ready(function(){
            $(".menu").click(function(){
                $(".sidebar").slideToggle("slow");
            });
        });
    </script>
    <body>
        <header>
        <a href="homepage.php" class="logo"><h2>LoDicoDa</h2></a>
            <div class="navigation">
                <button class="menu">Menu</button>
                    <div class="sidebar">
                        <a href="profilo.php"><i class='bx bxs-user-account'></i>Profilo</a>
                        <a href="ricerca.php"><i class='bx bxs-search'></i>Ricerca</a>
                        <a href="creablog.php"><i class='bx bxs-edit'></i>Crea Blog</a>
                        <a href="creapost.php"><i class='bx bxs-edit'></i>Crea Post</a>
                        <a href="modifica.php"><i class='bx bxs-edit'></i>Modifica</a>
                        <a href="newcoautore.php"><i class='bx bxs-edit'></i>Coautore</a>
                        <a href="sottocategoria.php"><i class='bx bxs-edit'></i>Sottocategoria</a>
                        <a href="premium.php"><i class='bx bxs-crown'></i>Premium</a>
                        <a href="logout.php"><i class='bx bxs-log-out' ></i>Logout</a>
                    </div>
            </div>
        </header>
    </body>
</html>