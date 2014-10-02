/**************************************************
	
	Global
	
**************************************************/
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