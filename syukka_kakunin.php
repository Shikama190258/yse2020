<?php
/* 
【機能】
出荷で入力された個数を表示する。出荷を実行した場合は対象の書籍の在庫数から出荷数を
引いた数でデータベースの書籍の在庫数を更新する。

【エラー一覧（エラー表示：発生条件）】
なし
*/

//(1)セッションを開始する
session_start();
function getByid($id,$con){
	/* 
	 * (2)書籍を取得するSQLを作成する実行する。
	 * その際にWHERE句でメソッドの引数の$idに一致する書籍のみ取得する。
	 * SQLの実行結果を変数に保存する。
	 */

	//(3)実行した結果から1レコード取得し、returnで値を返す。
	$sql = "SELECT * FROM books WHERE id = {$id}";
	$query = $con->query($sql);
	//print_r($query->fetch(PDO::FETCH_ASSOC));
	return $query->fetch(PDO::FETCH_ASSOC);
	
	// if ($query->num_rows > 0) {
	// 	while($row = $query->fetch_assoc()) {
	// 		return $row;
	// 	}
	// }	
 }


function updateByid($id,$con,$total){
	/*
	 * (4)書籍情報の在庫数を更新するSQLを実行する。
	 * 引数で受け取った$totalの値で在庫数を上書く。
	 * その際にWHERE句でメソッドの引数に$idに一致する書籍のみ取得する。
	 */
	$sql = "UPDATE books SET stock = {$total} WHERE id = {$id}";
	$con->query($sql);
	

	// アロー演算子　かえす必要がない？
}


