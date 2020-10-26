<?php declare(strict_types=1);
/*
【機能】
入荷で入力された個数を表示する。入荷を実行した場合は対象の書籍の在庫数に入荷数を加
えた数でデータベースの書籍の在庫数を更新する。

【エラー一覧（エラー表示：発生条件）】
なし
*/

//(1)セッションを開始する
session_start();

function getByid($id, $con)
{
    /*
     * (2)書籍を取得するSQLを作成する実行する。
     * その際にWHERE句でメソッドの引数の$idに一致する書籍のみ取得する。
     * SQLの実行結果を変数に保存する。
     */
    //(3)実行した結果から1レコード取得し、returnで値を返す。
    $sql = "SELECT * FROM books WHERE id = {$id}";
    $query = $con->query($sql);
    return $query->fetch(PDO::FETCH_ASSOC);
}

function updateByid($id, $con, $total): void
{
    /*
     * (4)書籍情報の在庫数を更新するSQLを実行する。
     * 引数で受け取った$totalの値で在庫数を上書く。
     * その際にWHERE句でメソッドの引数に$idに一致する書籍のみ取得する。
     */
    $sql = "UPDATE books SET stock = {$total} WHERE id = {$id}";
    $con->query($sql);
}

//(5)SESSIONの「login」フラグがfalseか判定する。「login」フラグがfalseの場合はif文の中に入る。
if ($_SESSION['login'] == false) {
    //(6)SESSIONの「error2」に「ログインしてください」と設定する。
    $_SESSION['error2'] = 'ログインしてください';
    //(7)ログイン画面へ遷移する。
    header('Location: login.php');
}

//(8)データベースへ接続し、接続情報を変数に保存する
//(9)データベースで使用する文字コードを「UTF8」にする
$db_name = 'zaiko2020_yse';
$host = 'localhost';
$user_name = 'zaiko2020_yse';
$password = '2020zaiko';
$dsn = "mysql:dbname={$db_name};host={$host}; charset=utf8";

try {
    $pdo = new PDO($dsn, $user_name, $password);
} catch (PDOException $e) {
    exit;
}

//(10)書籍数をカウントするための変数を宣言し、値を0で初期化する
$count_books = 0;

//(11)POSTの「books」から値を取得し、変数に設定する。
foreach ($_POST['books'] as $book_id) {
    /*
     * (12)POSTの「stock」について(10)の変数の値を使用して値を取り出す。
     * 半角数字以外の文字が設定されていないかを「is_numeric」関数を使用して確認する。
     * 半角数字以外の文字が入っていた場合はif文の中に入る。
     */
    $stock = $_POST['stock'][$count_books];

    if (!is_numeric($stock)) {
        //(13)SESSIONの「error」に「数値以外が入力されています」と設定する。
        $_SESSION['error'] = '数値以外が入力されています';
        //(14)「include」を使用して「nyuka.php」を呼び出す。
        include 'nyuka.php';
        //(15)「exit」関数で処理を終了する。
        exit;
    }

    //(16)「getByid」関数を呼び出し、変数に戻り値を入れる。その際引数に(11)の処理で取得した値と(8)のDBの接続情報を渡す。
    $book = getByid($book_id, $pdo);
    //(17) (16)で取得した書籍の情報の「stock」と、(10)の変数を元にPOSTの「stock」から値を取り出し、足した値を変数に保存する。
    $sum_stock = $book['stock'] + $stock;
    //(18) (17)の値が100を超えているか判定する。超えていた場合はif文の中に入る。
    if ($sum_stock > 100) {
        //(19)SESSIONの「error」に「最大在庫数を超える数は入力できません」と設定する。
        $_SESSION['error'] = '最大在庫数を超える数は入力できません';
        //(20)「include」を使用して「nyuka.php」を呼び出す。
        include 'nyuka.php';
        //(21)「exit」関数で処理を終了する。
        exit;
    }

    //(22) (10)で宣言した変数をインクリメントで値を1増やす。
    $count_books++;
}

