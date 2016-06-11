/*global $*/
// ドキュメントが読み込まれた際
$(document).ready(function(){
    // 送信ボタンを押したら sendComment() を実行
    $('#send-comment').click(function(){
        sendComment();
    });
    // 既存のコメントを読み込んで表示
    getComment();
});
// コメント送信（書き込み）
function sendComment() {
    // 未入力チェック
    if (!$('textarea[name=comment]').val()) {
        alert("感想を入力して下さい。");
        return false;
    }
    if(!$('.comment_user_id').val()) {
        alert("ログインをして下さい。");
        return false;
    }
    $.post(
        'comment_insert.php',
        {
            'task' : "comment_insert",
            'story_id' : $('.comment_story_id').val(),
            'content': $('.comment_insert_text').val()
        }
        ).success( 
            function(data) {
            // 書き込みが完了したら再度コメント一覧を読み込む
                getComment();
                $('.comment_insert_text').val(null);
            }
        ).error(
			function () {
				console.log( "ERROR" );
			}
		);
}
// コメント一覧受信・表示
function getComment() {
  $('.new_post_wrapper').css('display', 'table');
    $.post(
        'get_comments.php',
        {
            'story_id' : $('.comment_story_id').val()
        },
        function(data){
            // コメントリスト
            $('.comments_wrapper').html( data );
         comment_delete();
        }
    );
}

function comment_delete() {
	$('.delete').click( function(){
		$.post( "delete_comment.php",
			{
				'task' : "delete_comment",
				'commentId' : $('.comment_id').val()
			}
		).success(
			function ( data ) {
				console.log( "Response text : " + data );
				getComment();
			}
		).error(
			function () {
				console.log( "ERROR" );
			}
		);
    });
}
