<?php
session_start();
require('dbconnect.php');
require_once('login/fblogin.php');
require_once('editer.php');
require_once('security.php');

// if (!empty($_POST)) {
//     if ($_POST['action'] == '削除'){
//                 $sql = sprintf('DELETE FROM stories WHERE story_id=%d',
//                               $written_story['story_id']
//                               );
//         mysqli_query($db, $sql) or die(mysqli_error($db));
//     }
// }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>書きかけ一覧</title>
    <link href="css/profile.css" rel="stylesheet" type="text/css" media="all" />
    <link rel="shortcut icon" href="img/favicon.ico" />
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

<div id="wrap">
    <div class="written_stories">
        <h1>公開済みの記事</h1>
        <?php
        while($written_story = mysqli_fetch_assoc($written_stories)):
        ?>
        <!--<form action="profile.php" method="post">-->
        <a href="stories/story.php?story_id=<?php echo $written_story['story_id']; ?>">
        <h2><?php echo h($written_story['title']); ?></h2>
        <!-- <p><?php echo h($written_story['thumbnail']); ?></p> -->
        </a>
        <a class="edit" href="stories/write.php?story_id=<?php echo $written_story['story_id']; ?>&id=<?php echo $user_profile['id']; ?>">編集</a>
        <!--<form action="" method=post>-->
        <!--    <input type=hidden name="story_id" value=<?php $written_story['story_id']; ?>>-->
        <!--    <input type="submit" id="delete-story" value="削除" />-->
        <!--</form>-->
        <?php
            
        	if (!empty($_POST)) {
            
                        $sql = sprintf('DELETE FROM stories WHERE story_id=%d',
                                       $written_story['story_id']
                                      );
                mysqli_query($db, $sql) or die(mysqli_error($db));
            }
        ?>
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
        <!--一旦保留-->
        <!--<textarea class="story_id" style="display: block;"><?php echo h($writing_story['story_id']); ?></textarea>-->
        <!--<input type="button" id="delete-story" value="削除" />-->
        <?php
        endwhile;
        ?>
        </div>
    <a href="index.php">ホームへ戻る</a>
</div>

</body>
</html>