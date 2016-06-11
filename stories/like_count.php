<?php
class LikeCounter
{
    const SALT = 'hello_world.12345';
    private $log_like_dir;
    private $db_like_dir;
     
    function __construct($log_like_dir=null, $db_like_dir=null){
        //ログディレクトリ設定
        $this->log_like_dir    = is_null($log_like_dir) ? dirname(__FILE__) . '/log_like/' : $log_like_dir;
        $this->db_like_dir     = is_null($db_like_dir)  ? dirname(__FILE__) . '/db_like/' : $db_like_dir;
    }
 
    //IPをチェックしてカウントを増やす
    function log_like($story_id){
        
        $file = $this->log_like_dir . $story_id . '_' . md5($this::SALT . $story_id) . '.log_like';
         
        $data = array();
        $flag = true;
         
        $fp = fopen($file, 'a+b');
        flock($fp, LOCK_EX);
         
     
         
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
         
        if($flag){
            $count = $this->count_up($story_id);
        } else {
            $count = $this->get_count($story_id);
        }
 
        return $count;
    }
     
    //データベースのカウントを増やす
    function count_up($story_id, $num=1){
        $file = $this->db_like_dir . $story_id . '_' .md5($this::SALT . $story_id) . '.log_like';
         
        if(file_exists($file)){
            $count = (int)file_get_contents($file);
        } else {
            $count = 0;
        }
         
        if($num > 0){
            $count = $count + $num;
            file_put_contents( $file, $count, LOCK_EX );
        }
        return $count;
    }
     
    //データベースのカウントを得る
    function get_count($story_id){
        $count = $this->count_up($story_id, 0);
        return $count;
    }
}