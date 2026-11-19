<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lekker Eten - Categorieën</title>
    <link href="../style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cloudflare.com">
    <link rel="preconnect" href="https://googleapis.com">
    <link rel="preconnect" href="https://gstatic.com" crossorigin>
    <link href="https://googleapis.com/css2?family=Livvic:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://googleapis.com/css2?family=Cherish&display=swap" rel="stylesheet">
    <script defer src="../script.js"></script>
</head>
<body>
   <div class="hero-section">
        <div class="hero-text">
            lekker eten
        </div>
   </div>

    <div class="header" id="myHeader">
        <nav class="navbar">
            <input type="checkbox" id="menu-toggle">
            <label for="menu-toggle" class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </label>

            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="../gerechten/index.html">Gerechten</a></li>
                <li><a href="#">Toevoegen +</a></li>
            </ul>
        </nav>
    </div>

    <div id="content">
        <div id="filler"></div>
        <h1>Categorieen</h1>
        <div id="top-content">
            <form method="GET" action="index.php" style="display:flex; flex:1;">
                <input type="text" name="search" placeholder="Search.." value="<?= htmlspecialchars($search) ?>">
            </form>
            <a href="">Toevoegen +</a>
        </div>
        
        <div id="main-content">
            <?php if ($aantalRijen > 0) { ?>
                <?php foreach ($resultaten as $rij) { ?>
                    <a class="category-container" href="../gerechten/index.html?category_id=<?= $rij['id'] ?>">
                        <div class="category-card">
                            <h2><?= htmlspecialchars($rij['name']) ?></h2>
                            <p><?= htmlspecialchars($rij['description']) ?></p>
                        </div>
                    </a>
                <?php } ?>
            <?php } else { ?>
                <p>Geen categorieën gevonden.</p>
            <?php } ?>
        </div>
    </div>

    <footer>
        <p>© 2026 Drie Musketiers. All rights reserved.</p>
    </footer>
</body>
</html>
