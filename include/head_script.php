<?php
/**************************************LICENSE**************************************/
/*                                                                                 */
/* [GPL라이선스]                                                                     */
/* 투니툴은 GPL라이선스를 따릅니다.                                                */
/* 아래 Copyright는 라이선스 정책에 따라 절대 삭제 해서는 안됩니다.                */
/*                                                                                 */

echo "<!-------------
Powered by Toonytool Core
Copyright(C) Toonypaper(www.toonypaper.com) All Right Reserved.
본 웹서비스는 Toonytool Core로 개발 되었습니다.
------------->
";

/*                                                                                 */
/***********************************************************************************/
	
	//Index.php 거치지 않고 단독으로 Include되어 사용되는 경우를 위한 변수 초기화
	if(strstr($_SERVER['PHP_SELF'],"index.php")!=true){
		if(isset($_GET['m'])){
			$m = $_GET['m'];
		}else{
			$m = "";
		}
		if(isset($_GET['p'])){
			$p = $_GET['p'];
		}else{
			$p = "";
		}
		if(isset($_GET['saveViewType'])){
			$saveViewType = $_GET['saveViewType'];
		}else{
			$saveViewType = "";
		}
		if(isset($_GET['keepViewType'])){
			$keepViewType = $_GET['keepViewType'];
		}else{
			$keepViewType = "";
		}
		if(isset($_GET['viewType'])){
			$viewType = $_GET['viewType'];
		}else{
			$viewType = "p";
		}
		if(isset($viewType)&&$viewType=="p"){
			$viewDir = "";
		}else if(isset($viewType)){
			$viewDir = "m/";
		}
	}
	
	//Title
	echo "<title>".$site_config['ad_site_title']."</title>";
	//Meta
	echo "\n<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />";
	echo "\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=EDGE\" />";
	if($viewType=="m"){
		echo "\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, target-densitydpi=medium-dpi\" />";
	}
	//CSS
	if($site_config['ad_pavicon']!=""){ echo "\n<link rel=\"shortcut icon\" href=\"".__URL_PATH__."upload/siteInformations/".$site_config['ad_pavicon']."\" />"; }
	echo "\n<link href=\"".__URL_PATH__."smartEditor/css/smart_editor2_in.css\" rel=\"stylesheet\" type=\"text/css\" />";
	echo "\n<link href=\"".__URL_PATH__."library/css/jquery-ui.css\" rel=\"stylesheet\" type=\"text/css\" />";
	echo "\n<link href=\"".__URL_PATH__."library/css/{$viewDir}common.css\" rel=\"stylesheet\" type=\"text/css\" />";
	echo "\n<link href=\"".__URL_PATH__."library/css/{$viewDir}global.css\" rel=\"stylesheet\" type=\"text/css\" />";

	//레이아웃 스킨 CSS
	if($viewType=="p"){
		$layoutDir = "p/".$site_config['ad_site_layout']."/";
	}else{
		$layoutDir = "m/".$site_config['ad_msite_layout']."/";
	}
	echo "\n<link href=\"".__URL_PATH__."layoutskin/".$layoutDir."style.css\" rel=\"stylesheet\" type=\"text/css\" />";
	//모듈별 CSS
	for($i=0;$i<count($modulesDir);$i++){
		echo "\n<link href=\"".__URL_PATH__."modules/{$modulesDir[$i]}/library/css/{$viewDir}global.css\" rel=\"stylesheet\" type=\"text/css\" />";
	}
	//사용자 정의 CSS
	echo call_admin_design_bodyStyle("p");
	//JS
	echo "\n<script type=\"text/javascript\">__URL_PATH__ = \"".__URL_PATH__."\"; viewType = \"".$viewType."\"; viewDir = \"".$viewDir."\"; article = \"".$article."\"; m=\"".$m."\"; p=\"".$p."\";</script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."library/js/jquery-1.7.1.js\"></script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."library/js/jquery-ui.js\"></script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."library/js/ghost_html5.js\"></script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."library/js/jquery.form.js\"></script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."library/js/global.js\"></script>";
	//모듈별 JS
	for($i=0;$i<count($modulesDir);$i++){
		echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."modules/{$modulesDir[$i]}/library/js/{$viewDir}global.js\"></script>";
	}
	//설치 도메인과 접속 도메인이 다른 경우(Corss Domain) 설치 도메인으로 재접속
echo '
<script type="text/javascript">
	var ref_domain = "'."http://".$_SERVER['HTTP_HOST']."/".'";
	var ins_domain = "'.__URL_PATH__.'";
	if(ins_domain.indexOf(ref_domain)==-1){
		var href = "'.__URL_PATH__.'?'.$_SERVER['QUERY_STRING'].'";
		document.location.href = href;
	}
</script>
';
	//모바일 기기로 접속한 경우 모바일 페이지로 이동
	if($site_config['ad_use_msite']=="Y"&&$keepViewType!="true"){
echo '
<script type="text/javascript">
var viewType = "'.$viewType.'";
var __toony_saveViewType_cookie = getCookie("__toony_saveViewType_cookie");
if(__toony_saveViewType_cookie==""||__toony_saveViewType_cookie!="'.$viewType.'"){
	setCookie("__toony_saveViewType_cookie","'.$saveViewType.'",1);
	var saveViewType = "'.$saveViewType.'";
}else{
	var saveViewType = __toony_saveViewType_cookie;
}
var mobileKeyWords = new Array("iPhone", "iPod", "BlackBerry", "Android", "Windows CE", "LG", "MOT", "SAMSUNG", "SonyEricsson");
if(saveViewType!="p"&&viewType=="p"){
	for(var word in mobileKeyWords){
		if(navigator.userAgent.match(mobileKeyWords[word])!=null){
			window.document.location.href = __URL_PATH__+"m/";
		}
	}
}
if(saveViewType=="p"){
	$(document).ready(function(){
		$("footer").append("<div class=\"__viewFooterBtns\"><input type=\"button\" class=\"__button_small_gray __viewMobileModeBtn\" value=\"모바일 모드로 보기\" onClick=\"document.location.href=__URL_PATH__+\'m/?article=main\';\" /></div>");
	});
}
</script>
';
	}
?>