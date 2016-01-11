<?php
session_start();
require('../dbconnect.php');
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

//htmlspecialcharsのショートカット
function h($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

//該当ページを取得する
$story_id = $_GET['story_id'];
$sql = sprintf('SELECT title, article, thumbnail, writer_id, fb_id, name, introduce FROM stories INNER JOIN users ON stories.writer_id=users.id WHERE story_id=%d',
              $story_id
              );
$story = mysqli_query($db, $sql) or die(mysqli_error($db));
$story_data = mysqli_fetch_assoc($story);

//同じライターの記事を取得する

$sql = sprintf('SELECT title, thumbnail, story_id FROM stories WHERE (story_id != %d) AND (writer_id = %d) AND (published > 0)',
              $story_id,
              $story_data['writer_id']
              );
$samewriter_stories = mysqli_query($db, $sql) or die(mysqli_error($db));

//新着ストーリーを取得する
$sql = sprintf('SELECT title,thumbnail,article, fb_id, name, story_id FROM stories INNER JOIN users ON stories.writer_id=users.id WHERE published > 0 ORDER BY stories.published DESC LIMIT 0, 8');
$latest_stories = mysqli_query($db, $sql) or die(mysqli_error($db));

?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>記事ページ</title>
    <link href="../css/story.css" rel="stylesheet" type="text/css" media="all" />
    <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-69511577-1', 'auto');
  ga('send', 'pageview');

</script>
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
    <div id="story">
            <h1><?php echo h($story_data['title']); ?></h1>
            <!-- <p><?php echo ('<img src="../img/' . $story_data['thumbnail'] . '" />'); ?></p> -->
            <p><?php echo $story_data['article']; ?></p>
    </div>
    <div id="side">
        <div id="profile">
            <p id="fb_img"><?php echo '<img src="https://graph.facebook.com/' . $story_data['fb_id'] . '/picture">'; ?></p>
            <p id="name"><?php echo h($story_data['name']); ?></p>
            <!--<p><?php echo h($story_data['introduce']); ?></p>-->
        </div>
            <h3>この人の他の失敗談</h3>
            <div class="samewriter_stories">
            <?php
                while($stories = mysqli_fetch_assoc($samewriter_stories)):
            ?>
            <a class="samewriter_story" href="story.php?story_id=<?php echo $stories['story_id']; ?>">
            <p><?php echo mb_strimwidth($stories['title'], 0, 55, '...', 'UTF-8'); ?></p>
            <!-- <p><?php echo h($stories['thumbnail']); ?></p> -->
            </a>
            <?php
            endwhile;
            ?>
        </div>
            <h3>新着の失敗談</h3>
            <div class="latest_stories">
            <?php
                while($stories = mysqli_fetch_assoc($latest_stories)):
            ?>
            <a class="latest_story" href="story.php?story_id=<?php echo $stories['story_id']; ?>">
            <p><?php echo mb_strimwidth($stories['title'], 0, 55, '...', 'UTF-8'); ?></p>
            <!-- <p><?php echo h($stories['thumbnail']); ?></p> -->
            </a>
            <?php
            endwhile;
            ?>
        </div>
    </div>
</div>
</body>
</html>