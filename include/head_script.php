<?php
	echo "<title>".$site_config['ad_site_title']."</title>";
	//Meta
	echo "\n<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />";
	echo "\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=EDGE\" />";
	if($viewType=="m"){
		echo "\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, target-densitydpi=medium-dpi\" />";
	}
	//CSS
	if($site_config['ad_pavicon']!=""){ echo "\n<link rel=\"shortcut icon\" href=\"".__URL_PATH__."upload/siteInformations/".$site_config['ad_pavicon']."\" />"; }
	echo "\n<link href=\"".__URL_PATH__."library/css/jquery-ui.css\" rel=\"stylesheet\" type=\"text/css\" />";
	echo "\n<link href=\"".__URL_PATH__."library/css/{$viewDir}common.css\" rel=\"stylesheet\" type=\"text/css\" />";
	echo "\n<link href=\"".__URL_PATH__."library/css/{$viewDir}global.css\" rel=\"stylesheet\" type=\"text/css\" />";
	//모듈별 CSS
	if($m){
		echo "\n<link href=\"".__URL_PATH__."modules/{$m}/library/css/{$viewDir}global.css\" rel=\"stylesheet\" type=\"text/css\" />";
	}
	//사용자 정의 CSS
	echo call_admin_design_bodyStyle("p");
	//JS
	echo "\n<script type=\"text/javascript\">__URL_PATH__ = \"".__URL_PATH__."\";</script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."library/js/jquery-1.7.1.js\"></script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."library/js/jquery-ui.js\"></script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."library/js/ghost_html5.js\"></script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."library/js/jquery.form.js\"></script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."library/js/global.js\"></script>";
	//모듈별 JS
	if($m){
		echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."modules/{$m}/library/js/{$viewDir}global.js\"></script>";
	}
	//모바일 접속을 해도 유효하지 않은 페이지 선언
	$dont_msite = array(
		"account.idCheck"=>TRUE
	);
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
	if($site_config['ad_use_msite']=="Y"&&!array_key_exists($p,$dont_msite)){
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