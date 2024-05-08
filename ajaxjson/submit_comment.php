<?php

$servername = "localhost"; // データベースのホスト名
$username = "root"; // データベースのユーザー名
$password = ""; // データベースのパスワード
$dbname = "favorite"; // データベース名

$errors = []; // エラーメッセージを格納する配列を初期化

$name = isset($_POST["name"]) ? $_POST["name"] : ""; // フォームからの送信がない場合は空文字を設定
$gender = isset($_POST["gender"]) ? $_POST["gender"] : ""; // フォームからの送信がない場合は空文字を設定
$comment = isset($_POST["comment"]) ? $_POST["comment"] : ""; // フォームからの送信がない場合は空文字を設定

// POST メソッドでフォームが送信された場合の処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 名前の検証
    if (empty($name)) {
        $errors[] = "名前を入力してください。";
    }

    // 性別の検証
    if (empty($gender)) {
        $errors[] = "性別を選択してください。";
    }

    // コメントの検証
    if (empty($comment)) {
        $errors[] = "コメントを入力してください。";
    }

    // エラーがない場合のみデータベースに挿入する
    if (empty($errors)) {
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // データを挿入するSQLクエリを作成する
            $sql = "INSERT INTO contents (name, gender, message) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $gender, $comment]);

            echo "新しいレコードが正常に挿入されました";
        } catch (PDOException $e) {
            echo "エラー: " . $e->getMessage();
        }

        // データベース接続を閉じる
        $conn = null;
    }
}
