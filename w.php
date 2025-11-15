<?php
include 'hi.php';
include 'n.php';
$mysqli->select_db("producten");


// Verwijderactie verwerken
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['productid'];
    $sql = "DELETE FROM artikelen WHERE productid=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<p class='melding rood'>❌ Product verwijderd</p>";
}

// Zoekterm en filters ophalen
$zoekterm = isset($_GET['zoek']) ? trim($_GET['zoek']) : "";
$filter   = isset($_GET['filter']) ? $_GET['filter'] : "";
$catFilter = isset($_GET['category']) ? $_GET['category'] : "";

// Alle unieke categorieën ophalen
$catResult = $mysqli->query("SELECT DISTINCT category FROM artikelen ORDER BY category ASC");
$alleCategories = [];
while ($catRow = $catResult->fetch_assoc()) {
    $alleCategories[] = $catRow['category'];
}

// --- Query opbouwen ---
$sql = "SELECT * FROM artikelen WHERE 1=1";
$params = [];
$types  = "";

if ($catFilter !== "") {
    $sql .= " AND category = ?";
    $params[] = $catFilter;
    $types   .= "s";
}
if ($zoekterm !== "") {
    $sql .= " AND naam LIKE ?";
    $params[] = "%" . $zoekterm . "%";
    $types   .= "s";
}
if ($filter === "0-20") {
    $sql .= " AND nog_in_voorraad BETWEEN 0 AND 20";
} elseif ($filter === "21-50") {
    $sql .= " AND nog_in_voorraad BETWEEN 21 AND 50";
} elseif ($filter === "51+") {
    $sql .= " AND nog_in_voorraad >= 51";
}

$stmt = $mysqli->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>ProductenLijst</title>
    <style>
        body {
            background-color: #fff8dc;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0 30px 30px;
            padding-top: 100px;
        }
        h1 { color: darkred; text-align: center; margin-top: 20px; }
        .navbar {
            position: fixed; top: 0; left: 0; width: 100%;
            background-color: #f0c040; padding: 15px 0;
            z-index: 1000; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex; gap: 20px; justify-content: center;
        }
        .navbar a { text-decoration: none; font-weight: bold; color: #333; }
        .navbar a:hover { color: darkred; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; flex-wrap: wrap; gap: 10px; }
        form.zoekbalk { margin: 0; display: flex; gap: 10px; align-items: center; }
        input[type=text], select { padding: 8px; border-radius: 6px; border: 1px solid #ccc; }
        input[type=submit], button.filter {
            padding: 8px 16px; border-radius: 6px; border: none;
            background: #4caf50; color: white; font-weight: bold; cursor: pointer;
        }
        input[type=submit]:hover, button.filter:hover { opacity: 0.85; }
        table { width: 100%; border-collapse: collapse; margin-top: 25px; }
        th, td { border: 1px solid #ccc; padding: 12px; text-align: center; }
        th { background-color: #f0c040; color: black; }
        tr:nth-child(even) { background-color: #fdf5e6; }
        a.action-link, button {
            padding: 6px 12px; margin: 2px; border: none; border-radius: 6px;
            text-decoration: none; font-weight: bold; cursor: pointer;
        }
        a.action-link { background-color: #4caf50; color: white; }
        button { background-color: #d9534f; color: white; }
        button:hover, a.action-link:hover { opacity: 0.85; }
        .melding { text-align: center; font-size: 18px; margin: 20px 0; }
        .rood { color: red; }
        .product-foto { max-width: 100px; max-height: 100px; object-fit: cover; border-radius: 6px; }
    </style>
</head>
<body>

<!-- Navigatiebalk -->
<div class="navbar">
    <a href="nav.php">🏠 Home</a>
    <a href="w.php">📋 Productenlijst</a>
    <a href="index2.php">➕ Nieuw product toevoegen</a>
    <a href="check.php">🔄 Verkochte stuks updaten</a>
    <a href="dat.php">📊 Maandrapport producten</a>
</div>

<?php if ($catFilter === ""): ?>
    <h1>📦 Kies eerst een leverancier voor <br>de PRODUCTENLIJST</h1>
    <form method="get" action="w.php" style="text-align:center; margin-top:20px;">
        <select name="category">
            <option value="">-- Selecteer merk --</option>
            <?php foreach ($alleCategories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Toon producten</button>
    </form>
<?php else: ?>
    <h1>📋 Productenlijst voor levrancier: <?= htmlspecialchars($catFilter) ?></h1>

    <!-- Zoekbalk + filters -->
    <div class="topbar">
        <form method="get" class="zoekbalk">
            <input type="hidden" name="category" value="<?= htmlspecialchars($catFilter) ?>">
            <input type="text" name="zoek" placeholder="🔍 Zoek product..." value="<?= htmlspecialchars($zoekterm) ?>">
            <input type="submit" value="Zoeken">
        </form>

        <form method="get">
            <input type="hidden" name="category" value="<?= htmlspecialchars($catFilter) ?>">
            <span style="font-weight:bold;">Filter op voorraad:</span>
            <button type="submit" class="filter" name="filter" value="0-20">0-20</button>
            <button type="submit" class="filter" name="filter" value="21-50">21-50</button>
            <button type="submit" class="filter" name="filter" value="51+">51+</button>
        </form>
    </div>

    <?php
    if ($result && $result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Naam</th><th>Leverancier</th><th>Datum</th><th>Prijs</th><th>Voorraad</th><th>Foto</th><th>Acties</th></tr>";
        while ($row = $result->fetch_assoc()) {
            $datum = !empty($row['datum']) ? date("d-m-Y", strtotime($row['datum'])) : "-";
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['naam']) . "</td>";
            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
            echo "<td>" . $datum . "</td>";
            echo "<td>€" . $row['prijs'] . "</td>";
            echo "<td>" . $row['nog_in_voorraad'] . "</td>";
            if (!empty($row['foto']) && file_exists("uploads/" . $row['foto'])) {
                echo "<td><img src='uploads/" . $row['foto'] . "' alt='Foto' class='product-foto'></td>";
            } else {
                echo "<td>-</td>";
            }
                    echo "<td>
                    <a class='action-link' href='wi.php?id=" . $row['productid'] . "'>Wijzig</a>
                    <form method='post' style='display:inline'>
                        <input type='hidden' name='productid' value='" . $row['productid'] . "'>
                        <button type='submit' name='action' value='delete' onclick=\"return confirm('Wilt u zeker verwijderen?');\">Verwijder</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='melding'>Geen producten gevonden.</p>";
    }
    ?>
<?php endif; ?>

</body>
</html>
