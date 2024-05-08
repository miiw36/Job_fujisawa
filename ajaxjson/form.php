<?php

$servername = "localhost"; // データベースのホスト名
$username = "root"; // データベースのユーザー名
$password = ""; // データベースのパスワード
$dbname = "favorite"; // データベース名

$gender = ""; // 初期値を空に設定する

$errors = []; // エラーメッセージを格納する配列を初期化

// POST メソッドでフォームが送信された場合の処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 変数の初期化
    $name = $_POST["name"] ?? "";
    $gender = isset($_POST["gender"]) ? $_POST["gender"] : "";
    $comment = $_POST["comment"] ?? "";

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

// コメントの一覧を取得する
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT name, gender, message FROM contents WHERE visible = 1"; // visibleが1のコメントのみを取得
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
}

?>

<?php
// 変数の初期化
$name = isset($name) ? $name : "";
$comment = isset($comment) ? $comment : "";
$gender = isset($gender) ? $gender : "";
?>

<!-- エラーメッセージの表示 -->
<?php if (!empty($errors)) : ?>
    <ul>
        <?php foreach ($errors as $error) : ?>
            <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <title>お問い合わせ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('./image/back1.jpg');
            /* 背景画像のパスを指定 */
            background-size: cover;
            /* 背景画像をウィンドウに合わせて表示 */
            background-repeat: no-repeat;
            /* 背景画像を繰り返し表示しない */
            background-attachment: fixed;
            /* 背景画像を固定 */
            background-color: #f4f4f4;
            /* 背景色 */
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #3A8699;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        textarea {
            width: 70%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #3A8699;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* テーブル全体のスタイル */
        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            /* 角を丸くする */
            overflow: hidden;
            /* 角を丸くした部分を隠すために必要 */

        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        /* テーブルのヘッダー行のスタイル */
        th {

            padding: 15px;
            /* パディング */
            border: 1px solid #000;
            /* 境界線 */
            text-align: center;
            /* 中央揃え */
            font-size: 16px;
            /* フォントサイズ */
        }

        /* テーブルのデータ行のスタイル */
        td {
            padding: 10px;
            /* パディング */
            border: 1px solid #000;
            /* 境界線 */
            text-align: center;
            /* 中央揃え */
            font-size: 14px;
            /* フォントサイズ */
        }

        /* 偶数行の背景色 */
        tr:nth-child(even) {
            background-color: #F5D39A;
        }

        /* 奇数行の背景色 */
        tr:nth-child(odd) {
            background-color: #F9EAC0;
            /* 例として白色を設定 */
        }

        /* マウスオーバー時の背景色 */
        tr:hover {
            background-color: #f2f2f2;
        }

        .table-container {
            text-align: center;
            /* テーブルを中央揃え */
        }

        table {
            margin: 0 auto;
            /* テーブルを中央に配置 */
            width: 70%;
            /* 必要に応じて幅を設定 */
        }

        /* キャプションのスタイル */
        .toggle-title {
            font-size: 24px;
            /* フォントサイズ */
            color: #CC9852;
            /* テキスト色 */
            padding: 10px;
            /* パディング */
            margin-bottom: 10px;
            /* 下の余白 */
            text-align: center;
            /* 中央揃え */
            font-weight: bold;
            /* 太字 */
            text-shadow: -1px -1px 0 #000,
                1px -1px 0 #000,
                -1px 1px 0 #000,
                1px 1px 0 #000;
            /* 黒い縁取り */
        }

        .toggle-button {
            padding: 10px 20px;
            background-color: #FFD700;
            /* Yellow color */
            color: #000;
            /* Black text */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .toggle-button:hover {
            background-color: #FFA500;
            /* Darker yellow on hover */
        }

        .table-container {
            margin-bottom: 30px;
        }
    </style>

<body>

    <h2>お問い合わせ</h2>
    <form id="commentForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <strong>名前：</strong><br>
        <input type="text" name="name" size="30" value="<?php echo htmlspecialchars($name); ?>" style="width: 500px;"><br><br>
        <label style="font-size: 18px;"><strong>性別：</strong></label>
        <label style="font-size: 18px; display: inline-block; margin-right: 20px;">
            <input type="radio" name="gender" value="female" <?php if ($gender === "female") echo "checked"; ?>>
            女性
        </label>
        <label style="font-size: 18px; display: inline-block; margin-right: 20px;">
            <input type="radio" name="gender" value="male" <?php if ($gender === "male") echo "checked"; ?>>
            男性
        </label>
        <label style="font-size: 18px; display: inline-block; margin-bottom: 20px;">
            <input type="radio" name="gender" value="other" <?php if ($gender === "other") echo "checked"; ?>>
            その他
        </label><br>

        <strong>コメント：</strong><br>
        <textarea name="comment" cols="30" rows="5"><?php echo htmlspecialchars($comment); ?></textarea><br><br>
        <input type="submit" value="投稿">
    </form>

    <div class="table-container">
        <h3 class="toggle-title">コメントセクション</h3>
        <button id="toggleButton" class="toggle-button">表示切替</button>
    </div>

    <div id="commentTable" class="table-container" style="display: none;">
        <table>
            <thead>
                <tr>
                    <th style="background-color: #AD6847;">名前</th>
                    <th style="background-color: #AD6847;">性別</th>
                    <th style="background-color: #AD6847;">コメント</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($comment['name']); ?></td>
                        <td><?php echo htmlspecialchars($comment['gender']); ?></td>
                        <td><?php echo htmlspecialchars($comment['message']); ?></td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>


    <script src="jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // フォームの送信をAJAXで処理
            $('#commentForm').submit(function(e) {
                e.preventDefault(); // デフォルトのフォーム送信を防止
                $.ajax({
                    type: 'POST',
                    url: 'submit_comment.php',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#commentForm')[0].reset();
                        loadComments();
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });

            // テーブルの表示切替
            $('#toggleButton').click(function() {
                $('#commentTable').toggle();
            });

            // コメントを読み込むための関数
            function loadComments() {
                $.ajax({
                    type: 'GET',
                    url: 'get_comments.php',
                    success: function(response) {
                        $('#commentTable tbody').empty().append(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }

            // 最初にコメントを読み込む
            loadComments();
        });
    </script>

</body>

</html>