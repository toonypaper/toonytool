<?php
	/*
	모바일 버전으로 출력함
	*/
	$viewType = "m";
	$viewDir = "m/";
	
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	include __DIR_PATH__."include/outModules.inc.php";
	
	/*
	엔진이 설치되어 있는지 검사
	*/
	if(!is_file("../include/mysql.info.php")||!is_file("../include/path.info.php")||!defined('__HOST__')||!defined('__DB_NAME__')||!defined('__DB_USER__')||!defined('__DB_PASS__')||!$mysql->is_table("toony_admin_siteconfig")||strstr("http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'],__URL_PATH__)===FALSE||strstr(str_replace("\\","/",realpath(__FILE__)),__DIR_PATH__)===FALSE){
		echo '<script type="text/javascript">document.location.href = "../install/index.php";</script>'; exit;
	}
	
	$lib = new libraryClass();
	$method = new methodController();
	$innerCont = new skinController();
	$subpage = new skinController();
	$mysql = new mysqlConnection();
	
	$method->method_param("GET","article,m,p,saveViewType");
	
	/*
	모바일을 사용하지 않는 경우 PC페이지로 이동
	*/
	if($site_config['ad_use_msite']=="N"){
		$lib->error_location($site_config['ad_site_url'],"A");
	}
	
	/*
	검사
	*/
	if(!$article&&($m||$p)){
		$lib->error_alert_location("올바르지 않은 접근 입니다.",$site_config['ad_msite_url'],"A");
	}
	if(!$article){
		$article = "main";
	}
	
	/*
	메뉴 타입이 포워딩인 경우 포워딩 대상 메뉴로 article 변환
	*/
	$mysql->select("
		SELECT href,forward
		FROM toony_admin_menuInfo
		WHERE callName='$article' AND drop_regdate IS NULL AND vtype='m'
	");
	if($mysql->fetch("href")=="fm"){
		$article = $mysql->fetch("forward");
	}
	
	/*
	article로 DB를 색인하여 페이지 링크,링크문서 정보를 로드함
	*/
	$mysql->select("
		SELECT *
		FROM toony_admin_menuInfo
		WHERE callName='$article' AND drop_regdate IS NULL AND vtype='m'
	");
	$mysql->fetchArray("link,linkDoc,class,href,depth,parent");
	$menuInfo = $mysql->array;
	
	/*
	메뉴 타입이 수동 문서 연결인 경우
	*/
	if($menuInfo['href']=="mp"){
		$p = $lib->htmldecode($menuInfo['linkDoc']);
		
	/*
	메뉴 타입이 페이지&모듈 연결인 경우, URI형식의 문자열을 GET변수화 처리
	*/
	}else{
		$parseUrl = parse_url($lib->htmldecode($menuInfo['link']));
		$parseStr = $parseUrl['query'];
		parse_str($parseStr,$arrs);
		foreach($arrs as $val=>$key){
			global $$val;
			$$val = $key;
		}
	}
	
	/*
	주소표시줄에 수동으로 입력한 $m 혹은 $p 값이 있는 경우,
	현재 메뉴를 Active시킨 채 내용 영역에 수동 페이지를 호출하기 위한 설정
	*/
	if(isset($_GET['p'])){
		$p = $_GET['p'];
	}
	if(isset($_GET['m'])){
		$m = $_GET['m'];
	}
	
	/*
	URL 분석
	*/
	if($m!=""&&$m!="page"){
		$incDir = __DIR_PATH__."modules/{$m}/";
	}else{
		$incDir = __DIR_PATH__;
	}
	if(!$p){
		$p = "index";
	}
	
	/*
	모든 모듈의 global.php 를 인클루드
	*/
	$globalEnginePath = opendir(__DIR_PATH__."modules/");
	while($dir = readdir($globalEnginePath)){
		if(($dir!="."&&$dir!="..")){
			include_once __DIR_PATH__."modules/".$dir."/include/global.php";
		}
	}
	
	/*
	페이지 호출
	*/
	$loadFile = $incDir.$p.".php";
	$defFile = "main.php";
	
	/*
	모듈 호출 값이 있는 경우
	*/
	if($m){
		if($m=="page"){
			//페이지 호출인 경우
			$call_type = "design_page";
		}else if(is_file($loadFile)==true){
			//모듈 호출인 경우
			$call_type = "pageAndModule";
		}else{
			//모듈을 찾을 수 없는 경우
			$call_type = "notFind";
		}
	/*
	모듈 호출 값은 없이 페이지 호출 값만 있는 경우
	*/
	}else if($p&&($p!="index"&&$p!="main")){
		if(is_file($loadFile)==true){
			$call_type = "pageAndModule";
		}else{
			$call_type = "notFind";
		}
	}else{
		$call_type = "default";
	}
	
	/*
	모듈 혹은 수동 페이지 파일을 찾을 수 없어 호출 타입이 notFind가 된 경우, 변수값 초기화
	*/
	if($call_type=="notFind"){
		$article = "main";
		$menuInfo['class'] = "";
	}
	
	/*
	디자인 페이지 호출 이지만, 수동으로 입력한 $p 호출 값이 있는 경우 pageAndModule 로 타입 변경
	*/
	if($call_type=="design_page"&&isset($_GET['p'])){
		$call_type = "pageAndModule";	
	}
	
	/*
	레이아웃 스킨 정보를 가져옴
	*/
	$layoutDir = "m/".$site_config['ad_msite_layout']."/";
	
	/*
	사이드바, 헤더, 컨텐츠 영역에서 사용할 수 있도록 변수 글로벌화
	*/
	define("CALLED_ARTICLE",$article);
	define("CALLED_M",$m);
	define("CALLED_P",$p);
	define("CALLED_CLASS",$menuInfo['class']);
	define("CALLED_DEPTH",$menuInfo['depth']);
	define("CALLED_PARENT",$menuInfo['parent']);
	define("CALLED_VIEWTYPE",$viewType);
	define("CALLED_VIEWDIR",$viewDir);
	define("CALLED_LAYOUTDIR",$layoutDir);
?>
<!DOCTYPE HTML>
<html>
<head>
<?php include_once __DIR_PATH__."include/head_script.php";?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
<body>
<?php
	include_once __DIR_PATH__."header.inc.php";
	
	/*
	페이지 로드(출력)
	*/
	switch($call_type){
		case "design_page" :
			call_design_page("m",$p);
			break;
		case "pageAndModule" :
			$subpage = new skinController();
			$subpage->skin_file_path("sub.php");
			$subpage->skin_loop_header("[contentArea]");
			echo $subpage->skin_echo();
			include_once $loadFile;
			$subpage->skin = $subpage->skin_org;
			$subpage->skin_loop_footer("[contentArea]");
			echo $subpage->skin_echo();
			break;
		case "notFind" :
			include_once __DIR_PATH__.$defFile;
			break;
		case "default" :
			include_once __DIR_PATH__.$defFile;
			break;
		default :
			include_once __DIR_PATH__.$defFile;
	}
	
	include_once __DIR_PATH__."footer.inc.php";
?>
</body>
</html>