<?php
session_start();

require('../dbconnect.php');
//Facebookクラスのインスタンスの準備
require_once('../login/fblogin.php');
require_once('../security.php');
require_once('view_count.php');
require_once('like_count.php');

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

//count access

$counter = new ViewCounter();

$count = $counter->log( $story_id );


?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>記事ページ</title>
    <link href="../css/story.css" rel="stylesheet" type="text/css" media="all" />
    <link rel="shortcut icon" href="../img/favicon.ico" />
    <meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
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
    <div id="contents">
    <?php include '../sns.php' ; ?>
    <div id="story">
        <h1><?php echo h($story_data['title']); ?></h1>
            <div id="profile-mobile">
                <p id="fb_img"><?php echo '<img src="https://graph.facebook.com/' . $story_data['fb_id'] . '/picture">'; ?></p>
                <p id="name">by&nbsp;&nbsp;<?php echo h($story_data['name']); ?></p>
            </div>
        <!-- <p><?php echo ('<img src="../img/' . $story_data['thumbnail'] . '" />'); ?></p> -->
        <p><?php echo $story_data['article']; ?></p>
    </div>
    <!--<div id="btn01"><p><a href="javascript:void(0);">いいね！</a></p><span></span></div>-->
    <div id="side">
        <div id="profile">
            <p id="fb_img"><?php echo '<img src="https://graph.facebook.com/' . $story_data['fb_id'] . '/picture">'; ?></p>
            <p id="name"><?php echo h($story_data['name']); ?></p>
            <!--<p><?php echo h($story_data['introduce']); ?></p>-->
        </div>
            <h3>この人の他の失敗談</h3>
            <ul class="samewriter_stories">
                <?php
                    while($stories = mysqli_fetch_assoc($samewriter_stories)):
                ?>
                <li class="writer_story"><a href="story.php?story_id=<?php echo $stories['story_id']; ?>" class="writer_story_inner">
                <p><?php echo mb_strimwidth($stories['title'], 0, 55, '...', 'UTF-8'); ?></p>
                <!-- <p><?php echo h($stories['thumbnail']); ?></p> -->
                </a></li>
                <?php
                    endwhile;
                ?>
            </ul>
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
        <div id="comment_area">
            
            <div class="comments_wrapper">
			</div>
        	<form id="comment_form">
    			<div class="new_post_wrapper">
    				<div class="new_post_content">
    				    <textarea class="comment_insert_text" name="comment" value="" rows="5" cols="30" placeholder="感想をおくる..." style="height: 40px;overflow: hidden;word-wrap: break-word;resize: horizontal;"></textarea>
    				    <input type="button" id="send-comment" value="おくる" />
    				    
    				</div>
    			
    			</div>
    			<textarea class="comment_story_id" name="story_id" value="" rows="5" cols="30" style="display: none;"><?php echo $story_id ?></textarea>
    			<textarea class="comment_user_id" name="user_id" value="" rows="5" cols="30" style="display: none;"><?php echo $userId ?></textarea>
        	</form>
        	
        </div>
    </div>
     <a href="" onclick="scrollToTop(); return false" id="scroll-to-top" class="scroll-to-top-not-display">▲</a>
    
</div>
<div class = "footer">
    <div class="footer_left">
        <a href="../index.php"><img src="../img/logo-width.png" alt="refory" class="refory_logo_new"></a>
    </div>
        <p class="copylight">2016 © refory.jp</p>
</div>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script src="js/jquery-2.1.4..min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/comment.js"></script>
<script type="text/javascript" src="js/autoResize.js"></script>
<script type="text/javascript" src="js/count.js"></script>
<script >
function scrollToTop() {
  var x1 = x2 = x3 = 0;
  var y1 = y2 = y3 = 0;
  if (document.documentElement) {
    x1 = document.documentElement.scrollLeft || 0;
    y1 = document.documentElement.scrollTop || 0;
  }
  if (document.body) {
    x2 = document.body.scrollLeft || 0;
    y2 = document.body.scrollTop || 0;
  }
  x3 = window.scrollX || 0;
  y3 = window.scrollY || 0;
  var x = Math.max(x1, Math.max(x2, x3));
  var y = Math.max(y1, Math.max(y2, y3));
  window.scrollTo(Math.floor(x / 2), Math.floor(y / 2));
  if (x > 0 || y > 0) {
    window.setTimeout("scrollToTop()", 30);
  }
}
</script>
<script>
    var element = null;
window.onscroll = function() {
    var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
    var scrollHeight = document.documentElement.scrollHeight || document.body.scrollHeight;
    if (element == null) {
      element = document.getElementById('scroll-to-top');
    }
    if (scrollTop / scrollHeight > 0.1) {
      element.classList.remove('scroll-to-top-not-display');
    } else {
      element.classList.add('scroll-to-top-not-display');
    }
}
</script>


</body>
</html>