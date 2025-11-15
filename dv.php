<?php
include 'hi.php';
include 'n.php';
$mysqli->select_db("producten");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $oudeMaand = (int)$_POST['oude_maand'];
    $oudeJaar  = (int)$_POST['oude_jaar'];
    $nieuweDatum = $_POST['nieuwe_datum'];

    // Update alle producten van oude maand/jaar naar nieuwe datum
    $sql = "UPDATE artikelen 
            SET datum = ? 
            WHERE MONTH(datum) = ? AND YEAR(datum) = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sii", $nieuweDatum, $oudeMaand, $oudeJaar);

    if ($stmt->execute()) {
        echo "<p style='color:green; text-align:center;'>✅ Alle datums succesvol aangepast!</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>❌ Fout: " . $mysqli->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <br><br>
    <title>Datum_aanpassen</title>
    <style>
        body { font-family: Arial; background:#fdf5e6; padding:40px; }
        form { max-width:400px; margin:0 auto; background:#fff8dc; padding:20px; border-radius:8px; box-shadow:0 0 10px #ccc; }
        label { display:block; margin-top:15px; font-weight:bold; }
        select, input[type=date], input[type=submit] {
            width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:6px;
        }
        input[type=submit] { background:#4caf50; color:white; font-weight:bold; cursor:pointer; }
        input[type=submit]:hover { background:#45a049; }
    </style>
</head>
<body>
    <h1 style="text-align:center; color:darkred;">📅 Datum van producten aanpassen</h1>

    <form method="post">
        <label>Oude maand:</label>
        <select name="oude_maand">
            <?php
            for ($m=1; $m<=12; $m++) {
                echo "<option value='$m'>" . date("F", mktime(0,0,0,$m,1)) . "</option>";
            }
            ?>
        </select>

        <label>Oude jaar:</label>
        <select name="oude_jaar">
            <?php
            for ($y=date("Y")-5; $y<=date("Y")+1; $y++) {
                echo "<option value='$y'>$y</option>";
            }
            ?>
        </select>

        <label>Nieuwe datum:</label>
        <input type="date" name="nieuwe_datum" required>

        <input type="submit" value="Pas datums aan">
    </form>
</body>
</html>