/*
삭제
*/
$(document).ready(function(){
	//비회원인 경우 비밀번호 입력폼
	$("#delete_password_form").submit(function(){
		if($.trim($("input[name='s_password']",this).val())==""){
			alert("비밀번호를 입력하세요.");
			$("input[name='s_password']",this).focus();
			return false;
		}else{
			var returnVar = false;
			$.ajax({
				type		:	"POST",
				cache		:	false,
				data		:	$("#read_password_form").serialize(),
				async		:	false,
				dataType	:	"HTML",
				success		:	function(msg){
									if(msg.indexOf("error:notPassword")!=-1){
										returnVar = false;
										alert("비밀번호가 일치하지 않습니다.");
									}else{
										returnVar = true;
									}
								}
			});
			return returnVar;
		}
	});
});
/*
읽기
*/
$(document).ready(function(){
	//비회원인 경우 패스워드 입력 폼
	$("#read_password_form").submit(function(){
		if($.trim($("input[name='s_password']",this).val())==""){
			alert("비밀번호를 입력하세요.");
			$("input[name='s_password']",this).focus();
			return false;
		}else{
			var returnVar = false;
			$.ajax({
				type		:	"POST",
				cache		:	false,
				data		:	$("#read_password_form").serialize(),
				async		:	false,
				dataType	:	"HTML",
				success		:	function(msg){
									if(msg.indexOf("<!--error:notPassword-->")!=-1){
										returnVar = false;
										alert("비밀번호가 일치하지 않습니다.");
									}else{
										returnVar = true;
									}
								}
			});
			return returnVar;
		}
	});
	//글 삭제
	$("#read_delete_btn").click(function(){
		if(confirm("이 글을 삭제 하시겠습니까?")==true){
			var article = $("#read_form input[name='article']").val();
			var category = $("#read_form input[name='category']").val();
			$("#read_form input[name='p']").val("delete");
			$("#read_form").attr({method:"POST",action:__URL_PATH__+"m/?article="+article+"&category="+category+"&p=delete"}).submit();
		}
	});
	//textarea 가로 폭 지정
	function textareaResize(obj){
		var boxWidth = obj.width();
		$("textarea",obj).css({
			"width":boxWidth-70+"px"
		});
	}
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
		$("._CALLING_COMMENT").load(__URL_PATH__+"modules/board/read.comment.inc.php?board_id="+board_id+"&read="+read+"&article="+article+"&viewDir=m/",function(){
			comment_replace_url();
		});
	}
	//댓글 작성
	$(document).on("submit","#read_comment_form",function(e){
		e.preventDefault();
		if($("#comment_modify_button").length>0){
			alert("댓글 수정 입력폼이 열려 있습니다."); return;
		}
		var returnVar = false;
		$.ajax({
			type		:	"POST",
			url			:	__URL_PATH__+"modules/board/read.comment.submit.php",
			cache		:	false,
			data		:	$("#read_comment_form").serialize(),
			async		:	false,
			dataType	:	"HTML",
			success		:	function(msg){
								switch(msg){
									case "<!--success::1-->" :
										read_comment_include();
										returnVar = true;
										break;
									default :
										alert(msg);
										returnVar = false;
										break;
								}
							}
		});
		return returnVar;
	});
	read_comment_include();
	//대댓글 작성
	$(document).on("click","#comment_reply_img",function(event){
		$("#read_comment_form input[name='mode']").val("1");
		//댓글수정 창이 열려있는 경우 닫음
		$("#read_comment_form .comment_modify").fadeIn({duration:200});
		$("#read_comment_form .comment_modify_box").remove();
		//대댓글 달기 창 show
		if($(".replyWriteArea",$(this).parent().parent().parent()).css("display")=="none"){
			$("#read_comment_form input[name='cidno']").val($(this).attr("name").substr(14));
			$("#read_comment_form .replyWriteArea").hide();									 
			$(".replyWriteArea",$(this).parent().parent().parent()).show();
			$(".replyWriteArea textarea",$(this).parent()).focus();
			textareaResize($(".replyWriteArea",$(this).parent()));
		}else{
			$("#read_comment_form .replyWriteArea").hide();	
		}
		
	});
	$(document).on("click","#comment_reply_button",function(e){
		e.preventDefault();
		$_this = $(this);
		var returnVar = false;
		$.ajax({
			type		:	"POST",
			url			:	__URL_PATH__+"modules/board/read.comment.submit.php",
			cache		:	false,
			data		:	{
								'mode':'11',
								'board_id':$("#read_comment_form input[name=board_id]").val(),
								'read':$("#read_comment_form input[name=read]").val(),
								'cidno':$("#read_comment_form input[name=cidno]").val(),
								'type':$("#read_comment_form input[name=type]").val(),
								'comment':$("textarea[name=reply_comment]",$_this.parent()).val(),
								'writer':$("input[name=reply_writer]",$_this.parent()).val()
							},
			async		:	false,
			dataType	:	"HTML",
			success		:	function(msg){
								switch(msg){
									case "<!--success::1-->" :
										read_comment_include();
										returnVar = true;
										break;
									default :
										alert(msg);
										returnVar = false;
										break;
								}
							}
		});
		return returnVar;
	});
	//댓글 삭제
	$(document).on("click","#comment_del_img",function(event){
		var cidnoVar = $(this).attr("name");
		if(confirm("댓글을 삭제 하시겠습니까?")==true){
			$.ajax({
				type		:	"POST",
				url			:	__URL_PATH__+"modules/board/read.comment.submit.php",
				cache		:	false,
				data		:	{
									board_id:$("#read_comment_form input[name='board_id']").val(),
									read:$("#read_comment_form input[name='read']").val(),
									mode:"3",
									cidno:cidnoVar
								},
				async		:	false,
				dataType	:	"HTML",
				success		:	function(msg){
									switch(msg){
										case "<!--success::1-->" :
											read_comment_include();
											break;
										default :
											alert(msg);
											break;
									}
								}
			});
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
						textareaResize($(".comment_modify"));
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
		$.ajax({
			type		:	"POST",
			url			:	__URL_PATH__+"modules/board/read.comment.submit.php",
			cache		:	false,
			data		:	$("#read_comment_form").serialize(),
			async		:	false,
			dataType	:	"HTML",
			success		:	function(msg){
								switch(msg){
									case "<!--success::1-->" :
										read_comment_include();
										break;
									default :
										alert(msg);
										break;
								}
							}
		});
	});
	//추천/비추천
	$(document).on("click","._read_likesArea ._likes_btn",function(e){
		e.preventDefault();
		$.ajax({
			type		:	"POST",
			url			:	__URL_PATH__+"modules/board/read.likes.submit.php",
			cache		:	false,
			data		:	{
								'board_id':$("#read_form input[name=board_id]").val(),
								'read_idno':$("#read_form input[name=read]").val(),
								'mode':'likes'
							},
			async		:	false,
			dataType	:	"HTML",
			success		:	function(msg){
								switch(msg){
									case "<!--error::have_likes-->" :
										alert("이미 참여 하였습니다.");
										break;
									case "<!--error::not_permissions-->" :
										alert("추천/비추천 권한이 없습니다.");
										break;
									default :
										$("._read_likesArea ._likes_btn .__count").text(msg);
								}
							}
		});
	});
	$(document).on("click","._read_likesArea ._unlikes_btn",function(e){
		e.preventDefault();
		$.ajax({
			type		:	"POST",
			url			:	__URL_PATH__+"modules/board/read.likes.submit.php",
			cache		:	false,
			data		:	{
								'board_id':$("#read_form input[name=board_id]").val(),
								'read_idno':$("#read_form input[name=read]").val(),
								'mode':'unlikes'
							},
			async		:	false,
			dataType	:	"HTML",
			success		:	function(msg){
								switch(msg){
									case "<!--error::have_likes-->" :
										alert("이미 참여 하였습니다.");
										break;
									case "<!--error::not_permissions-->" :
										alert("추천/비추천 권한이 없습니다.");
										break;
									default :
										$("._read_likesArea ._unlikes_btn .__count").text(msg);
								}
								
							}
		});
	});
});
//본문에 이미지가 있는 경우 width값 조정, 새창 링크 연결
$(window).load(function(){
	if($("#board_memo_area img").length>0){
		$("#board_memo_area img").each(function(){
			//width값 조정
			if(parseInt($(this).width())>=parseInt($("#board_memo_area").width())){
				$(this).css({
					"width":"100%",
					"cursor":"pointer"
				});
			}
			//새창 링크 연결
			$(this).attr({
				"onclick":"window.open('"+$(this).attr('src')+"');",
				"title":"원본 사진 보기"
			});
		});
	}
});
	
