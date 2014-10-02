<?php
	//Step1이 진행되어 있으면 Step2로 이동
	if(is_file("../include/path.info.php")&&!is_file("../include/mysql.info.php")){
		echo '<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type="text/javascript">document.location.href = "step2.php";</script>'; exit;
	//Step2가 진행되어 있으면 Step3으로 이동
	}else if(is_file("../include/path.info.php")&&is_file("../include/mysql.info.php")){
		echo '<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type="text/javascript">document.location.href = "step3.php";</script>'; exit;
	}
	//퍼미션 검사
	function permission_check($file){
		$open = @is_writable($file);
		if(!$open){
			return "N";
		}else{
			return "Y";
		}
	}
	function permission_txt($val){
		if($val=="Y"){
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
	<img src="images/title.jpg" alt="투니툴 엔진 설치" />
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
				1. <strong>include/</strong> 디렉토리<span class="__span_sment"><?=permission_txt(permission_check("../include/"))?></span><br />
				2. <strong>upload/</strong> 내 모든 디렉토리<span class="__span_sment"><?=permission_txt(permission_check("../upload/"))?></span><br />
				3. <strong>capcha/</strong> 디렉토리<span class="__span_sment"><?=permission_txt(permission_check("../capcha/"))?></span>
			</span>
		</div>
	</div>
</article>
<footer>
	<?php
		if(permission_check("../include/")=="Y"&&permission_check("../upload/")=="Y"){
	?>
	<input type="button" class="__button_submit" value="다음 단계로" onClick="document.location.href='step2.php';" />
	<?php }else{ ?>
	<input type="button" class="__button_cancel" value="권한 설정 다시 검사하기" onClick="document.location.reload();" />
	<?php } ?>
</footer>
</body>
</html>
