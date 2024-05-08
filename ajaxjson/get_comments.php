<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "favorite";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT name, gender, message FROM contents WHERE visible = 1 ORDER BY id DESC LIMIT 10"; // visibleが1のコメントのみを取得し、最新の10件を表示
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($comments as $comment) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($comment['name']) . "</td>";
        echo "<td>" . htmlspecialchars($comment['gender']) . "</td>";
        echo "<td>" . htmlspecialchars($comment['message']) . "</td>";
        echo "</tr>";
    }
} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
}
