<?php
	/*
	엔진이 설치되어 있는지 검사
	*/
	if(!is_file("../include/mysql.info.php")||!is_file("../include/path.info.php")){
		echo '<script type="text/javascript">document.location.href = "../install/index.php";</script>'; exit; 
	}
	
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$method = new methodController();
	$mysql = new mysqlConnection();
	
	$method->method_param("GET","m,p");
?>
<!DOCTYPE HTML>
<html>
<head>
<?php include_once __DIR_PATH__."admin/include/head_script.php";?>
</head>
<body>
<?php
	/*
	URL 분석
	*/
	if($m!=""){
		$incDir = "modules/{$m}/admin/";
	}else{
		$incDir = "admin/";
	}
	/*
	페이지 호출
	*/
	$loadFile = __DIR_PATH__.$incDir.$p.".php";
	$defFile = __DIR_PATH__."admin/main.php";
	//레벨이 1인 경우에만 페이지 호출
	if($member[me_level]==1||$p=="login"){
		if(($m||$p)&&$p!="index"&&$p!="main"&&is_file($loadFile)==true){
			include_once $loadFile;
		}else{
			include_once $defFile;
		}
	//권한이 없는 경우 로그인 페이지 호출
	}else if($p!="login"){
		$lib->func_page_level(__URL_PATH__."admin/?p=login&redirect=".urlencode("admin/?m=".$m."&p=".$p),"1");
	}
?>
</body>
</html>