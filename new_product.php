<?php declare(strict_types=1);

session_start();

function console_log($data): void
{
    echo '<script>';
    echo 'console.log(' . json_encode($data) . ')';
    echo '</script>';
}

function getLastId($con)
{
    $sql = 'select id from books where id=(select max(id) from books)';
    return $con->query($sql)->fetch(PDO::FETCH_ASSOC)['id'];
}

function addRecord($con): void
{
    // title author salesDate isbn price stock
    $id = getLastId($con) + 1;
    $title = $_POST['title'];
    $author = $_POST['author'];
    $salesDate = $_POST['salesDate'];
    $isbn = $_POST['isbn'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    //$sql = "INSERT INTO books VALUES (${id},${title},${author},${salesDate},${isbn},${price},${stock})";
    $sql = "INSERT INTO books(id,title,author,salesDate,isbn,price,stock)VALUES('{$id}','{$title}','{$author}','{$salesDate}','{$isbn}','{$price}','{$stock}')";
    $con->query($sql);
}

function connectToDatabase()
{
    $db_name = 'zaiko2020_yse';
    $host = 'localhost';
    $dsn = "mysql:dbname={$db_name};host={$host}; charset=utf8";
    $user_name = 'zaiko2020_yse';
    $password = '2020zaiko';

    try {
        return new PDO($dsn, $user_name, $password);
    } catch (PDOException $e) {
        echo 'エラー!: ' . $e->getMessage() . '<br/gt;';
        die();
    }
}

if ($_SESSION['login'] == false) {
    $_SESSION['error2'] = 'ログインしてください';
    header('Location: login.php');
}

function checkInputData(): void
{
    // TODO

    $title = $_POST['title'];
    $author = $_POST['author'];
    $salesDate = $_POST['salesDate'];
    $isbn = $_POST['isbn'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
}

$pdo = connectToDatabase();

if (isset($_POST['add']) && $_POST['add'] = 'ok') {
    // checkInputData();

    addRecord($pdo);

    $_SESSION['success'] = '新商品を追加しました';
    header('Location: zaiko_ichiran.php');
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>新商品追加</title>
    <link rel="stylesheet" href="css/ichiran.css" type="text/css" />
</head>

<body>
    <!-- ヘッダ -->
    <div id="header">
        <h1>新商品追加</h1>
    </div>

    <!-- メニュー -->
    <div id="menu">
        <nav>
            <ul>
                <li><a href="zaiko_ichiran.php?page=1">書籍一覧</a></li>
            </ul>
        </nav>
    </div>

    <form action="new_product.php" method="post">
        <div id="pagebody">
            <!-- エラーメッセージ -->
            <div id="error">
                <?php
            if (!empty($_SESSION["error"])) {
                echo $_SESSION['error'];
            }
            ?>
            </div>
            <div id="center">
                <table>
                    <thead>
                        <tr>
                            <th id="id">ID</th>
                            <th id="book_name">書籍名</th>
                            <th id="author">著者名</th>
                            <th id="salesDate">発売日</th>
                            <th id="isbm">ISBM</th>
                            <th id="itemPrice">金額</th>
                            <th id="stock">在庫数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $maxid = getLastId($pdo);
                        $newid = $maxid + 1;
                        ?>
                        <tr>
                            <td><?= $newid; ?>
                            </td>
                            <input type="hidden" name="ID" value="<?= $newid; ?>">

                            <td><input type='text' name='title' size='20' maxlength='11' required></td>
                            <td><input type='text' name='author' size='20' maxlength='11' required></td>
                            <td><input type='text' name='salesDate' size='20' maxlength='11' required></td>
                            <td><input type='text' name='isbn' size='20' maxlength='11' required></td>
                            <td><input type='text' name='price' size='10' maxlength='11' required></td>
                            <td><input type='text' name='stock' size='10' maxlength='11' required></td>

                        </tr>
                    </tbody>
                </table>
                <button type="submit" id="kakutei" formmethod="POST" name="add" value="ok">確定</button>
            </div>
        </div>
    </form>
    <!-- フッター -->
    <div id="footer">
        <footer>株式会社アクロイト</footer>
    </div>
</body>

</html>