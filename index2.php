<?php
include 'hi.php';
include 'n.php';
$mysqli->select_db("producten");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naam     = $_POST['naam'];
    $prijs    = $_POST['prijs'];
    $voorraad = $_POST['voorraad'];
    $datum    = $_POST['datum']; 
    $category = $_POST['category']; // veld voor category

    // Bestand uploaden
    $fotoNaam = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $extensie = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $fotoNaam = uniqid() . '.' . $extensie;
        $doelmap  = 'uploads/' . $fotoNaam;
        move_uploaded_file($_FILES['foto']['tmp_name'], $doelmap);
    }

    // Query met gekozen datum en category
    $sql = "INSERT INTO artikelen (naam, prijs, nog_in_voorraad, foto, datum, category)  
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sdisss", $naam, $prijs, $voorraad, $fotoNaam, $datum, $category);

    if ($stmt->execute()) {
        echo "<p class='melding groen'>✅ Product succesvol toegevoegd!</p>";
        if ($fotoNaam) {
            echo "<p style='text-align:center;'><img src='uploads/$fotoNaam' alt='Foto' style='max-width:200px;'></p>";
        }
        echo "<p style='text-align:center;'>📅 Toegevoegd op: " . $datum . "</p>";
        echo "<p style='text-align:center;'>📂 Category: " . htmlspecialchars($category) . "</p>";
    } else {
        echo "<p class='melding rood'>❌ Fout bij toevoegen: " . $mysqli->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Nieuw product toevoegen</title>
    <style>
        body {
            background-color: #fdf5e6;
            font-family: Arial, sans-serif;
            padding: 40px;
        }
        h1 {
            color: darkgreen;
            text-align: center;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff8dc;
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
        input[type="number"],
        input[type="file"],
        input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        input[type="submit"] {
            margin-top: 20px;
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .melding {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .groen { color: green; }
        .rood { color: red; }
    </style>
</head>
<body>
<br><br>
<h1>➕ Nieuw product toevoegen</h1>

<form method="post" enctype="multipart/form-data">
    <label>Naam:</label>
    <input type="text" name="naam" required>

    <label>Prijs:</label>
    <input type="number" step="0.01" name="prijs" required>

    <label>Voorraad:</label>
    <input type="number" name="voorraad" required>

    <label>Foto uploaden:</label>
    <input type="file" name="foto" accept="image/*">

    <label>Datum:</label>
    <input type="date" name="datum" value="<?php echo date('Y-m-d'); ?>" required>

    <label>Levrancier:</label>
    <input type="text" name="category" required>

    <input type="submit" value="Toevoegen">
</form>

</body>
</html>