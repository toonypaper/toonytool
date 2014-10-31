/**************************************************
	
	Global
	
**************************************************/
/*
AJAX Form Validator
*/
function validt_error($form,msg){
	msg = msg.replace("<!--","");
	msg = msg.replace("-->","");
	msg = msg.split("|");
	msg[0] = msg[0].replace("error::","");
	msg[1] = msg[1].replace("msg::","");
	$input = $("*[name="+msg[0]+"]",$form);
	var input_title = $input.attr("title");
	if($.trim(msg[1])=="NULL_ERROR"){
		alert(input_title+" : 입력해 주세요.");
	}else if($.trim(msg[1])=="NOT_CAPCHA"){
		alert("스팸방지 코드가 올바르지 않습니다.");
		$("#zsfImg").attr("src",__URL_PATH__+"capcha/zmSpamFree.php?re&zsfimg="+new Date().getTime());	
	}else if($.trim(msg[1])!=""){
		alert(msg[1]);
	}else{
		alert(input_title+" : 올바르게 입력해 주세요.");
	}
	$input.focus();
}
function validt_success($form,msg){
	msg = msg.replace("<!--","");
	msg = msg.replace("-->","");
	msg = msg.split("|");
	if(msg[0].indexOf("success::")!=-1){
		msg[0] = msg[0].replace("success::","");
		msg[1] = msg[1].replace("location::","");
		if($.trim(msg[0])!=""){
			alert(msg[0]);
		}
		if(msg[1].indexOf("window.close&&opener.href=")!=-1){
			msg[1] = msg[1].replace("window.close&&opener.href=","");
			opener.document.location.href = __URL_PATH__+msg[1];
			window.close();
		}else{
			switch(msg[1]){
				case "window.close" :
					window.close();
					break;
				case "window.close&&opener.reload" :
					opener.document.location.reload();
					window.close();
					break;
				case "window.document.location.reload" :
					window.document.location.reload();
					break;
				case "" :
					return;
					break;
				default :
					window.document.location.href = __URL_PATH__+msg[1];
			}
		}
	}
	if(msg[0].indexOf("success_returnStr::")!=-1){
		msg[0] = msg[0].replace("success_returnStr::","");
		msg[1] = msg[1].replace("msg::","");
		$(msg[0]).html(msg[1]);
	}
	if(msg[0].indexOf("success_returnFunction::")!=-1){
		msg[0] = msg[0].replace("success_returnFunction::","");
		eval(msg[0]);
	}
}
function validt_returnAjax($form,msg){
		msg = msg.replace("<!--","");
		msg = msg.replace("-->","");
		msg = msg.split("|");
		msg[0] = msg[0].replace("returnAjax::","");
		msg[1] = msg[1].replace("ajaxDoc::","");
		alert(msg[0]);
		$.ajax({
			type		:	"POST",
			url			:	__URL_PATH__+msg[1],
			cache		:	false,
			data		:	$form.serialize(),
			async		:	false,
			dataType	:	"HTML",
			success		:	function(msg){
								if(msg.indexOf("error::")!=-1){
									validt_error($form,msg);
								}else if(msg.indexOf("success::")!=-1){
									validt_success($form,msg);
								}else{
									alert("일시적인 오류입니다.");
									alert(msg);
								}
							}
		});
}

