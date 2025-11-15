<?php
include 'hi.php';
include 'n.php';
$mysqli->select_db("producten");

// Als er een update is verstuurd
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['productid'])) {
    $id = (int)$_POST['productid'];
    $verkocht = (int)$_POST['verkocht'];

    $sql = "UPDATE artikelen 
            SET gekochte_stuks = gekochte_stuks + ?, 
                nog_in_voorraad = nog_in_voorraad - ? 
            WHERE productid = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("iii", $verkocht, $verkocht, $id);
    $stmt->execute();
}

// Zoekterm en filter ophalen
$zoekterm = isset($_GET['zoek']) ? trim($_GET['zoek']) : "";
$filter   = isset($_GET['filter']) ? $_GET['filter'] : "";

// Query opbouwen
if ($zoekterm !== "" || $filter !== "") {
    $sql = "SELECT * FROM artikelen WHERE 1=1";
    $params = [];
    $types  = "";

    if ($zoekterm !== "") {
        $sql .= " AND naam LIKE ?";
        $params[] = "%" . $zoekterm . "%";
        $types   .= "s";
    }

    // Filter op verkochte stuks
    if ($filter === "0-20") {
        $sql .= " AND gekochte_stuks BETWEEN 0 AND 20";
    } elseif ($filter === "21-50") {
        $sql .= " AND gekochte_stuks BETWEEN 21 AND 50";
    } elseif ($filter === "51+") {
        $sql .= " AND gekochte_stuks >= 51";
    }

    $stmt = $mysqli->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM artikelen";
    $result = $mysqli->query($sql);
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Verkochte_stuks bijwerken</title>
    <style>
        body {
            background-color: #fff8dc;
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        h1 {
            color: darkred;
            text-align: center;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        form.zoekbalk {
            margin: 0;
        }
        input[type=text] {
            padding: 8px;
            width: 250px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        input[type=submit], button.filter {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin-left: 5px;
        }
        input[type=submit]:hover, button.filter:hover { background-color: #45a049; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f0c040;
            color: black;
        }
        tr:nth-child(even) { background-color: #fdf5e6; }
        input[type="number"] {
            width: 60px;
            padding: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .melding {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .groen { color: green; }
    </style>
</head>
<body>
<br><br>
<h1>🔄 Verkochte stuks bijwerken</h1>
<!-- Zoekbalk links + filter rechts -->
<div class="topbar">
    <!-- Zoekbalk -->
    <form method="get" class="zoekbalk">
        <input type="text" name="zoek" placeholder="🔍 Zoek product..." value="<?php echo htmlspecialchars($zoekterm); ?>">
        <input type="submit" value="Zoeken">
    </form>

    <!-- Filter rechts -->
    <form method="get" style="margin:0;">
        <span style="font-weight:bold; margin-right:10px;">Filter op verkochte stuks:</span>
        <button type="submit" class="filter" name="filter" value="0-20">0-20</button>
        <button type="submit" class="filter" name="filter" value="21-50">21-50</button>
        <button type="submit" class="filter" name="filter" value="51+">51+</button>
    </form>
</div>

<?php
if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr>
            <th>Naam</th>
            <th>Leverancier</th>
            <th>Voorraad</th>
            <th>Verkocht</th>
            <th>Prijs</th>
            <th>Omzet</th>
            <th>Update verkochte stuks</th>
          </tr>";
    while ($row = $result->fetch_assoc()) {
        $omzet = $row['gekochte_stuks'] * $row['prijs'];


        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['naam']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . $row['nog_in_voorraad']  . "</td>";
        echo "<td>" . $row['gekochte_stuks'] . "</td>";
        echo "<td>€" . number_format($row['prijs'], 2, ',', '.') . "</td>";
        echo "<td>€" . number_format($omzet, 2, ',', '.') . "</td>";
        echo "<td>
                <form method='post' style='margin:0'>
                    <input type='hidden' name='productid' value='" . $row['productid'] . "'>
                    <input type='number' name='verkocht' min='1' required>
                    <input type='submit' value='Update'>
                </form>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='melding'>Geen producten gevonden.</p>";
}
?>

</body>
</html>