/*
작성
*/
$(document).ready(function(){
	//패스워드 입력 폼
	$("#modify_password_form").submit(function(){
		if($.trim($("input[name='s_password']",this).val())==""){
			alert("비밀번호를 입력하세요.");
			$("input[name='s_password']",this).focus();
			return false;
		}else{
			var returnVar = false;
			$.ajax({
				type		:	"POST",
				cache		:	false,
				data		:	$("#modify_password_form").serialize(),
				async		:	false,
				dataType	:	"HTML",
				success		:	function(msg){
									if(msg.indexOf("error:notPassword")!=-1){
										returnVar = false;
										alert("비밀번호가 일치하지 않습니다.");
									}else{
										returnVar = true;
									}
								}
			});
			return returnVar;
		}
	});
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
	$("#write_form .write_submit_btn").click(function(e){
		var $btn_org = $(this);
		var btn_org_value = $(this).val();
		$(this).attr("disabled",true).val("등록중...");
		$("#write_form").attr({method:"POST",action:__URL_PATH__+"modules/board/write.submit.php"});
		$("#write_form").ajaxForm({
			type		:	"POST",
			dataType	:	"HTML",
			success		:	function(msg){
								if(msg=="error::spam_replace"){
									alert("스팸방지코드가 올바르지 않습니다.");
									$("#zsfImg").attr("src",__URL_PATH__+"capcha/zmSpamFree.php?re&zsfimg="+new Date().getTime());	
									$("input[name=capcha]").val("").focus();
								}else if(msg.substr(0,7)=="Success"){
									msg = msg.replace(/&amp;/g,"&");
									window.location.href=msg.substr(7);
								}else{
									alert(msg);
									$btn_org.attr("disabled",false).val(btn_org_value);
								}
							}
		});
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
		var selected = encodeURI($("option:selected",$(this)).val());
		var article = $("#array_form input[name=article]").val();
		if(selected=="all"){
			document.location.href = __URL_PATH__+"m/?article="+article;
		}else{
			document.location.href = __URL_PATH__+"m/?article="+article+"&category="+selected;
		}
	});
	//비밀글을 클릭시
	$(".bbs_subject_title").click(function(){
		var href=$(this).attr("href")
		var returnVar = false;
		$.ajax({
			type		:	"GET",
			url			:	href,
			cache		:	false,
			async		:	false,
			dataType	:	"HTML",
			success		:	function(msg){
								if(msg.indexOf("<!--error:notSecret-->")!=-1){
									div_alert("error","비밀글을 읽을 권한이 없습니다.");
									returnVar = false;
								}else{
									returnVar = true;
								}
							}
		});
		return returnVar;
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