//첨부파일이 포함된 AJAX Submit을 시작함
function ajaxFormSubmit($form){
	var ajaxDefault = $form.attr("ajaxFormSubmit");
	var ajaxAction = $form.attr("ajaxAction");
	ajaxFormSubmit_val = true;
	$form.attr("action",__URL_PATH__+ajaxAction);
	$form.ajaxForm({
		cache		:	false,
		async		:	false,
		type		:	"POST",
		dataType	:	"HTML",
		beforeSend	:	function(){
							$("*[ajaxSubmit],input[type=button]",$form).attr("disabled",true);
						},
		success		:	function(msg){
							if(msg.indexOf("error::")!=-1){
								validt_error($form,msg);
							}else if(msg.indexOf("success::")!=-1){
								validt_success($form,msg);
							}else if(msg.indexOf("success_returnStr::")!=-1){
								validt_success($form,msg);
							}else if(msg.indexOf("success_returnFunction::")!=-1){
								validt_success($form,msg);
							}else if(msg.indexOf("returnAjax::")!=-1){
								validt_returnAjax($form,msg);
							}else{
								alert("일시적인 오류입니다.");
								alert(msg);
							}
							$("*[ajaxSubmit],input[type=button]",$form).attr("disabled",false);
						}
	});
	$form.submit();
}
//첨부파일이 포함되지 않은 AJAX Submit을 시작함
function ajaxSubmit($form){
	var ajaxAction = $form.attr("ajaxAction");
	var ajaxType = $form.attr("ajaxType");
	$.ajax({
		type		:	"POST",
		url			:	__URL_PATH__+ajaxAction,
		cache		:	false,
		data		:	$form.serialize(),
		async		:	false,
		dataType	:	"HTML",
		beforeSend	:	function(){
							$("*[ajaxSubmit],input[type=button]",$form).attr("disabled",true);
						},
		success		:	function(msg){
							if(msg.indexOf("error::")!=-1){
								validt_error($form,msg);
							}else if(msg.indexOf("success::")!=-1){
								validt_success($form,msg);
							}else if(msg.indexOf("success_returnStr::")!=-1){
								validt_success($form,msg);
							}else if(msg.indexOf("success_returnFunction::")!=-1){
								validt_success($form,msg);
							}else if(msg.indexOf("returnAjax::")!=-1){
								validt_returnAjax($form,msg);
							}else{
								alert("일시적인 오류입니다.");
								alert(msg);
							}
							$("*[ajaxSubmit],input[type=button]",$form).attr("disabled",false);
						}
	});
}
//AJAX, AJAX Form Submit시 처리
$(document).ready(function(){
	$(document).on("submit","*[ajaxAction]",function(e){
		var ajaxType = $(this).attr("ajaxType");
		if(ajaxType=="multipart"&&ajaxFormSubmit_val!=true){
			e.preventDefault();
			ajaxFormSubmit($(this));
		}
		if(ajaxType=="html"){
			e.preventDefault();
			ajaxSubmit($(this));
		}
	});
	$(document).on("click","*[ajaxSubmit]",function(e){
		e.preventDefault();
		var ajaxDefault = $(this).attr("ajaxSubmit");
		$("form[name="+ajaxDefault+"]").submit();
	});
	ajaxFormSubmit_val = false;
	$(document).on("click","*[ajaxFormSubmit]",function(e){
		ajaxFormSubmit($("*[name="+$(this).attr("ajaxFormSubmit")+"]"));
	});
});

/*
'allCheck' 속성이 있는 요소를 클릭하는 경우 클릭시 'cnum[]' 이름 가진 체크박스 모두 선택
*/
$(document).ready(function(){
	$("*[allCheck]").click(function(e){
		e.preventDefault();
		var $form = $("*[name="+$(this).attr("allCheck")+"]");
		$("input[name='cnum[]']",$form).each(function(){
			if($(this).is(":checked")==false){
				$(this).attr({checked:true});
			}else{
				$(this).attr({checked:false});
			}
		});
	});
});

