<?php
//該当ページを取得する
$story_id = $_GET['story_id'];
$sql = sprintf('SELECT title, article, thumbnail, writer_id, fb_id, name, introduce FROM stories INNER JOIN users ON stories.writer_id=users.id WHERE story_id=%d',
              $story_id
              );
$story = mysqli_query($db, $sql) or die(mysqli_error($db));
$story_data = mysqli_fetch_assoc($story);

$url = ((empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
?>

<div class="share">

<div class="sns">
<ul class="clearfix">
<!--ツイートボタン-->
<li class="twitter">
<a target = "_blank" href="https://twitter.com/intent/tweet?url=<?php echo urlencode($url)?>&text=<?php echo $story_data['title'] ?> "onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;"><i class="fa fa-twitter"></i><img alt="ツイッターボタン" src="img/twitter.png"><p>Twitter</p></a>
</li>

<!--Facebookボタン-->
<li class="facebook">
<a href="http://www.facebook.com/sharer.php?src=bm&u=<?php echo urlencode($url)?>&t=<?php echo $story_data['title']?>【refory.jp】" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;"><i class="fa fa-facebook"></i><img alt="Facebookボタン" src="img/fb.png"><p>Facebook</p></a>
</li>

<!--はてブボタン-->
<li class="hatebu">
<a href="http://b.hatena.ne.jp/add?mode=confirm&url=https://<?php echo $url ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=510');return false;" ><i class="fa fa-hatena"></i><img alt="はてぶボタン" src="img/hatebu.png"><p>はてブ</p></a>
</li>

</ul>

</div>
</div>