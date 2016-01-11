<?php
session_start();
require('dbconnect.php');

//Facebookクラスのインスタンスの準備
require_once("php-sdk/facebook.php");

//htmlspecialcharsのショートカット
function h($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$config = array(
    'appId'  => '1653590704887251',
    'secret' => '98e9267713857adc34e5bc72122008d0'
);

$facebook = new Facebook($config);
$userId = $facebook->getUser();
$user = $facebook->api('/me','GET');


//直アクセスのリダイレクト
if (!isset($userId)) {
    header('Location: http://refory.jp');
    }


//writer_idを抽出する 
$sql = sprintf('SELECT id FROM users WHERE fb_id="'.$userId.'"');
$user_data = mysqli_query($db, $sql) or die(mysqli_error($db));
$user_profile = mysqli_fetch_assoc($user_data);

//同じライターの公開済みの記事を取得する
$sql = sprintf('SELECT title, thumbnail, story_id FROM stories WHERE (writer_id = %d) AND (published > 0)',
              $user_profile['id']
              );
$written_stories = mysqli_query($db, $sql) or die(mysqli_error($db));

//同じライターの下書きの記事を取得する
$sql = sprintf('SELECT title, thumbnail, story_id FROM stories WHERE (writer_id = %d) AND (published = 0)',
              $user_profile['id']
              );
$writing_stories = mysqli_query($db, $sql) or die(mysqli_error($db));
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mypage</title>
    <link href="css/profile.css" rel="stylesheet" type="text/css" media="all" />
</head>
<body>
<div id="header">
    <div id="header_left">
    <a href="index.php"><img src="img/refory_logo.png"></a>
    </div>
    <div id="header_right">
    <?php
    if (isset($user)) {
        //ログイン済みでユーザー情報が取れていれば表示
        echo '<a href="profile.php"><img src="https://graph.facebook.com/' . $userId . '/picture"></a>';
        echo '<a id="write" href="stories/write.php?id=' . $user_profile['id'] . '">失敗談を書こう</a>';
        } else {
        //未ログインならログイン URL を取得してリンクを出力
        $loginUrl = $facebook->getLoginUrl();
        echo '<a href="' . $loginUrl . '">Login with Facebook</a>';
        }
    ?>
    </div>
</div>

<div id="wrap">
    <div class="written_stories">
        <h1>公開済みの記事</h1>
        <?php
        while($written_story = mysqli_fetch_assoc($written_stories)):
        ?>
        <a href="stories/story.php?story_id=<?php echo $written_story['story_id']; ?>">
        <h2><?php echo h($written_story['title']); ?></h2>
        <!-- <p><?php echo h($written_story['thumbnail']); ?></p> -->
        </a>
        <a class="edit" href="stories/write.php?story_id=<?php echo $written_story['story_id']; ?>&id=<?php echo $user_profile['id']; ?>">編集</a>
        <?php
        endwhile;
        ?>
        </div>
        <div class="writing_stories">
        <h1>下書きの記事</h1>
        <?php
        while($writing_story = mysqli_fetch_assoc($writing_stories)):
        ?>
        <a href="stories/write.php?story_id=<?php echo $writing_story['story_id']; ?>&id=<?php echo $user_profile['id']; ?>">
        <h2><?php echo h($writing_story['title']); ?></h2>
        <!-- <p><?php echo h($writing_story['thumbnail']); ?></p> -->
        </a>
        <?php
        endwhile;
        ?>
        </div>
    <a href="index.php">TOPへ戻る</a>
</div>
</body>
</html>