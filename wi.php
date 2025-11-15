<?php
include 'hi.php';
$mysqli->select_db("producten");

if (!isset($_GET['id'])) {
    die("Geen product ID opgegeven.");
}


$id = (int)$_GET['id'];

// Ophalen van product
$sql = "SELECT * FROM artikelen WHERE productid=?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Product niet gevonden.");
}

// Als formulier verzonden is
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naam = $_POST['naam'];
    $prijs = (float)$_POST['prijs'];
    $voorraad = (int)$_POST['voorraad'];
    $verkocht = (int)$_POST['gekochte_stuks'];

    $sql = "UPDATE artikelen SET naam=?, prijs=?, nog_in_voorraad=?, gekochte_stuks=? WHERE productid=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sdiii", $naam, $prijs, $voorraad, $verkocht, $id);
    $stmt->execute();

    echo "<p class='melding groen'>✅ Product  bijgewerkt</p>";

    // Herlaad opnieuw de actuele data
    $sql = "SELECT * FROM artikelen WHERE productid=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Product wijzigen</title>
    <style>
        body {
            background-color: #fff8dc;
            font-family: Arial, sans-serif;
            padding: 40px;
        }
        
        h1 {
            color: darkred;
            text-align: center;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            margin-top: 20px;
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #45a049;
        }
        .melding {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .groen {
            color: green;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
        a:hover {
            color: darkred;
        }
    </style>
</head>
<body>

<h1>✏️ Product wijzigen</h1>

<form method="post">
    <label>Naam:</label>
    <input type="text" name="naam" value="<?php echo htmlspecialchars($product['naam']); ?>" required>

    <label>Prijs:</label>
    <input type="number" step="0.01" name="prijs" value="<?php echo $product['prijs']; ?>" required>

    <label>Voorraad:</label>
    <input type="number" name="voorraad" value="<?php echo $product['nog_in_voorraad']; ?>" required>

    <label>Verkocht:</label>
    <input type="number" name="gekochte_stuks" value="<?php echo $product['gekochte_stuks']; ?>" required>

    <button type="submit">Opslaan</button>
</form>


<a href="w.php">⬅️ Terug naar overzicht</a>

</body>
</html>