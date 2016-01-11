
<?php
session_start();
require('dbconnect.php');
//Facebookクラスのインスタンスの準備
require_once("php-sdk/facebook.php");

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


//最新ページを取得する
$sql = sprintf('SELECT title,thumbnail,article, fb_id, name, story_id FROM stories INNER JOIN users ON stories.writer_id=users.id WHERE published > 0 ORDER BY stories.published DESC LIMIT 0, 8');
$stories = mysqli_query($db, $sql) or die(mysqli_error($db));


//htmlspecialcharsのショートカット
function h($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>失敗談共有サイトrefory</title>
    <meta name="descpription" content="失敗から得た学びをシェアしよう！失敗談共有サイトrefory（リフォリー）">
    <meta name="keyword"　content="失敗談,社会人,ビジネスマン,仕事,仕事術,起業">
    <!--facebookシェア関連-->
    <meta property=”og:title” content=”失敗談共有サイトrefory” />
    <meta property=”og:description” content=”失敗から得た学びをシェアしよう！失敗談共有サイトrefory（リフォリー）” />
    <meta property=”og:url” content=”http://refory.jp/” />
    <meta property=”og:image” content=”http://refory.jp/img/refory_copy.jpg” />
   
    <link href="css/top.css" rel="stylesheet" type="text/css" media="all" />
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
    <a href="#"><img src="img/refory_logo.png"></a>
    </div>
    <div id="header_right">
    <?php
    if (isset($user)) {
        //ログイン済みでユーザー情報が取れていれば表示
        echo '<a href="profile.php"><img src="https://graph.facebook.com/' . $userId . '/picture"></a>';
        echo '<a id="write" href="stories/write.php?id=' . $id['id'] . '">失敗談を書こう</a>';
        } else {
        //未ログインならログイン URL を取得してリンクを出力
        $loginUrl = $facebook->getLoginUrl();
        echo '<a id="fb_login" href="' . $loginUrl . '">facebook でログイン</a>';
        }
    ?>
    </div>
</div>

<div id="copy_img">
    <img src="img/refory_copy.jpg">
</div>

<div id="wrap">
<!-- 新着記事の表示 -->
<h2>新着ストーリー</h2>
<div id="stories">
<?php
    while($story = mysqli_fetch_assoc($stories)):
?>
    <div class="story">
       <a href="stories/story.php?story_id=<?php echo $story['story_id']; ?>">
        <div class="writer">
        <p class="fb_img"><?php echo '<img src="https://graph.facebook.com/' . $story['fb_id'] . '/picture?type=normal">'; ?></p>
        <p class="name"><?php echo h($story['name']); ?></p>
        </div>
        <div class="text">
        <h3 class="title"><?php echo h(mb_strimwidth($story['title'], 0, 96, '...', 'UTF-8')); ?></h3>
        <!-- <p class="article"><?php echo mb_strimwidth($story['article'], 0, 64, '...', 'UTF-8'); ?></p> -->
        </div>
        </a>
    </div>
<?php
    endwhile;
?>
</div>
<div id="paging">
<a href="latest.php?page=2">次ページ</a＞
</div>
</body>
</html>