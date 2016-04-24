<?php


        global $sql;
        global $userId;
        global $user_data;
        global $user_profile;
        global $writing_stories;
        global $written_stories;
        
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