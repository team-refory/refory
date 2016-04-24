<?php 
//Facebookクラスのインスタンスの準備
require_once("php-sdk/facebook.php");

$config = array(
    'appId'  => '949703361751323',
    'secret' => 'fd41a5265af7eff69f2dc781b4d86e1d'
);

$facebook = new Facebook($config);

//ログイン済みの場合はユーザー情報を取得
    if ($facebook->getUser()) {
        try {
            $userId = $facebook->getUser();//ログインしたユーザーのID取得
            $user = $facebook->api('/me','GET');//ユーザーデータを取得する部分
            //idの抽出
            $sql_Idcheck = sprintf('SELECT id FROM users WHERE fb_id="'.$userId.'"');
            $record1 = mysqli_query($db, $sql_Idcheck) or die(mysqli_error($db));
        $id = mysqli_fetch_assoc($record1);
            
            //登録済みか確認
            $sql_recordcheck = sprintf('SELECT COUNT(*) AS cnt FROM users WHERE fb_id="'.$userId.'"');
            $record2 = mysqli_query($db, $sql_recordcheck) or die(mysqli_error($db));
        $table = mysqli_fetch_assoc($record2);
            if ($table['cnt'] == 0) {
                //id登録
                $sql_insert = sprintf('INSERT INTO users SET fb_id="'.$userId.'",name="'.$user['name'].'", created="%s"',
                      date('Y-m-d H:i:s')
                      );
                      mysqli_query($db, $sql_insert) or die(mysqli_error($db));
            }
        } catch(FacebookApiException $e) {
            //取得に失敗したら例外をキャッチしてエラーログに出力
            error_log($e->getType());
            error_log($e->getMessage());
        }
    }

?>