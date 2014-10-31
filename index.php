<?php
	/*
	엔진이 설치되어 있는지 검사
	*/
	if(!is_file("include/mysql.info.php")||!is_file("include/path.info.php")){
		echo '<script type="text/javascript">document.location.href = "install/index.php";</script>'; exit; 
	}
	
	/*
	PC버전으로 출력함
	*/
	$viewType = "p";
	$viewDir = "";
	
	include "include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	include __DIR_PATH__."include/outModules.inc.php";
	
	$lib = new libraryClass();
	$method = new methodController();
	$innerCont = new skinController();
	$subpage = new skinController();
	$mysql = new mysqlConnection();
	
	$method->method_param("GET","article,m,p,saveViewType");
	
	/*
	관리자 정보가 생성 되었되어 있는지 검사 (없다면 설치 3단계로 이동)
	*/
	$mysql->select("
		SELECT *
		FROM toony_member_list
		WHERE me_admin='Y' AND me_drop_regdate IS NULL
	");
	if($mysql->numRows()<1){
		$lib->error_location(__URL_PATH__."install/step3.php","A");
	}
	
	/*
	검사
	*/
	if(!$article&&($m||$p)){
		$lib->error_alert_location("올바르지 않은 접근 입니다.",$site_config['ad_site_url'],"A");
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
		WHERE callName='$article' AND vtype='p' AND drop_regdate IS NULL
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
		WHERE callName='$article' AND drop_regdate IS NULL AND vtype='p'
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
		$incDir = "";
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
			//디자인 페이지 호출인 경우
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
	$layoutDir = "p/".$site_config['ad_site_layout']."/";
	
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
</head>
<body>
<?php
	include_once __DIR_PATH__."header.inc.php";
	
	/*
	페이지 로드(출력)
	*/
	switch($call_type){
		case "design_page" :
			call_design_page("p",$p);
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
	
	/*
	팝업을 띄움
	*/
	//팝업 정보 로드
	if($viewType=="p"){
		$mysql->select("
			SELECT *
			FROM toony_admin_popupconfig
			WHERE void_use='Y'
			ORDER BY regdate DESC
		");
	}
	//팝업 출력
	if($mysql->numRows()>0){
		do{
			$pop_use_val = "N";
			$mysql->fetchArray("name,img,memo,void_link,link,bleft,btop,target,start_level,end_level,pop_article,pop_article_txt");
			$popup = $mysql->array;
			//팝업을 띄우는 조건에 부합하는지 검사
			if($member['me_level']<=$popup['start_level']&&$member['me_level']>=$popup['end_level']){
				switch($popup['pop_article']){
					case "main" :
						if($article=="main"){
							$pop_use_val = "Y";
						}
						break;
					case "all" :
						$pop_use_val = "Y";
						break;
					case "select" :
						$pop_article_expl = explode(",",$popup['pop_article_txt']);
						for($i=0;$i<sizeof($pop_article_expl);$i++){
							if($article==$pop_article_expl[$i]){
								$pop_use_val = "Y";
							}
						}
						break;
				}
			}
			
			//팝업을 띄우는 조건에 부합하는 경우 팝업 출력
			if($pop_use_val=="Y"){
				if($popup['void_link']=="Y"){
					$link = $popup['link'];
				}else{
					$link = "#";
				}
				//팝업 엘리먼트를 작성
				$popup_elements = '
					<div class="__toony_popup_'.$popup['name'].'" style="display:none; padding:10px; background-color:#fff; border:1px solid #999;">
						<a href="'.$link.'" target="'.$popup['target'].'"><img src="'.__URL_PATH__.'upload/siteInformations/'.$popup['img'].'" alt="'.$popup['memo'].'" title="'.$popup['memo'].'" /></a>
						<div style="background:#333; padding:3px; color:#fff; font-size:11px; letter-spacing:-1px; position:relative;">
							<label><input type="checkbox" name="toony_popup_1week_'.$popup['name'].'" /> 오늘 하루 이 창을 열지 않음</label>
							<a href="#" class="___close" style="position:absolute; top:5px; right:8px; color:#AFAFAF;">닫기</a>
						</div>
					</div>
				';
				echo $popup_elements;
				//팝업 스크립트를 작성
				$popup_script = '
					<script type="text/javascript">
						$(document).ready(function(){
				';
				$popup_script .= '
						//팝업 - '.$popup['name'].'
						if(getCookie("toony_popup_1week_'.$popup['name'].'")!="true"){
							$(".__toony_popup_'.$popup['name'].'").css({
								"position":"absolute",
								"z-index":800,
								"left":"'.$popup['bleft'].'px",
								"top":"'.$popup['btop'].'px"
							}).show();
							$(".__toony_popup_'.$popup['name'].' .___close").click(function(){
								$(".__toony_popup_'.$popup['name'].'").hide();
								if($(".__toony_popup_'.$popup['name'].' input[name=toony_popup_1week_'.$popup['name'].']").is(":checked")==true){
									setCookie("toony_popup_1week_'.$popup['name'].'","true",1);
								}
							});
						}
				';
				$popup_script .= "
						});
					</script>
				";
				echo $popup_script;
			}
		}while($mysql->nextRec());
	}
?>
</body>
</html>