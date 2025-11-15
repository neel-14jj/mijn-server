<style>
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background-color: #f0c040;
        padding: 15px 0;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        z-index: 1000;
    }

    .navbar a {
        color: black;
        text-decoration: none;
        margin: 0 20px;
        font-weight: bold;
        font-size: 16px;
        padding: 8px 16px;
        border-radius: 6px;
        transition: background-color 0.3s;
    }

    .navbar a:hover {
        background-color: #ffa500;
    }

    body {
        padding-top: 70px; /* hoogte van navbar + marge */
    }
</style>

<div class="navbar">
    <a href="nav.php">🏠 Home</a>
    <a href="w.php">📋 Productenlijst</a>
    <a href="index2.php">➕ Nieuw product toevoegen</a>
    <a href="check.php">🔄 Verkochte stuks updaten</a>
    <a href="dat.php">📊 Maandrapport producten</a>
</div>