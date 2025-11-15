<?php
include 'n.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Startpagina</title>
    <style>
        body {
            background-color: #fff8dc;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        h1 {
            color: darkblue;
        }
        .menu {
            margin-top: 40px;
        }
        .menu a {
            display: inline-block;
            margin: 15px;
            padding: 15px 25px;
            background-color: #f0c040;
            color: black;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }
        .menu a:hover {
            background-color: #ffa500;
        }
    </style>
</head>
<body>

    <h1>Productbeheer</h1>

    <div class="menu">
        <a href="w.php">📋 Productenlijst</a>
        <a href="check.php">🔄 Verkochte stuks updaten</a>
        <a href="dat.php">📊 Maandrapport producten</a>
       <a href="dv.php">📅 Datum veranderen</a>
        <a href="sv.php">📦 Stocks resetten</a>


    </div>

</body>
</html>
