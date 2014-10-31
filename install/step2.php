<?php
	include_once "functions.inc.php";
	$functions = new functions();
	
	if($functions->file_permission("../include/")==FALSE || $functions->file_permission("../upload/sessionCookies/")==FALSE || $functions->file_permission("../upload/siteInformations/")==FALSE || $functions->file_permission("../upload/smartEditor/")==FALSE){
		$functions->error_alert_location("1단계가 진행되지 않았습니다.","index.php");
	}
	if($functions->file_check("../include/path.info.php")==TRUE && $functions->file_check("../include/mysql.info.php")==TRUE){
		$functions->error_alert_location("이미 1~2단계가 진행 되었습니다.","step3.php");
	}
	
	$file = @fopen("../include/path.info.php","w");
	@fwrite($file,"<?php\n    define(\"__DIR_PATH__\",\"".str_replace('install/'.basename(__FILE__),'',str_replace("\\","/",realpath(__FILE__)))."\");\n    define(\"__URL_PATH__\",\"".str_replace('install/'.basename(__FILE__),'','http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'])."\");\n?>");
	@fclose($file);
	
	$file = @fopen("../include/engine.inc.php","w");
	@fwrite($file,"<?php\n    header(\"Content-Type: text/html; charset=UTF-8\");\n    ini_set(\"display_errors\", 1);\n    ini_set(\"error_reporting\",\"E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE\");\n    include_once \"".str_replace('install/'.basename(__FILE__),"",str_replace("\\","/",realpath(__FILE__)))."include/path.info.php\";\n    include_once __DIR_PATH__.\"/include/session.info.php\";\n    include_once __DIR_PATH__.\"/include/mysql.info.php\";\n    include_once __DIR_PATH__.\"/include/mysql.class.php\";\n    include_once __DIR_PATH__.\"/include/lib.class.php\";\n    include_once __DIR_PATH__.\"/include/paging.class.php\";\n    include_once __DIR_PATH__.\"/include/modeling.class.php\";\n    include_once __DIR_PATH__.\"/include/mailSender.class.php\";\n    include_once __DIR_PATH__.\"/include/fileUploader.class.php\";\n    include_once __DIR_PATH__.\"/include/validator.class.php\";\n?>");
	@fclose($file);
	
?>
<!DOCTYPE HTML>
<html>
<head>
<title>투니페이퍼 투니툴 - 설치하기</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../library/js/jquery-1.7.1.js"></script>
<script type="text/javascript" src="../library/js/jquery-ui.js"></script>
<script type="text/javascript" src="../library/js/ghost_html5.js"></script>
<script type="text/javascript" src="../library/js/respond.min.js"></script>
</head>
<body>
<header>
	<img src="images/title.jpg" alt="투니툴 코어 설치" />
</header>
<form name="step2Form" action="step3.php" method="post">
<article>
	<div class="inner">
		<div class="t">
			<strong>2단계</strong>Mysql 정보를 설정 합니다.
		</div>
		<div class="c">
			<span class="stitle">
				투니툴 설치와 작동을 위한 Mysql설정을 합니다.<br />
				계정의 Mysql 정보를 입력 후 다음 단계로 진행 하십시오.
			</span>
			<table cellpadding="0" cellspacing="0">
				<tr>
					<th>호스트</th>
					<td><input type="text" name="host" value="localhost" /></td>
				</tr>
				<tr>
					<th>DB 이름</th>
					<td><input type="text" name="db" value="" /></td>
				</tr>
				<tr>
					<th>아이디</th>
					<td><input type="text" name="id" value="" /></td>
				</tr>
				<tr>
					<th>패스워드</th>
					<td><input type="password" name="password" value="" /></td>
				</tr>
			</table>
		</div>
	</div>
	
</article>
<footer>
	<input type="submit" class="__button_submit" value="다음 단계로" />
</footer>
</form>
</body>
</html>
