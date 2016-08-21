
<?php
session_start();
require('dbconnect.php');
require_once('login/fblogin.php');
require_once('security.php');

//投稿を取得する
$page = $_REQUEST['page'];
if ($page == '') {
    $page = 1;
}
$page = max($page, 1);

//ページを一定数取得する
$sql = 'SELECT COUNT(*) AS cnt FROM stories';
$recordSet = mysqli_query($db, $sql);
$table = mysqli_fetch_assoc($recordSet);
$maxPage = ceil($table['cnt']/ 12);
$page = min($page, $maxPage);

$start = ($page - 1)*12;
$start = max(0, $start);

//最新ページを取得する
$sql = sprintf('SELECT title,thumbnail, article, fb_id, name, story_id FROM stories INNER JOIN users ON stories.writer_id=users.id WHERE published > 0 ORDER BY stories.created DESC LIMIT 0, 12',
              $start
              );
// $sql = sprintf('SELECT title,thumbnail,article, fb_id, name, story_id FROM stories INNER JOIN users ON stories.writer_id=users.id WHERE published > 0 ORDER BY stories.published DESC LIMIT 0, 8');
$stories = mysqli_query($db, $sql) or die(mysqli_error($db));


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
    <meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
    <link rel="shortcut icon" href="img/favicon.ico" />
    <link href="css/top.css" rel="stylesheet" type="text/css" media="all" />
    <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-69511577-1', 'auto');
  ga('send', 'pageview');

</script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="jquery.autopager-1.0.0.js"></script>
</head>
<body>


<div id="header">
   
        <div id="header_left">
            <a href="index.php"><p><img src="img/logo-width.png" alt="refory" class="refory_logo_new"></p></a>
        </div>
        <div id="header_right">
        <?php
        if (isset($user)) { ?>
            <!--ログイン済みでユーザー情報が取れていれば表示 -->
            <div class="fb_img-header">
                <p class="fb_img-header"><?php echo '<a href="profile.php"><img src="https://graph.facebook.com/' . $userId . '/picture"></a>'; ?></p>
            </div>
            <div class="write">
            <?php echo '<a class="write" href="stories/write.php?id=' . $id['id'] . '">失敗談を書こう</a>'; ?>
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

<div id="refory_img">
    <div id="copy_img">
        <img src="img/refory_top.png" class="logo_img">
    </div>
</div>
<div id="wrap">
    <div  id="contents">
        
    <!-- 新着記事の表示 -->
    <h2 class = "headline">★新着ストーリー</h2>
    
        <div id="stories">
        <?php
            while($story = mysqli_fetch_assoc($stories)):
        ?>
            <div class="story">
               <a href="stories/story.php?story_id=<?php echo h($story['story_id']); ?>">
                <div class="writer">
                <p class="fb_img"><?php echo '<img src="https://graph.facebook.com/' . $story['fb_id'] . '/picture?type=normal">'; ?></p>
                <!--<p class="fb_img"><?php echo '<img src="https://graph.facebook.com/' . $userId . '/picture?type=normal">'; ?></p>-->
                <p class="name"><?php echo h($story['name']); ?></p>
                </div>
                <div class="text">
                <h3 class="title"><?php echo h(mb_strimwidth($story['title'], 0, 96, '...', 'UTF-8')); ?></h3>
                <!--<p class="article"><?php echo mb_strimwidth($story['article'], 0, 64, '...', 'UTF-8'); ?></p>-->
                </div>
                
                </a>
            </div>
        <?php
            endwhile;
        ?>
        </div>
        
    </div>
    <div id="next">

            <a href="latest.php?page=<?php echo($page+1); ?>" class = "button">次の記事を表示！</a>
            <img id="loading" src="/img/icon_loading.gif" alt="読み込み中"  width="29" height="29">
    </div>
</div>  
        

<div class = "footer">
    <div class="footer_left">
        <a href="inde.php"><img src="img/logo-width.png" alt="refory" class="refory_logo_new"></a>
    </div>
        <p class="copylight">2016 © refory.jp</p>
</div>

<script>
var maxpage = 99;
/* global $ */
$('#loading').css('display', 'none');
$.autopager({
    content: '#stories',// 読み込むコンテンツ
    link: '#next a', // 次ページへのリンク
    autoLoad: false,
 
    start: function(current, next){
      $('#loading').css('display', 'block');
      $('#next a').css('display', 'none');
    },
 
    load: function(current, next){
        $('#loading').css('display', 'none');
        $('#next a').css('display', 'block');
        if( current.page >= maxpage ){ //最後のページ
            $('#next a').hide(); //次ページのリンクを隠す
        }
    }
});
 
$('#next a').click(function(){ // 次ページへのリンクボタン
    $.autopager('load'); // 次ページを読み込む
    return false;
});
</script>
</body>
</html>