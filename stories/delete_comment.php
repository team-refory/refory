<?php
    session_start();

    require('../dbconnect.php');
    
	if( isset( $_POST['task']) && $_POST['task'] == 'delete_comment' ){
		$commentId = (int)( $_POST['commentId'] );
		$sql = sprintf('DELETE FROM comment WHERE comment_id = %s',
		    $commentId);
		mysqli_query($db, $sql) or die(mysqli_error($db));
	}
?>