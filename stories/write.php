<?php
session_start();
require('../dbconnect.php');
//Facebookクラスのインスタンスの準備
require_once('../login/fblogin.php');
require_once('../security.php');


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
        /*ページ遷移
        header('Location:  http://localhost/refory/index.php');  //投稿の重複を防ぐために再度同じページを開かせている
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

  <link href="../css/write.css" rel="stylesheet" type="text/css" media="all" />
  <link rel="shortcut icon" href="../img/favicon.ico" />
<script src="jquery.min.js" type="text/javascript"></script>
  <script src="../ckeditor/ckeditor.js" type="text/javascript"></script>
    <script type="text/javascript">
    CKEDITOR.config.toolbar = [
['Bold','Strike'],
['CreatePlaceholder']
,['Format']

];
    </script>

 <style type="text/css">
a {
	color: #03f;
	text-decoration: underline;
}
</style>
  
  
</head>
<body>
<div id="header">
        <div id="header_left">
            <a href="../index.php"><img src="img/logo-width.png" alt="refory" class="refory_logo_new"></p></a>
        </div>
        <div id="header_right">
            <?php
            if (isset($user)) { ?>
                <!--ログイン済みでユーザー情報が取れていれば表示 -->
            <div class="fb_img-header">
                <p class="fb_img-header"><?php echo '<a href="../profile.php"><img src="https://graph.facebook.com/' . $userId . '/picture"></a>'; ?></p>
            </div>
                <div class="write">
                <?php echo '<a class="write" href="write.php?id=' .$id['id'] . '">失敗談を書こう</a>'; ?>
            <?php } else { ?>
                <!--未ログインならログイン URL を取得してリンクを出力 -->
                <?php
                $loginUrl = $facebook->getLoginUrl();
                echo '<a id="fb_login" href="' . $loginUrl . '">facebook でログイン</a>';
                }
            ?>
            </div>
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
        
        <div id="container">



<h1>本文</h1>
<form action="" method="post" id="testForm" onsubmit="">
<textarea name="article" class="ckeditor"><?php if (!empty($writing_article)): ?><?php echo $writing_article; ?><?php endif; ?></textarea>
</form>


</div>
       
       
       
        
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