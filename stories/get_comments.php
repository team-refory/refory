<?php
session_start();

require('../dbconnect.php');
//Facebookクラスのインスタンスの準備
require_once('../login/fblogin.php');
require_once('../security.php');


$sql = sprintf('SELECT comment_id, story_id, CM.fb_id, UR.name, CM.content FROM comment CM INNER JOIN users UR ON CM.fb_id = UR.fb_id WHERE story_id = %d and published > 0 ORDER BY CM.created DESC LIMIT 0, 10',
            mysqli_real_escape_string($db, $_POST['story_id'])
              );
$comments = mysqli_query($db, $sql) or die(mysqli_error($db));

    while($comment_list = mysqli_fetch_assoc($comments)):
        echo '<div class="comment_list">';
        echo '<textarea class="comment_id" style="display: none;">'.h($comment_list['comment_id']).'</textarea>';
    	echo '<div class="user_info">';
    	echo '<div class="user_image"><img src="https://graph.facebook.com/' . $comment_list['fb_id'] . '/picture?type=normal"> </a></div>';
    	echo '<div class="user_name">'.h($comment_list['name']).'</div>';
    	echo '</div>';
    	echo '<div class="user_comment">';
        echo '<p class="comment_text">'.h($comment_list['content']).'</p>';
        //バグ発見のため一旦コメントアウト
        // if($comment_list['fb_id'] == $userId){
        //     echo '<input type="button" class="delete" value="削除" />';
        // }
    	echo '</div>';
    	echo '</div>';
    endwhile;
    



?>