<?php
// Adatbázis kapcsolódási adatok
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";  // Vagy a megfelelő szervernév
$username = "root";         // MySQL felhasználó
$password = "";             // MySQL jelszó
$dbname = "auto_dekor";     // Az adatbázis neve

// Adatbázis kapcsolat létrehozása
$conn = new mysqli($servername, $username, $password, $dbname);

// Kapcsolat ellenőrzése
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

// Adatok fogadása az űrlapból
$tipus = $conn->real_escape_string($_POST['tipus']);
$km_allas = $conn->real_escape_string($_POST['km']);
$le = $conn->real_escape_string($_POST['le']);
$ar = $conn->real_escape_string($_POST['ar']);
$uzemanyag_tipus = $conn->real_escape_string($_POST['fueltype']);
$valto_tipus = $conn->real_escape_string($_POST['gearbox']);

// Kép feltöltés kezelése
$imagePath = null;
if (!empty($_FILES['images']['name'][0])) {
    $targetDir = "uploads/";
    $imageName = basename($_FILES['images']['name'][0]);
    $targetFilePath = $targetDir . $imageName;

    // Kép formátum ellenőrzése
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = array('jpg', 'jpeg', 'png', 'webp');
    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES['images']['tmp_name'][0], $targetFilePath)) {
            $imagePath = $targetFilePath;
        } else {
            echo "Hiba történt a kép feltöltése közben.";
        }
    } else {
        echo "Csak JPG, JPEG, PNG, és WEBP formátumok engedélyezettek.";
    }
}

// Adatok beszúrása az adatbázisba, SQL injection megelőzés
$stmt = $conn->prepare("INSERT INTO autok (tipus, km_allas, le, ar, uzemanyag_tipus, valto_tipus, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("siissss", $tipus, $km_allas, $le, $ar, $uzemanyag_tipus, $valto_tipus, $imagePath);

if ($stmt->execute()) {
    echo "Új rekord sikeresen hozzáadva!";
} else {
    echo "Hiba: " . $stmt->error;
}

$stmt->close();

// Autók lekérdezése
$sql = "SELECT * FROM autok";
$result = $conn->query($sql);

// Eredmények megjelenítése
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h2>" . htmlspecialchars($row['tipus']) . "</h2>";
        echo "<p>Km állás: " . htmlspecialchars($row['km_allas']) . "</p>";
        echo "<p>LE: " . htmlspecialchars($row['le']) . "</p>";
        echo "<p>Ár: " . htmlspecialchars($row['ar']) . "</p>";
        echo "<p>Üzemanyag típus: " . htmlspecialchars($row['uzemanyag_tipus']) . "</p>";
        echo "<p>Váltó típus: " . htmlspecialchars($row['valto_tipus']) . "</p>";
        if (!empty($row['image_path'])) {
            echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='Autó képe' style='max-width: 100%; height: auto;'>";
        }
        echo "</div>";
    }
} else {
    echo "Nincs még autó feltöltve.";
}

// Kapcsolat bezárása
$conn->close();
?>
