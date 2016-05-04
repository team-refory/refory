<?php
session_start();

require('../dbconnect.php');
//Facebookクラスのインスタンスの準備
require_once('../login/fblogin.php');
require_once('../security.php');


if( isset( $_POST['task']) && ($_POST['task'] == 'comment_insert') && !empty($userId)) {
                $sql = sprintf('INSERT INTO comment SET story_id=%d,fb_id=%d,content="%s",created=NOW(), published=NOW()',
                               mysqli_real_escape_string($db, $_POST['story_id']),
                               $userId,
                               mysqli_real_escape_string($db, $_POST['content']));
                              
    mysqli_query($db, $sql) or die(mysqli_error($db));
}else{
    header ('location: story.php');

}
?>