<?php
include 'hi.php';
include 'n.php';
$mysqli->select_db("producten");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_all'])) {
    // Zet alle gekochte_stuks en nog_in_voorraad terug naar 0
    $sql = "UPDATE artikelen SET gekochte_stuks = 0, nog_in_voorraad = 0";
    if ($mysqli->query($sql)) {
        echo "<p style='color:green; text-align:center;'>✅ Alle producten zijn gereset: voorraad en verkochte stuks = 0</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>❌ Fout bij resetten: " . $mysqli->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Reset producten</title>
    <style>
        body { font-family: Arial; background:#fdf5e6; padding:40px; }
        form { max-width:400px; margin:0 auto; background:#fff8dc; padding:20px; border-radius:8px; box-shadow:0 0 10px #ccc; text-align:center; }
        input[type=submit] {
            background:#d9534f; color:white; font-weight:bold;
            padding:10px 20px; border:none; border-radius:6px; cursor:pointer;
        }
        input[type=submit]:hover { background:#c9302c; }
    </style>
</head>
<body>
    <h1 style="text-align:center; color:darkred;">🔄 Reset alle producten</h1>

    <!-- Formulier met bevestiging -->
    <form method="post" onsubmit="return confirm('Weet je zeker dat je ALLE producten wilt resetten naar 0?')">
        <input type="submit" name="reset_all" value="Reset alles naar 0">
    </form>
</body>
</html>