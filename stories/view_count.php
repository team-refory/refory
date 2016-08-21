<?php
require('../dbconnect.php');

class ViewCounter
{
    const SALT = 'hello_world.12345';
    private $log_dir;
    private $db_dir;
     
    function __construct($log_dir=null, $db_dir=null){
        //ログディレクトリ設定
        $this->log_dir    = is_null($log_dir) ? dirname(__FILE__) . '/log/' : $log_dir;
        $this->db_dir     = is_null($db_dir)  ? dirname(__FILE__) . '/db/' : $db_dir;
    }
 
    //IPをチェックしてカウントを増やす
    function log($story_id){
        $ip = date("Ymd_") . md5($this::SALT . $_SERVER['REMOTE_ADDR']);
 
        $file = $this->log_dir . $story_id . '_' . md5($this::SALT . $story_id) . '.log';
         
        $data = array();
        $flag = true;
         
        $fp = fopen($file, 'a+b');
        flock($fp, LOCK_EX);
         
        //直近100件までを読み込む
        for($i=0;$i<100;$i++){
            if(feof($fp)) break;
            $line = fgets($fp);
            if($ip === rtrim($line)){
                $flag = false;
                break;
            } else {
                $data[] = $line;
            }
        }
     
        if($flag){
            array_unshift($data, $ip . "\n");
            ftruncate ($fp, 0);
            rewind($fp);
            foreach($data as $value){
                fwrite($fp, $value);
            }
        }
         
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
        $file = $this->db_dir . $story_id . '_' .md5($this::SALT . $story_id) . '.log';
         
        if(file_exists($file)){
            $count = (int)file_get_contents($file);
        } else {
            $count = 0;
        }
         
        if($num > 0){
            $count = $count + $num;
            file_put_contents( $file, $count, LOCK_EX );
            $db = mysqli_connect('localhost', 'harubuta', '' , 'refory') or die(mysqli_connect_error());
            mysqli_set_charset($db, 'utf8');
            $sql = sprintf('INSERT INTO views SET story_id=%d,views="%d",created=NOW(), published=NOW()',
                              $story_id,
                              $count
                              );
            mysqli_query($db, $sql) or die(mysqli_error($db));
            
        }
        return $count;
    }
     
    //データベースのカウントを得る
    function get_count($story_id){
        $count = $this->count_up($story_id, 0);
        return $count;
        
        
    }
}