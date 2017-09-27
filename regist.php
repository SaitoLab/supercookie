<?php
try{
$dbh = new PDO{

    'mysql:host=localhost;dbname=fingerprint_research;charset=utf8',
    'rkoshiba',
    'password',
    array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_EMULATE_PREPARES => false,
    )
  );
  $name = filter_input(INPUT_POST, 'user'); //index.htmlからPOSTメソッドで送られてきた値を受け取っている

  $stmt = $dbh->prepare('insert into user(name) values(?)'); //例えば以下のコメントのように書く
  // $stmt = $dbh->prepare('SELECT name FROM (テーブル名) WHERE name = ?');
  // ポイントはユーザからのデータをSQLに渡す時は上のように渡したい値を?で表すこと
  $stmt->bindValue(1, $name); //$sampleの代わりに入れたいデータを用意する
  $stmt->execute(); //SQL実行。INSERT文みたいな登録するだけならここまででいいはず。SELECT文あたりを実行するなら実行結果を取り出す処理をこれ以降書いていく

} catch (PDOException $e) {
  header('Content-Type: text/plain; charset=UTF-8', true, 500);
  exit($e->getMessage());
}

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>DB登録処理完了</title>
</head>
<body>
  <p>データベースに登録しました。</p>
</body>
</html>

