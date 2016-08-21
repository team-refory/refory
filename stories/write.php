<?php
session_start();
require('../dbconnect.php');
//Facebookクラスのインスタンスの準備
require_once('../login/fblogin.php');
require_once('../security.php');

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

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
        if ($_POST['action'] == '公開') {
            $sql = sprintf('INSERT INTO stories SET writer_id=%d,title="%s", article="%s", created=NOW(), published=NOW()',
                           mysqli_real_escape_string($db, $_GET['id']),
                           mysqli_real_escape_string($db, $_POST['title']),
                           mysqli_real_escape_string($db, $_POST['article'])
                          );
        } else if ($_POST['action'] == '下書き保存') {
            $sql = sprintf('INSERT INTO stories SET writer_id=%d,title="%s", article="%s", created=NOW()',
                           mysqli_real_escape_string($db, $_GET['id']),
                           mysqli_real_escape_string($db, $_POST['title']),
                           mysqli_real_escape_string($db, $_POST['article'])
                          );
        } else if ($_POST['action'] == '更新') {
            $sql = sprintf('UPDATE stories SET title="%s", article="%s" WHERE story_id=%d',
                           mysqli_real_escape_string($db, $_POST['title']),
                           mysqli_real_escape_string($db, $_POST['article']),
                           mysqli_real_escape_string($db, $_GET['story_id'])
                          );
        } else if ($_POST['action'] == '削除') {
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
    ['Bold','Strike']
    ,['Format']
    ,['CreatePlaceholder']
    ];
    CKEDITOR.config.height = '400px';
    CKEDITOR.config.extraPlugins='confighelper';
    // CKEDITOR.config.extraPlugins='autogrow';
    CKEDITOR.config.autogrow_minHeight='400px';
    CKEDITOR.config.autogrow_maxHeight='500px';
    CKEDITOR.config.resize_minHeight = '400px';
    CKEDITOR.config.resize_maxHeight = '500px';
    CKEDITOR.replace( 'ckeditor', {
	extraPlugins : 'confighelper',
	extraPlugins: 'placeholder',
	extraPlugins:'autogrow',
	extraPlugins:'autogrow_minHeight',
	extraPlugins:'autogrow_maxHeight',
	autogrow_minHeight:'400px',
	autogrow_maxHeight:'600px'
    });
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
                echo '<a id="fb_login" href="' . $loginUrl . '"> Facebookログインして<br><span>あなたの失敗談を書こう</span></a>';
                }
            ?>
            </div>
        </div>
    </div>
   
    <div id="wrap">
    <div id="container">
        
        <div class="white-board">
            <form id="editorBottun">
                <ul style="list-style:none;">
                   <li><input type="button" id="decoBold" value="太文字"/></li>
                   <li><input type="button" id="decoStrike" value="取消線"/></li>
                   <li><input type="button" id="decoH3" value="見出し"/></li>
                </ul>
            </form>
            
            <form action="" method="post" enctype="multipart/form-data">
                <div class="post-title">
                    <textarea name="title" placeholder="タイトル..." style="overflow: hidden; word-wrap: break-word; resize: none;" ><?php if (!empty($writing_title)): ?><?php echo $writing_title; ?><?php endif; ?></textarea>
                </div>
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
        
    
            <form action="" method="post" id="testForm" onsubmit="">
            <textarea name="article" id= "ckeditor" class="ckeditor" placeholder = "あなたの失敗談をここに入力..."><?php if (!empty($writing_article)): ?><?php echo $writing_article; ?><?php endif; ?></textarea>
            
        </div>
    
            <div class = "footer">
                <div class="function-area">
                    <ul style="list-style:none;">
                        
                    <?php
                        if (preg_match("|^https?://refory\.jp\/profile\.php|", $referer)) {
                           // マイページからの遷移した場合の処理
                            echo '<li><a href="../profile.php" class = "mypage">書きかけ一覧</a></li>';
                            echo '<li><input type="submit" class="delete_button" name="action" value="削除" onClick="return submit();" /></li>';
                            echo '<li><input type="submit" class="save_button" name="action" value="下書き保存" /></li>';
                            echo '<li><input type="submit" class="update_button" name="action" value="更新" /></li>';
                            echo '<li><input type="submit" class="release_button" name="action" value="公開" /></li>';
                            
                        }
                        else {
                         /* 直接アクセス時の処理 */
                         echo '<li><a href="../profile.php" class = "mypage">書きかけ一覧</a></li>';
                         echo '<li><input type="submit" class="save_button" name="action" value="下書き保存" /></li>';
                         echo '<li><input type="submit" class="release_button" name="action" value="公開" /></li>';
                        }
                    ?>
                        
                    </ul>
                </div>
            </div>
            </form>
        </div>
    </div>
<script>
   document.getElementById('decoBold').onclick = function(){
       document.getElementById('cke_13').click();
   };
   document.getElementById('decoStrike').onclick = function(){
       document.getElementById('cke_14').click();
   };
   document.getElementById('decoH3').onclick = function(){
       document.getElementById('cke_41_option').click();
   };
</script>
<script type="text/javascript">

function submit(){

	// 「OK」時の処理開始 ＋ 確認ダイアログの表示
	if(window.confirm('削除してもよろしいでしょうか？')){
        
		this.location.href = "../profile.php"; // example_confirm.html へジャンプ
        return true;
	}

	// 「キャンセル」時の処理開始
	else{

	window.alert('キャンセルされました'); // 警告ダイアログを表示
         this.location.href="../profile.php";
         return false;
	}
	// 「キャンセル」時の処理終了

}

</script>
</body>
</html>