/*
'at'속성이 있는 링크를 클릭한 경우 'at'에 지정된 엘리먼트에
링크 URL을 AJAX 로드한다.
*/
$(document).on("click","a[atEle]",function(e){
	e.preventDefault();
	var atUrl = $(this).attr("atUrl");
	var atEle = $(this).attr("atEle");
	$(atEle).load(atUrl);
});
/*
세션쿠키 컨트롤러
*/
//쿠키를 굽는다.
function setCookie( name, value, expiredays ) { 
	var todayDate = new Date(); 
	if (expiredays == null){
		expiredays = 30;
	}
	// Cookie Save Timeout (1Day = 1) 
	todayDate.setDate( todayDate.getDate() + expiredays ); 
	document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";" 
}
//쿠키를 가져온다.
function getCookie( name ){ 
	var nameOfCookie = name + "="; 
	var x = 0; 
	while ( x <= document.cookie.length ){ 
		var y = (x+nameOfCookie.length); 
		if ( document.cookie.substring( x, y ) == nameOfCookie ) { 
		if ( (endOfCookie=document.cookie.indexOf( ";", y )) == -1 ) 
		endOfCookie = document.cookie.length; 
		return unescape( document.cookie.substring( y, endOfCookie ) ); 
	} 
	x = document.cookie.indexOf( " ", x ) + 1; 
	if ( x == 0 ) 
	break; 
	} 
	return ""; 
}
/*
AJAX 통신 시작하는 처리
*/
$(document).ajaxStart(function(){
	//중복 클릭 방지를 위한 DIV Cover 띄움
	$body_div_cover = $("<div id='body_div_cover'></div>");
	$body_div_cover.css({
		"background-color":"#ffffff",
		"opacity":"0.4",
		"position":"fixed",
		"width":"100%",
		"height":"100%",
		"z-index":"999",
		"top":"0",
		"left":"0"
	}).appendTo("body");
	$body_loading = $("<img src='"+__URL_PATH__+"images/loading.gif' />");
	$body_loading.css({
		"position":"fixed",
		"margin-top":"20px",
		"margin-left":"20px",
		"z-index":"999",
		"top":"10px",
		"left":"10px"
	}).appendTo("body");
})
.ajaxSuccess(function(){
	$body_div_cover.remove();
	$body_loading.remove();
});
/*
jQuery-ui Datepicker 설정
*/
$(document).ready(function(){
	$.datepicker.regional['ko'] = {
		closeText: '닫기',
		prevText: '이전달',
		nextText: '다음달',
		currentText: '오늘',
		monthNames: ['1월(JAN)','2월(FEB)','3월(MAR)','4월(APR)','5월(MAY)','6월(JUN)',
					'7월(JUL)','8월(AUG)','9월(SEP)','10월(OCT)','11월(NOV)','12월(DEC)'],
		monthNamesShort: ['1월','2월','3월','4월','5월','6월',
					'7월','8월','9월','10월','11월','12월'],
		dayNames: ['일','월','화','수','목','금','토'],
		dayNamesShort: ['일','월','화','수','목','금','토'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		weekHeader: 'Wk',
		dateFormat: 'yy-mm-dd',
		firstDay: 0,
		isRTL: false,
		showMonthAfterYear: true,
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['ko']);
});
/*
스마트에디터 이미지 컨트롤
*/
smartEditor_submit_val = false;
//에디터가 실행되는 경우 삽입 이미지 기록용 textarea 생성, 본문내 이미지 기록
function smartEditor_create_recTextarea(){
	$("body").append('<textarea name="smart_editor2_attach_images" id="smart_editor2_attach_images" style="display:none;"></textarea>'); //기존 삽입된 이미지까지 기록
	$("body").append('<textarea name="smart_editor2_attach_images_new" id="smart_editor2_attach_images_new" style="display:none;"></textarea>'); //새로 삽입된 이미지만 기록
	/*
	//본문에서 기존 삽입된 이미지 추출
	var article_html = $("textarea[smarteditor]").val();
	var article_images = article_html.match(/smartEditor\/[a-zA-Z0-9-_\.]+.(jpg|gif|png|bmp)/gi);
	if(article_images){
		for(var i=0;i<article_images.length;i++){
			var images_rec = $("#smart_editor2_attach_images").val();
			$("#smart_editor2_attach_images").val(images_rec+","+article_images[i].replace("smartEditor/",""));
		}
	}
	*/
}
//본문에 이미지를 삽입하는 경우 Hidden Input에 기록
function smartEditor_insert_image(file){
	//기존 이미지까지 포함하여 기록
	var images_rec = $("#smart_editor2_attach_images").val();
	$("#smart_editor2_attach_images").val(images_rec+","+file);
	//새로 추가한 이미지만 따로 기록
	var images_new_rec = $("#smart_editor2_attach_images_new").val();
	$("#smart_editor2_attach_images_new").val(images_new_rec+","+file);
}
//페이지를 벗어날 때 본문에 삽입했던 이미지가 삭제되었는지 검사 후 처리
function smartEditor_remove_image(){
	var $textarea = $("textarea[smarteditor]");
	var textarea_id = $("textarea[smarteditor]").attr("id");
	if($("#smart_editor2_attach_images").length>0){
		oEditors.getById[textarea_id].exec("UPDATE_CONTENTS_FIELD", []);
		var article_html = $textarea.val();
		var images_rec = $("#smart_editor2_attach_images").val();
		var images_rec_new = $("#smart_editor2_attach_images_new").val();
		//article_html 변수의 값에서 삽입된 이미지 파일명만 추출
		var article_images = article_html.match(/smartEditor\/[a-zA-Z0-9-_\.]+.(jpg|gif|png|bmp)/gi);
		var images = "";
		if(article_images){
			for(var i=0;i<article_images.length;i++){
				images += ","+article_images[i].replace("smartEditor/",""); //배열을 하나의 문자열로 합침
			}
		}else{
			images = "NULL"
		}
		//수정 버튼을 클릭시 : images_rec 에는 있지만, article_html 에는 없는 파일명인 경우 서버에서 삭제(AJAX)
		if(smartEditor_submit_val==true){
			images_rec = images_rec.split(",");
			for(var i=1;i<images_rec.length;i++){
				if(images.indexOf(images_rec[i])==-1){
					$.ajax({
						type		:	"POST",
						url			:	__URL_PATH__+"smartEditor/smartEditor_remove_imgs.php",
						cache		:	false,
						data		:	{ "file":images_rec[i] },
						async		:	false,
						dataType	:	"HTML"
					});
				}
			}
		//submit 없이 그냥 페이지를 벗어날 때 : 새로 삽입한 모든 이미지를 삭제
		}else if(smartEditor_submit_val==false&&images_rec_new!=""){
			images_rec_new = images_rec_new.split(",");
			for(var i=1;i<images_rec_new.length;i++){
				$.ajax({
					type		:	"POST",
					url			:	__URL_PATH__+"smartEditor/smartEditor_remove_imgs.php",
					cache		:	false,
					data		:	{ "file":images_rec_new[i] },
					async		:	false,
					dataType	:	"HTML"
				});
			}
		}
	}
}