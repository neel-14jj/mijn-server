<?php
include 'hi.php';
include 'n.php';
$mysqli->select_db("producten");

// Waarden uit formulier 
$maand = isset($_POST['maand']) ? (int)$_POST['maand'] : date('m');
$jaar  = isset($_POST['jaar']) ? (int)$_POST['jaar'] : date('Y');

// Query met filter op maand en jaar
$sql = "SELECT * FROM artikelen 
        WHERE MONTH(datum) = ? AND YEAR(datum) = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $maand, $jaar);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Maandrapport</title>
    <style>
        body { background-color:#fff8dc; font-family:Arial; padding:30px; }
        h1 { text-align:center; color:darkred; }
        form { text-align:center; margin-bottom:20px; }
        select, input[type=submit] {
            padding:6px 12px; margin:5px; border-radius:6px; border:1px solid #ccc;
        }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { border:1px solid #ccc; padding:10px; text-align:center; }
        th { background:#f0c040; }
        tr:nth-child(even) { background:#fdf5e6; }
        .product-foto { max-width:80px; max-height:80px; object-fit:cover; border-radius:6px; }
    </style>
</head>
<body>
<br><br>
<h1>📊 Maandrapport producten</h1>

<!-- Formulier om maand en jaar te kiezen -->
<form method="post">
    <label>Maand:</label>
    <select name="maand">
        <?php
        for ($m=1; $m<=12; $m++) {
            $selected = ($m == $maand) ? "selected" : "";
            echo "<option value='$m' $selected>" . date("F", mktime(0,0,0,$m,1)) . "</option>";
        }
        ?>
    </select>

    <label>Jaar:</label>
    <select name="jaar">
        <?php
        for ($y=date("Y")-5; $y<=date("Y")+1; $y++) {
            $selected = ($y == $jaar) ? "selected" : "";
            echo "<option value='$y' $selected>$y</option>";
        }
        ?>
    </select>

    <input type="submit" value="Toon rapport">
</form>

<?php
if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Naam</th><th>Leverancier</th><th>Prijs</th><th>Voorraad</th><th>Verkocht</th><th>Omzet</th></tr>";
    $totaalOmzet = 0;
    while ($row = $result->fetch_assoc()) {
        $omzet = $row['gekochte_stuks'] * $row['prijs'];
        $totaalOmzet += $omzet;

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['naam']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>€" . number_format($row['prijs'], 2, ',', '.') . "</td>";
        echo "<td>" . $row['nog_in_voorraad'] . "</td>";
        echo "<td>" . $row['gekochte_stuks'] . "</td>";
        echo "<td>€" . number_format($omzet, 2, ',', '.') . "</td>";


        echo "</tr>";
    }

    // Maandnaam ophalen
    $maandNaam = date("F", mktime(0, 0, 0, $maand, 1));

    echo "<tr style='color:blue; font-weight:bold; background-color:green;'>
            <th colspan='5'>Totaal omzet van $maandNaam $jaar</th>
            <th>€" . number_format($totaalOmzet, 2, ',', '.') . "</th>
          </tr>";
    echo "</table>";

    // Printknop toevoegen
    echo "<div style='text-align:center; margin-top:20px;'>
            <button onclick=\"window.print()\" 
                    style='padding:10px 20px; background:#4caf50; color:white; border:none; border-radius:6px; cursor:pointer; font-weight:bold;'>
                🖨️ Print rapport
            </button>
          </div>";

} else {
    echo "<p style='text-align:center;'>Geen producten gevonden voor deze periode.</p>";
}
?>
</body>
</html>