//(5)SESSIONの「login」フラグがfalseか判定する。「login」フラグがfalseの場合はif文の中に入る。
//if (/* ⑤の処理を書く */){
if ($_SESSION['login'] == False){
	//(6)SESSIONの「error2」に「ログインしてください」と設定する。
	$_SESSION['error2'] = "ログインしてください";
	//(7)ログイン画面へ遷移する。
	// header('Location: login.php');
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
// $count = 0;
//(11)POSTの「books」から値を取得し、変数に設定する。
//foreach(/* ⑪の処理を書く */){
foreach($_POST['books'] as $book_id){
	/*
	 * (12)POSTの「stock」について⑩の変数の値を使用して値を取り出す。
	 * 半角数字以外の文字が設定されていないかを「is_numeric」関数を使用して確認する。
	 * 半角数字以外の文字が入っていた場合はif文の中に入る。
	 */
	$stock = $_POST['stock'][$count_books];//$_POST[][]
	// $stock = $_POST["stock"][$count];
	//if (/* ⑫の処理を書く */) {
	if (!is_numeric($stock)) {
		//(13)SESSIONの「error」に「数値以外が入力されています」と設定する。
		//(14)「include」を使用して「syukka.php」を呼び出す。
		//(15)「exit」関数で処理を終了する。
		$_SESSION['error'] = "数値以外が入力されています";
		include 'syukka.php';
		
		exit;
	}

	//(16)「getByid」関数を呼び出し、変数に戻り値を入れる。その際引数に(11)の処理で取得した値と⑧のDBの接続情報を渡す。
	// $book = getId($book_id, $pdo);
	$book = getByid($book_id, $pdo);
	// $stock = $_POST['stock'][$count_books];
	//(17) (16)で取得した書籍の情報の「stock」と、⑩の変数を元にPOSTの「stock」から値を取り出して書籍情報の「stock」から値を引いた値を変数に保存する。
	$sum_stock = $book['stock'] - $stock;
	// $sum_stock=$book['stock']-$_POST['stock'][$count_books];
	//$sum_stock = getByid($book_id, $pdo)['stock'] - $stock;
	
	//(18) (17)の値が0未満か判定する。0未満の場合はif文の中に入る。
	//if(/* (18)の処理を行う */){
	if($sum_stock < 0){
		//(19)SESSIONの「error」に「出荷する個数が在庫数を超えています」と設定する。
		//(20)「include」を使用して「syukka.php」を呼び出す。
		//㉑「exit」関数で処理を終了する。

		//echo $sum_stock;
		// echo '$book[stock]ha';
		// echo $book['stock'];
		// echo '$bookvarha';
		// var_dump($book);
		// echo '$bookha';
		// echo $book;
		//echo '$getidha';
		//echo getByid($book_id, $pdo)['stock'];
		// echo '$getidvarha';
		// var_dump(getByid($book_id, $pdo));
		echo '$stockha';
		echo $stock;
		echo '$stockvarha';
		var_dump($stock);




		$_SESSION['error'] = '出荷する個数が在庫数を超えています';

		include 'syukka.php';
		exit;
	}
	
	//㉒ ⑩で宣言した変数をインクリメントで値を1増やす。
	$count_books++;
	// $count++;
}


/*
 * ㉓POSTでこの画面のボタンの「add」に値が入ってるか確認する。
 * 値が入っている場合は中身に「ok」が設定されていることを確認する。
 *
 */
//if(/* ㉓の処理を書く */){
if(isset($_POST['add']) && $_POST['add'] == 'ok'){
	//㉔書籍数をカウントするための変数を宣言し、値を0で初期化する。
	$count_books = 0;
	// $count = 0;
	//㉕POSTの「books」から値を取得し、変数に設定する。
	//foreach(/* ㉕の処理を書く */){
	foreach($_POST['books'] as $book_id){
		//㉖「getByid」関数を呼び出し、変数に戻り値を入れる。その際引数に㉕の処理で取得した値と⑧のDBの接続情報を渡す。
		//㉗ ㉖で取得した書籍の情報の「stock」と、㉔の変数を元にPOSTの「stock」から値を取り出して書籍情報の「stock」から値を引いた値を変数に保存する。
		//㉘「updateByid」関数を呼び出す。その際に引数に㉕の処理で取得した値と⑧のDBの接続情報と㉗で計算した値を渡す。
		//㉙ ㉔で宣言した変数をインクリメントで値を1増やす。
		$book = getByid($book_id,$pdo);
		$sum_stock = $book['stock'] - $_POST['stock'];
		// $sum_stock = $book['stock'] - $stock;
		updateByid($book_id,$pdo,$sum_stock);
		$count_books++;
		// $count++;
	}

	//㉚SESSIONの「success」に「入荷が完了しました」と設定する。
	//㉛「header」関数を使用して在庫一覧画面へ遷移する。
	$_SESSION['success'] = '入荷が完了しました';
	// header('Location: zaiko_ichiran.php');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>出荷確認</title>
<link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>
<body>
<div id="header">
	<h1>出荷確認</h1>
</div>
<form action="syukka_kakunin.php" method="post" id="test">
	<div id="pagebody">
		<div id="center">
			<table>
				<thead>
					<tr>
						<th id="book_name">書籍名</th>
						<th id="stock">在庫数</th>
						<th id="stock">出荷数</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					//㉜書籍数をカウントするための変数を宣言し、値を0で初期化する。
					$count_books = 0;
					//$count = 0;
					//㉝POSTの「books」から値を取得し、変数に設定する。
					//foreach(/* ㉝の処理を書く */){
					foreach($_POST['books'] as $book_id){
						//㉞「getByid」関数を呼び出し、変数に戻り値を入れる。その際引数に㉜の処理で取得した値と⑧のDBの接続情報を渡す。
						// ↑32ではなく33ではないか？
						$book = getByid($book_id,$pdo);
					?>
					<tr>
						<td><?php echo	$book['title']/* ㉟ ㉞で取得した書籍情報からtitleを表示する。 */;?></td>
						<td><?php echo	$book['stock']/* ㊱ ㉞で取得した書籍情報からstockを表示する。 */;?></td>
						<td><?php echo	$_POST['stock'][$count_books]/* ㊲ POSTの「stock」に設定されている値を㉜の変数を使用して呼び出す。 */;?></td>
					</tr>
					<input type="hidden" name="books[]" value="<?php echo $book/* ㊳ ㉝で取得した値を設定する */;?>">
					<input type="hidden" name="stock[]" value='<?php echo $_POST['stock'][$count_books] /* ㊴「POSTの「stock」に設定されている値を㉜の変数を使用して設定する。 */;?>'>
					<?php
						//㊵ ㉜で宣言した変数をインクリメントで値を1増やす。
						$count_books++;
						//$count =0;
					}
					?>
				</tbody>
			</table>
			<div id="kakunin">
				<p>
					上記の書籍を出荷します。<br>
					よろしいですか？
				</p>
				<button type="submit" id="message" formmethod="POST" name="add" value="ok">はい</button>
				<button type="submit" id="message" formaction="syukka.php">いいえ</button>
			</div>
		</div>
	</div>
</form>
<div id="footer">
	<footer>株式会社アクロイト</footer>
</div>
</body>
</html>
