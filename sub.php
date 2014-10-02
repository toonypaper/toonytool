<?php
	$tpl = new skinController();
	$mysql = new mysqlConnection();
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("_tpl/".CALLED_VIEWDIR."sub.html");

	/*
	서브페이지 네비게이션 선언
	*/
	if(CALLED_DEPTH==1){
		$where = "callName='".CALLED_ARTICLE."'";
	}else if(CALLED_DEPTH==2){
		$where = "(callName='".CALLED_ARTICLE."') OR (class='".CALLED_CLASS."' AND depth=1)";
	}else if(CALLED_DEPTH==3){
		$where = "(callName='".CALLED_ARTICLE."') OR (class='".CALLED_CLASS."' AND depth=1) OR (idno='".CALLED_PARENT."' AND depth=2)";
	}
	$mysql->select("
		SELECT *
		FROM toony_admin_menuInfo
		WHERE vtype='".CALLED_VIEWTYPE."' AND drop_regdate IS NULL AND ($where)
		ORDER BY depth ASC
	");
	$naviTxt = "<a href=\"{$site_config['ad_site_url']}".CALLED_VIEWDIR."\">{$site_config['ad_site_name']}</a>";
	
	//depth가 1인 경우 1차 메뉴 출력
	if(CALLED_DEPTH==1){
		$naviTxt .= " > <a href=\"?article=".$mysql->fetch("callName")."\">".$mysql->fetch("name")."</a>";
	//depth가 2 이상인 경우 2차 메뉴 출력
	}else{
		do{
			$naviTxt .= " > <a href=\"?article=".$mysql->fetch("callName")."\">".$mysql->fetch("name")."</a>";
		}while($mysql->nextRec());
	}
	
	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[subpage_title]",$mysql->fetch("name"));
	$tpl->skin_modeling("[navigator]",$naviTxt);
	echo $tpl->skin_echo();
?>