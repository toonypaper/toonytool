<?php
	include_once "functions.inc.php";
	$functions = new functions();
	
	if($functions->file_check("../include/path.info.php")==TRUE){
		$functions->error_alert_location("이미 1단계가 진행 되었습니다.","step2.php");
	}
	if($functions->file_check("../include/mysql.info.php")==TRUE){
		$functions->error_alert_location("이미 1~2단계가 진행 되었습니다.","step3.php");
	}
	function permission_txt($file){
		global $functions;
		if($functions->file_permission($file)){
			return "<span style='color:blue;font-size:11px;letter-spacing:-1px;padding-left:10px;'>변경 완료됨</span>";
		}else{
			return "<span style='color:red;font-size:11px;letter-spacing:-1px;padding-left:10px;'>퍼미션 변경되지 않음</span>";
		}
	}
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
<article>
	<div class="inner">
		<div class="t">
			<strong>1단계</strong>투니툴 파일의 퍼미션 설정 여부를 검사 합니다.
		</div>
		<div class="c">
			<span class="stitle">
				투니툴 설치를 위해선 아래 디렉토리 혹은 파일의 권한(Permission)이 <strong>707</strong> 이상으로 설정되어 있어야 합니다.<br />
				권한을 설정 후 다음 단계로 진행 하십시오.
			</span>
			<span class="tb">
				1. <strong>include/</strong> 디렉토리<span class="__span_sment"><?=permission_txt("../include/")?></span><br />
				2. <strong>capcha/</strong> 디렉토리<span class="__span_sment"><?=permission_txt("../capcha/")?></span><br />
				3. <strong>upload/sessionCookies/</strong> 디렉토리<span class="__span_sment"><?=permission_txt("../upload/sessionCookies/")?></span><br />
				4. <strong>upload/siteInformations/</strong> 디렉토리<span class="__span_sment"><?=permission_txt("../upload/siteInformations/")?></span><br />
				5. <strong>upload/smartEditor/</strong> 디렉토리<span class="__span_sment"><?=permission_txt("../upload/smartEditor/")?></span>
			</span>
		</div>
	</div>
</article>
<footer>
	<?php
		if($functions->file_permission("../include/")==TRUE && $functions->file_permission("../capcha/")==TRUE && $functions->file_permission("../upload/sessionCookies/")==TRUE && $functions->file_permission("../upload/siteInformations/")==TRUE && $functions->file_permission("../upload/smartEditor/")==TRUE){
	?>
	<input type="button" class="__button_submit" value="다음 단계로" onClick="document.location.href='step2.php';" />
	<?php }else{ ?>
	<input type="button" class="__button_cancel" value="권한 설정 다시 검사하기" onClick="document.location.reload();" />
	<?php } ?>
</footer>
</body>
</html>
