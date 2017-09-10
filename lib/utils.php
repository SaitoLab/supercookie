<?php

error_reporting(E_ALL & _NOTICE);

define('DSN', 'mysql:host=localhost;dbname=fingerprint_research;charset=utf8');
define('DB_USER', 'fingerprint_php');
define('DB_PASSWORD', 'i63WOKjoprZf80Q9o');
/**
 * 引数に与えられた文字列をhtmlエスケープして返す．
 * @param string $str エスケープしてechoする文字列
 * @return string エスケープ済み文字列
 */
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
/**
 * ランダムな文字列を生成する．
 * @param int $length 生成する文字列の長さ
 * @return string ランダムな文字列
 */
function generateRandomString($length = 32) {
    $bytes = openssl_random_pseudo_bytes($length * 2);
    if ($bytes === false) {
        header('HTTP', true, 500);
        error('Internal Server Error');
    }
    return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
}
/**
 * @return \PDO
 */
function getPDO() {
    try {
        $dbh = new PDO(DSN, DB_USER, DB_PASSWORD);
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $dbh;
    } catch (PDOException $e) {
        header('HTTP', true, 500);
        error('DB Error');
    }
}
/**
 * セッションを破棄、トップページにリダイレクト
 * @param string $str 出力する文字列
 */
function error() {
    session_start();
    $_SESSION = array();
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
    header("Location: http://www.saitolab.org/fingerprint/");
    die();
}
/**
 * セッションを破棄、セッションクッキー消去
 */
function clearSession() {
    session_start();
    $_SESSION = array();
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
}
//最新のアクセスに対してhash化，保存する
function ssdeepNewFP(){
    //使用FP
    $features=["remote_addr","http_accept","http_accept_charset","http_accept_encoding","http_accept_language","http_connection","http_origin","http_user_agent","http_referer","touch","timezone","javascript_useragent","session_storage","local_storage","screen","device_pixel_ratio","pluginlist","sse","private_ip","fontlist"];
    // DBの最新データを取得
    $row = getNewDataFromDB();
    $arr=[];
    foreach($features as $feature){
        $arr[$feature]=$row[$feature];
    }
    $str=implode($arr);
    // $fuzzy = ssdeep_fuzzy_hash($str);
    $fuzzy = sha1($str);
    //ssdeep保存 ステートメント生成、bindするデータを配列で生成
    $statement = 'UPDATE fingerprint_data SET ';
    $inputParams = array();
    $value='ssdeep';
    $statement .= $value . '=:' . $value;
    $inputParams += array(':' . $value => $fuzzy);
    $statement .= ' WHERE id=:lastInsertId';
    $inputParams += array(':lastInsertId' => $_SESSION['lastInsertId']);
    // プリペアドステートメント実行
    $dbh = getPDO();
    $stmt = $dbh->prepare($statement);
    $stmt->execute($inputParams);
    return $fuzzy;
}
//最新のアクセス(今回のアクセス)を取得する
function getNewDataFromDB() {
    $dbh = getPDO();
    $stmt = $dbh->prepare('SELECT * FROM fingerprint_data ORDER BY id DESC LIMIT 1;');
    $stmt->execute();
    $rows = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($rows, $row);
    }
    $stmt->closeCursor();
    return $rows[0];
}
//最新の1000個のサンプルを取得する
function get1000DataFromDB() {
    $dbh = getPDO();
    $stmt = $dbh->prepare('SELECT http_cookie,1st_update,ssdeep FROM fingerprint_data ORDER BY id DESC LIMIT 1000;');
    $stmt->execute();
    $rows = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tmp = array();
        foreach ($row as $key => $value) {
            $tmp += array($key => $value);
        }
        array_push($rows, $tmp);
    }
    $stmt->closeCursor();
    return $rows;
}
