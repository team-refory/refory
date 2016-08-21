/*global $*/
$(document).ready(function(){
	
	//初期時のカウンターファイル読み込み
	//同じファイル名だと正常に読み込めないので、ランダムな数字を生成し、パラメータとして付けることで、ユニークな状態にしている。
	var randnum1 = Math.floor( Math.random() * 10000 );	
	$("#btn01 span").load("./count01.txt?r=" + randnum1);	
	
	
	//カウンター追加後の読み込み関数
	function func01(data){
		var randnum1 = Math.floor( Math.random() * 10000 );
		$("#btn01 span").load("./count01.txt?r=" + randnum1);
	}


	//カウンター＋1追加処理
	$('#btn01 p').click(function(e){
		//「param1」変数の値と書き込みファイル名の情報をもってpost.phpへ。その後、関数func01を実行
		$.post("post.php" , {"param1": 'count01.txt'} , func01);
		$("#btn01 span").load("./count01.txt");
	});

});