/*
 * (23)POSTでこの画面のボタンの「add」に値が入ってるか確認する。
 * 値が入っている場合は中身に「ok」が設定されていることを確認する。
 */
if (isset($_POST['add']) && $_POST['add'] = 'ok') {
    //(24)書籍数をカウントするための変数を宣言し、値を0で初期化する。
    $count_books = 0;

    //(25)POSTの「books」から値を取得し、変数に設定する。
    foreach ($_POST['books'] as $book_id) {
        //(26)「getByid」関数を呼び出し、変数に戻り値を入れる。その際引数に(25)の処理で取得した値と(8)のDBの接続情報を渡す。
        $book = getByid($book, $pdo);
        //(27) (26)で取得した書籍の情報の「stock」と、(24)の変数を元にPOSTの「stock」から値を取り出し、足した値を変数に保存する。
        $sum_stock = $book['stock'] + $_POST['stock'];
        //(28)「updateByid」関数を呼び出す。その際に引数に(25)の処理で取得した値と(8)のDBの接続情報と(27)で計算した値を渡す。
        updateByid($book_id, $pdo, $sum_stock);
        //(29) (24)で宣言した変数をインクリメントで値を1増やす。
        $count_books++;
    }

    //(30)SESSIONの「success」に「入荷が完了しました」と設定する。
    $_SESSION['success'] = '入荷が完了しました';
    //(31)「header」関数を使用して在庫一覧画面へ遷移する。
    header('Location: zaiko_ichiran.php');
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<title>入荷確認</title>
	<link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>

<body>
	<div id="header">
		<h1>入荷確認</h1>
	</div>
	<form action="nyuka_kakunin.php" method="post" id="test">
		<div id="pagebody">
			<div id="center">
				<table>
					<thead>
						<tr>
							<th id="book_name">書籍名</th>
							<th id="stock">在庫数</th>
							<th id="stock">入荷数</th>
						</tr>
					</thead>
					<tbody>
						<?php
                        //(32)書籍数をカウントするための変数を宣言し、値を0で初期化する。
                        $count_books = 0;
                        //(33)POSTの「books」から値を取得し、変数に設定する。
                        foreach ($_POST['books'] as $book_id) {
                            //(34)「getByid」関数を呼び出し、変数に戻り値を入れる。その際引数に(32)の処理で取得した値と(8)のDBの接続情報を渡す。
                            $book = getId($book_id, $pdo);
                            $stock = $_POST['stock'][$count_books]; ?>
						<tr>
							<td><?php $book['title']/* (35) (34)で取得した書籍情報からtitleを表示する。 */; ?>
							</td>
							<td><?php $book['stock']/* (36) (34)で取得した書籍情報からstockを表示する。 */; ?>
							</td>
							<td><?php $stock /* (36) POSTの「stock」に設定されている値を(32)の変数を使用して呼び出す。 */; ?>
							</td>
						</tr>
						<input type="hidden" name="books[]"
							value="<?php $book['id'] /* (37) (33)で取得した値を設定する */; ?>">
						<input type="hidden" name="stock[]"
							value='<?php $stock /* (38)POSTの「stock」に設定されている値を(32)の変数を使用して設定する。 */; ?>'>
						<?php
                            //(39) (32)で宣言した変数をインクリメントで値を1増やす。
                            $count_books++;
                        }
                        ?>
					</tbody>
				</table>
				<div id="kakunin">
					<p>
						上記の書籍を入荷します。<br>
						よろしいですか？
					</p>
					<button type="submit" id="message" formmethod="POST" name="add" value="ok">はい</button>
					<button type="submit" id="message" formaction="nyuka.php">いいえ</button>
				</div>
			</div>
		</div>
	</form>
	<div id="footer">
		<footer>株式会社アクロイト</footer>
	</div>
</body>

</html>