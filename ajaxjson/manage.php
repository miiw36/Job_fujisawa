<?php
// データベース接続情報
$servername = "localhost";
$dbname = "favorit";
$username = "root";
$password = "";

try {
    // PDO インスタンスを作成し、データベースに接続する
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // エラー時に例外を投げる
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 表示/非表示の状態を更新する処理
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['visibleRecords']) && !empty($_POST['visibleRecords'])) {
            foreach ($_POST['visibleRecords'] as $id) {
                $visible = isset($_POST['visible_' . $id]) ? 1 : 0;
                // データベースのvisibleフィールドを更新するクエリを作成
                $sql = "UPDATE contents SET visible = :visible WHERE id = :id";
                // プリペアドステートメントを準備
                $stmt = $conn->prepare($sql);
                // パラメータに値をバインド
                $stmt->bindParam(':visible', $visible, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                // クエリを実行
                $stmt->execute();
            }
            // 成功メッセージを返す（オプション）
            echo "Visibility updated successfully";
        }
    }

    // 表示するデータを取得するクエリを作成
    $sql = "SELECT * FROM contents";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $contents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ページ</title>

    <style>
        table {
            border-collapse: collapse;
            width: 70%;
            margin: 0 auto;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .hidden {
            color: transparent;
            /* 文字を透明にする */
        }

        .button-container {
            width: 120px;
            /* ボタンの幅を固定 */
        }

        /* ボタンのスタイル */
        .delete-button {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .delete-button:hover {
            background-color: #d32f2f;
            /* ホバー時の背景色 */
        }

        /* スイッチのスタイル */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 34px;
            /* 角丸 */
        }

        /* スライダーのボタンのスタイル */
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 50%;
            /* 角丸 */
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
            /* 3D効果 */
        }

        /* スイッチがONの場合のスタイル */
        input:checked+.slider {
            background-color: #2196F3;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        /* スイッチがONの場合のボタンのスタイル */
        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* テーブル全体のスタイル */
        #contentTable {
            width: 70%;
            border-collapse: collapse;
        }

        /* テーブルのヘッダー行のスタイル */
        #contentTable th {
            background-color: #F5DED9;
            /* 背景色 */
            padding: 10px;
            border: 1px solid #000;
            /* 境界線 */
            text-align: center;
            /* 中央揃え */
        }

        /* テーブルのデータ行のスタイル */
        #contentTable td {
            padding: 10px;
            border: 1px solid #000;
            /* 境界線 */
            text-align: center;
            /* 中央揃え */
        }
    </style>
</head>

<body>
    <h2>コンテンツ一覧</h2>
    <form id="displayForm" action="manage.php" method="post">
        <table id="contentTable">
            <tr>
                <th>Switch</th>
                <th>ID</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Message</th>
                <th>Created At</th>

            </tr>

            <?php foreach ($contents as $content) : ?>
                <tr>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="visible_<?= $content['id'] ?>" value="<?= $content['id'] ?>" <?= $content['visible'] == 1 ? 'checked' : '' ?>>
                            <span class="slider round"></span>
                        </label>
                        <input type="hidden" name="visibleRecords[]" value="<?= $content['id'] ?>">
                    </td>
                    <td><?= $content['id'] ?></td>
                    <td><?= $content['name'] ?></td>
                    <td><?= $content['gender'] ?></td>
                    <td><?= $content['message'] ?></td>
                    <td><?= $content['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>

        </table>
        <br>
    </form>



    <script>
        document.querySelectorAll('.switch input[type="checkbox"]').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });
    </script>


</body>

</html>