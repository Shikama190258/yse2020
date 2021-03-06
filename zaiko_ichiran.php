<?php declare(strict_types=1);
/*
【機能】
書籍テーブルより書籍情報を取得し、画面に表示する。
商品をチェックし、ボタンを押すことで入荷、出荷が行える。
ログアウトボタン押下時に、セッション情報を削除しログイン画面に遷移する。

【エラー一覧（エラー表示：発生条件）】
入荷する商品が選択されていません：商品が一つも選択されていない状態で入荷ボタンを押す
出荷する商品が選択されていません：商品が一つも選択されていない状態で出荷ボタンを押す
*/

//(1)セッションを開始する
session_start();
session_regenerate_id(true);

//(2)SESSIONの「login」フラグがfalseか判定する。「login」フラグがfalseの場合はif文の中に入る。
if ($_SESSION['login'] == false) {      // (2)の処理を書く
    //(3)SESSIONの「error2」に「ログインしてください」と設定する。
    $_SESSION['error2'] = 'ログインしてください';

    //(4)ログイン画面へ遷移する。
    header('Location: login.php');
}

//(5)データベースへ接続し、接続情報を変数に保存する
//(6)データベースで使用する文字コードを「UTF8」にする

$db_name = 'zaiko2020_yse';
$host = 'localhost';
$user_name = 'zaiko2020_yse';
$password = '2020zaiko';
$dsn = "mysql:dbname={$db_name};host={$host};charset=utf8";

try {
    $pdo = new PDO($dsn, $user_name, $password);
} catch (PDOException $e) {
    exit;
}

//(7)書籍テーブルから書籍情報を取得するSQLを実行する。また実行結果を変数に保存する
$sql = 'SELECT * FROM books';
$query = $pdo->query($sql);


// ここから3つ目追加
// ここから3つ目追加
// ここから3つ目追加

// 上の(7)下2行をコメントアウトしてこの下の2行をコメントアウト解除すれば恐らく削除機能
// 事前にphpmyadminの構造から独自にyse2020へis_delカラム追加しておかないと機能しない

// $sql = "SELECT * FROM books WHERE is_del = 0";
// $query = $pdo->query($sql);

// ここまで3つ目追加
// ここまで3つ目追加
// ここまで3つ目追加


?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<title>書籍一覧</title>
	<link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>

<body>
	<div id="header">
		<h1>書籍一覧</h1>
	</div>
	<form action="zaiko_ichiran.php" method="post" id="myform" name="myform">
		<div id="pagebody">

			<!-- エラーメッセージ表示  -->
			<div id="error">
				<?php
                /*
                 * (8)SESSIONの「success」にメッセージが設定されているかを判定する。
                 * 設定されていた場合はif文の中に入る。
                 */
                if (!empty($_SESSION['success'])) {  // (8)の処理を書く
                    //(9)SESSIONの「success」の中身を表示する。
                    echo $_SESSION['success'];
                    $_SESSION['success'] = '';
                }

                ?>
			</div>

			<!-- 左メニュー -->
			<div id="left">
				<p id="ninsyou_ippan">
					<?php
                        echo @$_SESSION['account_name'];
                    ?><br>
					<button type="button" id="logout" onclick="location.href='logout.php'">ログアウト</button>
				</p>
				<button type="submit" id="btn1" formmethod="POST" name="decision" value="3"
					formaction="nyuka.php">入荷</button>

				<button type="submit" id="btn1" formmethod="POST" name="decision" value="4"
					formaction="syukka.php">出荷</button>

				<button type="submit" id="btn1" formmethod="POST" name="decision" value="5" formaction="new_product.php"
					style="width:90%;font-size:16px;">新商品追加</button>

				<button type="submit" id="btn1" formmethod="POST" name="decision" value="6"
					formaction="delete_product.php" style="width:90%;font-size:16px;">商品削除</button>
			</div>
			<!-- 中央表示 -->
			<div id="center">

				<!-- 書籍一覧の表示 -->
				<table>
					<thead>
						<tr>
							<th id="check"></th>
							<th id="id">ID</th>
							<th id="book_name">書籍名</th>
							<th id="author">著者名</th>
							<th id="salesDate">発売日</th>
							<th id="itemPrice">金額</th>
							<th id="stock">在庫数</th>
						</tr>
					</thead>
					<tbody>
						<?php
                        //(10)SQLの実行結果の変数から1レコードのデータを取り出す。レコードがない場合はループを終了する。
                        while ($extract = $query->fetch(PDO::FETCH_ASSOC)) { // ⑩の処理を書く
                            //(11)extract変数を使用し、1レコードのデータを渡す。
                            echo '<tr>' . PHP_EOL;
                            echo "<td><input type='checkbox' name='books[]'value='{$extract['id']}'></td>"; // ⑫IDを設定する
                            echo "<td>{$extract['id']}</td>" . PHP_EOL; // ⑬IDを表示する
                            echo "<td>{$extract['title']}</td>" . PHP_EOL; // ⑭titleを表示する
                            echo "<td>{$extract['author']}</td>" . PHP_EOL; // ⑮authorを表示する
                            echo "<td>{$extract['salesDate']}</td>" . PHP_EOL; // ⑯salesDateを表示する
                            echo "<td>{$extract['price']}</td>" . PHP_EOL; // ⑰priceを表示する
                            echo "<td>{$extract['stock']}</td>" . PHP_EOL; // ⑱stockを表示する
                            echo '</tr>' . PHP_EOL;
                        }
                        ?>
					</tbody>
				</table>
			</div>
		</div>
	</form>
	<div id="footer">
		<footer>株式会社アクロイト</footer>
	</div>
</body>

</html>