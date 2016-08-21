
<?php
session_start();
require('dbconnect.php');
require_once('login/fblogin.php');
require_once('security.php');



?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
    <link rel="shortcut icon" href="img/favicon.ico" />
    <link href="css/top.css" rel="stylesheet" type="text/css" media="all" />
    <link rel="stylesheet" href="css/template/normalize.css">
        <link rel="stylesheet" href="css/template/bootstrap.min.css">
        <link rel="stylesheet" href="css/template/font-awesome.min.css">
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/template/owl.carousel.css">
        <link rel="stylesheet" href="css/template/owl.theme.css">
        <link rel="stylesheet" href="css/template/animate.css">
        <link rel="stylesheet" href="css/template/slicknav.min.css">
        <link rel="stylesheet" href="css/template/responsive.css">
        <link rel="stylesheet" href="css/template/main.css">
    <!-- BootstrapのCSS読み込み -->
    <link href="bootstrap-3.3.6-dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- BootstrapのJS読み込み -->
    <script src="bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
    <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-69511577-1', 'auto');
  ga('send', 'pageview');

</script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
</head>
<body>


<div id="header">
   
        <div id="header_left">
            <a href="index.php"><p><img src="img/logo-width.png" alt="refory" class="refory_logo_new"></p></a>
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

<div id="refory_img">
    <div id="copy_img">
        <img src="img/refory_top.png" class="logo_img">
    </div>
</div>
<div id="wrap">

    <!-- 新着記事の表示 -->
    <div class="container">
					<section class="features" id="features">
			<div class="container">
				<div class="row">
					<div class="col-md-12 col-sm-12 wow fadeIn">
						<h2>reforyとは・・・</h2>
					</div>
					<div class="col-md-3 col-sm-6 wow fadeInLeft">
						<div class="single-service">
							<p style="background:#EE3867;"><i class="fa fa-heart-o"></i></p>
							<h3>失敗経験の本当の価値</h3>
							<p>失敗はネガティブなもの？それは挑戦し成長している人ほど持っている。その人にしかない経験です。海外では積極的に「学び」としてシェアされるほど魅力的なものです。</p>
						</div>
					</div>
					<div class="col-md-3 col-sm-6 wow fadeInLeft">
						<div class="single-service">
							<p style="background:#3DB39D;"><i class="fa fa-pencil"></i></p>
							<h3>成功談との異なる魅力</h3>
							<p>成功談はモチベーションが上がります。しかし、失敗談には同じ轍を踏まないための反省＝「学び」がある。その明確なメッセージが失敗談の魅力です</p>
						</div>
					</div>
					<div class="col-md-3 col-sm-6 wow fadeInRight">
						<div class="single-service">
							<p style="background:#57C7ED;"><i class="fa fa-mobile"></i></p>
							<h3>シェアするメリット</h3>
							<p>コメントをもら →　振り返り、より深まる。失敗談を読むことで同じ轍を踏むことを予防できます。「まだ整理仕切れていない...」と思えてもシェアすることで前にも進めます</p>
						</div>
					</div>
					<div class="col-md-3 col-sm-6 wow fadeInRight">
						<div class="single-service">
							<p style="background:#324E5C;"><i class="fa fa-smile-o"></i></p>
							<h3>失敗から学び、つなげていく</h3>
							<p>書けば書くほど成長を記録でき、それが将来の若手にとって他のどこにもない「大きな価値」となります。reforyはその学びをつなげる場を目指しています</p>
						</div>
					</div>
				</div>
			</div>
		</section>
		
    
        
    </div>
    
</div>  
        

<div class = "footer">
    <div class="footer_left">
        <a href="inde.php"><img src="img/logo-width.png" alt="refory" class="refory_logo_new"></a>
    </div>
        <p class="copylight">2016 © refory.jp</p>
</div>


</body>
</html>