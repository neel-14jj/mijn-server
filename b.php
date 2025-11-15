<?php
include 'hi.php';
$mysqli->select_db("producten");

// Alle producten ophalen
$sql = "SELECT productid, naam, category FROM artikelen";
$result = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Producten overzicht</title>
    <style>
        body { background-color:#fff8dc; font-family:Arial,sans-serif; padding:30px; }
        h1 { text-align:center; color:darkred; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { border:1px solid #ccc; padding:10px; text-align:center; }
        th { background:#f0c040; }
        tr:nth-child(even) { background:#fdf5e6; }
        input[type="text"], input[type="number"] {
            width: 100px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
    </style>
</head>
<body>
<h1>📋 Producten overzicht november 2025</h1>

<?php
if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr>
            <th>Naam</th>
            <th>Merk</th>
            <th>Prijs</th>
            <th>Voorraad</th>
            <th>Verkocht</th>
          </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['naam']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td><input type='number' step='0.01' name='prijs_".$row['productid']."' placeholder='€'></td>";
        echo "<td><input type='number' name='voorraad_".$row['productid']."''></td>";
        echo "<td><input type='number' name='verkocht_".$row['productid']."' '></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='text-align:center;'>Geen producten gevonden.</p>";
}
?>

</body>
</html>