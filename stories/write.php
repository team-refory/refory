<?php
session_start();
require('../dbconnect.php');
/* ログインしてないで直アクセスした際のリダイレクト
if (empty($_GET['id'])){
    header('Location: http://refory.jp/index.php');
    exit();
}
*/

//Facebookクラスのインスタンスの準備
require_once("../php-sdk/facebook.php");

$config = array(
    'appId'  => '1653590704887251',
    'secret' => '98e9267713857adc34e5bc72122008d0'
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


//下書きを編集する
if (!empty($_GET['story_id'])) {
    $sql = sprintf('SELECT title, article FROM stories WHERE story_id=%d',
                  $_GET['story_id']
                  );
    $writing_story = mysqli_query($db, $sql) or die(mysqli_error($db));
    $writing_data = mysqli_fetch_assoc($writing_story);
    $writing_title = $writing_data['title'];
    $writing_article = $writing_data['article'];
}

//投稿を記録する
if (!empty($_POST)) {  //フォームから送信されたかの確認
    /*
    $fileName = $_FILES['thumbnail']['name'];
    if (!empty($fileName)) {
        $ext = substr($fileName, -3);
        if ($ext != 'jpg' && $ext != 'JPG' && $ext != 'gif' && $ext != 'GIF' && $ext != 'png' && $ext != 'PNG') {
            $error['thumbnail'] = 'type';
        }
    if ($_POST['title'] != ''&&$_POST['article'] != '') {
        //画像をアップロードする
        $thumbnail = date('YmdHis') . $_FILES['thumbnail']['name'];
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], '../img/' . $thumbnail);
        */
        //投稿をdbに保存
        if ($_POST['action'] == '公開する') {
            $sql = sprintf('INSERT INTO stories SET writer_id=%d,title="%s", article="%s", created=NOW(), published=NOW()',
                           mysqli_real_escape_string($db, $_GET['id']),
                           mysqli_real_escape_string($db, $_POST['title']),
                           mysqli_real_escape_string($db, $_POST['article'])
                          );
        } else if ($_POST['action'] == '下書き保存する') {
            $sql = sprintf('INSERT INTO stories SET writer_id=%d,title="%s", article="%s", created=NOW()',
                           mysqli_real_escape_string($db, $_GET['id']),
                           mysqli_real_escape_string($db, $_POST['title']),
                           mysqli_real_escape_string($db, $_POST['article'])
                          );
        } else if ($_POST['action'] == '更新する') {
            $sql = sprintf('UPDATE stories SET title="%s", article="%s" WHERE story_id=%d',
                           mysqli_real_escape_string($db, $_POST['title']),
                           mysqli_real_escape_string($db, $_POST['article']),
                           mysqli_real_escape_string($db, $_GET['story_id'])
                          );
        } else if ($_POST['action'] == '削除する') {
            $sql = sprintf('DELETE FROM stories WHERE story_id=%d',
                           mysqli_real_escape_string($db, $_GET['story_id'])
                          );
        }
        mysqli_query($db, $sql) or die(mysqli_error($db));
        /*
        header('Location:  http://refory.jp/index.php');  //投稿の重複を防ぐために再度同じページを開かせている
        exit();
        */
        
    }
    //}
//}

?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>失敗談作成</title>
      <!-- include jquery -->
  <script src="//code.jquery.com/jquery-1.11.3.min.js"></script> 

  <!-- include libraries BS3 -->
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.1/css/bootstrap.min.css" />
  <script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.0.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" />

  <!-- include summernote -->
  <link rel="stylesheet" href="../dist/summernote.css">
  <script type="text/javascript" src="../dist/summernote.js"></script>

  <script type="text/javascript">
    $(function() {
      $('.summernote').summernote({
        height: 200
      });
/*　★なんかエラーを返していそう
      $('form').on('submit', function (e) {
        e.preventDefault();
        alert($('.summernote').code());
      });
*/
    });
  </script>
  <link href="../css/write.css" rel="stylesheet" type="text/css" media="all" />
</head>
<body>
<div id="header">
    <div id="header_left">
    <a href="../index.php"><img src="../img/refory_logo.png"></a>
    </div>
    <div id="header_right">
    <?php
    if (isset($user)) {
        //ログイン済みでユーザー情報が取れていれば表示
        echo '<a href="../profile.php"><img src="https://graph.facebook.com/' . $userId . '/picture"></a>';
        echo '<a id="write" href="write.php?id=' . $id['id'] . '">失敗談を書こう</a>';
        } else {
        //未ログインならログイン URL を取得してリンクを出力
        $loginUrl = $facebook->getLoginUrl();
        echo '<a id="fb_login" href="' . $loginUrl . '">facebook でログイン</a>';
        }
    ?>
    </div>
</div>
   
    <div id="wrap">
    <form action="" method="post" enctype="multipart/form-data">
        <dl>
            <dt>タイトル</dt>
            <dd>
                <textarea name="title" cols=64 rows=1><?php if (!empty($writing_title)): ?><?php echo $writing_title; ?><?php endif; ?></textarea>
            </dd>
            <!--
            <dt>サムネイル</dt>
            <dd>
                <input type="file" name="thumbnail" size="50"/>
                <?php if (!empty($error) && $error['thumbnail'] == 'type'): ?>
                <p class="error">サムネイルはgifまたはjpg、pngの画像を指定してください</p>
                <?php endif; ?>
                <?php if (!empty($_GET['story_id'])): ?>
                <p class="error">恐れ入りますが改めてサムネイルを指定してください</p>
                <?php endif; ?>
            </dd>
            -->
            <dt>本文</dt>
            <dd>
                <textarea name="article" class="summernote" id="contents" required="required" title="Contents"><?php if (!empty($writing_article)): ?><?php echo $writing_article; ?><?php endif; ?></textarea>
            </dd>
        </dl>
        <div>
            <input type="submit" name="action" value="更新する" />
            <input type="submit" name="action" value="下書き保存する" />
            <input type="submit" name="action" value="公開する" />
            <input type="submit" name="action" value="削除する" />
        </div>
    </form>
    </div>
</body>
</html>