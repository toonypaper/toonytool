/*
글읽기
*/
//댓글 내에서 url 문자열을 자동링크
function comment_replace_url(){
	var url_patt = /((http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\wㄱ-ㅎㅏ-ㅣ가-힣\;\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?)/gi;
	var $comment_modify = $("#read_comment_form .comment_list .memo .comment_modify");
	$comment_modify.each(function(){
		//.comment_modify DIV의 name attribute와 내용을 새로 생성한 .comment_modify_org DIV에 상속시킴
		$(this).after("<div class='comment_modify_org' style='display:none;' name='"+$(this).attr("name")+"'>"+$(this).html()+"</div>").attr("name","");
		//url문자열을 찾아 자동 링크를 걸어줌
		var match_url = String($(this).html().match(url_patt));
		var match_url2 = match_url.split(",");
		var replace_url = $(this).html();
		for(i=1;i<=match_url2.length;i++){
			replace_url = replace_url.replace(match_url2[i-1],"<a href='"+match_url2[i-1]+"' target='_blank'>"+match_url2[i-1]+"</a>");
		}
		$(this).html(replace_url);
	});
}
//댓글 박스 AJAX 로드
function read_comment_include(){
	var board_id = $("#read_form input[name='board_id']").val();
	var read = $("#read_form input[name='read']").val();
	var article = $("#read_form input[name='article']").val();
	$("._CALLING_COMMENT").load(__URL_PATH__+"modules/board/read.comment.inc.php?article="+article+"&board_id="+board_id+"&read="+read+"&article="+article+"&viewDir=m/&viewType=m",function(){
		comment_replace_url();
	});
}
$(window).load(function(){
	read_comment_include();
	//글 삭제
	$("#read_delete_btn").click(function(){
		if(confirm("이 글을 삭제 하시겠습니까?")==true){
			var article = $("#read_form input[name='article']").val();
			var category = $("#read_form input[name='category']").val();
			$("#read_form input[name='p']").val("delete");
			$("#read_form").attr({method:"POST",action:__URL_PATH__+"m/?article="+article+"&category="+category+"&p=delete"}).submit();
		}
	});
	//댓글 작성
	$(document).on("click","#read_comment_form ._submitBtn",function(e){
		e.preventDefault();
		var now_cidno = $("#read_comment_form input[name=cidno]").val();
		$("#read_comment_form input[name=mode]").val("1");
		$("#read_comment_form input[name=cidno]").val("");
		if($("#comment_modify_button").length>0){
			alert("댓글 수정 입력폼이 열려 있습니다.");
			$("#read_comment_form input[name=mode]").val("2");
			$("#read_comment_form input[name=cidno]").val(now_cidno);
			return;
		}
		$("#read_comment_form").submit();
	});
	//대댓글 작성
	$(document).on("click","#comment_reply_img",function(e){
		$("#read_comment_form input[name='mode']").val("1");
		//댓글수정 창이 열려있는 경우 닫음
		$("#read_comment_form .comment_modify").fadeIn({duration:200});
		$("#read_comment_form .comment_modify_box").remove();
		//대댓글 달기 창 show
		var $replyWriteForm = $(".replyWriteArea",$(this).parent().parent().parent());
		if($replyWriteForm.css("display")=="none"){
			$("#read_comment_form input[name='cidno']").val($(this).attr("name").substr(14));
			$("#read_comment_form .replyWriteArea").hide();									 
			$replyWriteForm.show();
			$("textarea",$replyWriteForm).focus();
		}else{
			$("#read_comment_form .replyWriteArea").hide();	
		}
		
	});
	$(document).on("click","#comment_reply_button",function(e){
		e.preventDefault();
		$("#read_comment_form input[name=mode]").val("11");
		$("#read_comment_form input[name=reply_writer_o]").val($("input[name=reply_writer]",$(this).parent()).val());
		$("#read_comment_form textarea[name=reply_comment_o]").val($("textarea[name=reply_comment]",$(this).parent()).val());
		$("#read_comment_form").submit();
	});
	//댓글 삭제
	$(document).on("click","#comment_del_img",function(event){
		if(confirm("댓글을 삭제 하시겠습니까?")==true){
			$("#read_comment_form input[name=mode]").val("3");
			$("#read_comment_form input[name=cidno]").val($(this).attr("name"));
			$("#read_comment_form").submit();
		}
	});
	
	//댓글 수정
	$(document).on("click","#comment_modify_img",function(event){
		if(event.target==this){
			//대댓글 창이 열려 있는 경우 닫음
			$("#read_comment_form .replyWriteArea").hide();
			//수정창을 show
			$imgVar = $(this);
			$("#read_comment_form .comment_modify_org").each(function(index){
				if($(this).attr("name")==$imgVar.attr("name")){
					if($(this).parent().children(".comment_modify").css("display")!="none"){
						$(this).parent().children(".comment_modify").hide();
						$(this).after("<div class=\"comment_modify_box comment_modify_div_"+index+" comment_form_box\"><textarea name=\"comment_modify\" class=\"comment_modify_textarea_"+index+"\" style=\"height:50px;\">"+$(this).html().replace(/<BR>\n/gi,"\n").replace(/<BR>/gi,"\n")+"</textarea> <input type=\"button\" id=\"comment_modify_button\" class=\"__button_small\" value=\"수정\"></div>")
						.next().hide().fadeIn({duration:200});
						$("#read_comment_form .comment_modify_textarea_"+index).focus();
						$("#read_comment_form input[name='mode']").val("2");
						$("#read_comment_form input[name='cidno']").val($(this).attr("name").substr(15));
					}else{
						$(this).parent().children(".comment_modify").fadeIn({duration:200});
						$("#read_comment_form .comment_modify_div_"+index).remove();
						$("#read_comment_form input[name='mode']").val("1");
					}
				}else{
					$(this).parent().children(".comment_modify").fadeIn({duration:200});
					$("#read_comment_form .comment_modify_div_"+index).remove();
				}
		   });
		}
	});
	$(document).on("click","#comment_modify_button",function(event){
		$("#read_comment_form").submit();
	});
	
	//추천/비추천
	$(document).on("click","._read_likesArea ._likes_btn",function(e){
		e.preventDefault();
		$form = $("form[name=read_likeAreaForm]");
		$("input[name=mode]",$form).val("likes");
		$form.submit();
		
	});
	$(document).on("click","._read_likesArea ._unlikes_btn",function(e){
		e.preventDefault();
		$form = $("form[name=read_likeAreaForm]");
		$("input[name=mode]",$form).val("unlikes");
		$form.submit();
	});
});
//본문에 이미지가 있는 경우 width값 조정, 새창 링크 연결
$(window).load(function(){
	if($("#board_memo_area img").length>0){
		$("#board_memo_area img").each(function(){
			//width값 조정
			if(parseInt($(this).width())>articleIMG_width){
				$(this).css({
					"width":articleIMG_width,
					"cursor":"pointer"
				});
			}
			//height값 조정
			if(parseInt($(this).height())>articleIMG_height){
				$(this).css({
					"height":articleIMG_height,
					"cursor":"pointer"
				});
			}
			//새창 링크 연결
			$(this).attr({
				"onclick":"window.open('"+$(this).attr('src')+"');",
				"title":"원본 사진 보기"
			}).css({
				"cursor":"pointer"
			});
		});
	}
});
	
/*
작성
*/
$(document).ready(function(){
	//공지사항 옵션 체크시 답변알림 옵션 숨김
	function use_notice_label(){
		if($("input[name=use_notice]").is(":checked")==true){
			$("._use_secret_label").hide();
		}else{
			$("._use_secret_label").show();
		}	
	}
	$("input[name=use_notice]").click(use_notice_label);
	use_notice_label();
	//글 작성
	$("#write_form").keypress(function(e){
		if($("textarea[name=ment]").is(":focus")!=true&&e.keyCode==13){
			return false;
		}else{
			return true;
		}
	});
	$(".write_submit_btn",$("#write_form")).click(function(e){
		$("#write_form").submit();
	});
	//공지글 작성 옵션을 체크하는 경우 카테고리 selectbox 숨김
	function category_select_display(){
		var checked = $("#use_notice").is(":checked");
		var $category_select_noticeTxt = "<span class=\"_category_select_noticeTxt\">공지글은 모든 카테고리에 노출됩니다.</span>";
		if(checked==true){
			$("._category_select")
			.after($category_select_noticeTxt)
			.hide();
		}else{
			$("._category_select_noticeTxt").remove();
			$("._category_select").show();
		}
	}
	category_select_display();
	$("#use_notice").click(category_select_display);
});
/*
리스트
*/
$(document).ready(function(){
	//카테고리를 선택한 경우
	$("#array_form select[name=category]").change(function(){
		var selected = encodeURIComponent($("option:selected",$(this)).val());
		var article = $("#array_form input[name=article]").val();
		if(selected=="all"){
			document.location.href = __URL_PATH__+"m/?article="+article;
		}else{
			document.location.href = __URL_PATH__+"m/?article="+article+"&category="+selected;
		}
	});
	//검색 버튼을 클릭시
	$("#array_form").submit(function(){
		if($.trim($("input[name='keyword']",this).val())==""){
			alert("검색어를 입력하세요.");
			$("input[name='keyword']",this).focus();
			return false;
		}
		$(this).attr({method:"GET",action:js_url_path+"array.php"});
	});
});