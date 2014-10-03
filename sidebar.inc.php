<?php
	$header = new skinController();
	$loop = new skinController();
	$footer = new skinController();
	$mysql = new mysqlConnection();
	
	/*
	1차 메뉴 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_admin_menuInfo
		WHERE vtype='".CALLED_VIEWTYPE."' AND drop_regdate IS NULL AND class='".CALLED_CLASS."' AND depth=1
	");
	$mysql->fetchArray("callName,name,link,linkDoc,title_img,href");
	$d1Array = $mysql->array;
	
	/*
	2,3차 메뉴 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_admin_menuInfo
		WHERE vtype='".CALLED_VIEWTYPE."' AND drop_regdate IS NULL AND class='".CALLED_CLASS."' AND depth>=2
		ORDER BY class ASC, zindex ASC,depth ASC
	");
	
	/*
	템플릿 로드
	*/
	//header
	$header->skin_file_path("_tpl/".CALLED_VIEWDIR."sidebar.inc.html");
	$header->skin_loop_header("[{menu_start}]");
	//loop
	$loop->skin_file_path("_tpl/".CALLED_VIEWDIR."sidebar.inc.html");
	$loop->skin_loop_array("[{menu_start}]","[{menu_end}]");
	//footer
	$footer->skin_file_path("_tpl/".CALLED_VIEWDIR."sidebar.inc.html");
	$footer->skin_loop_footer("[{menu_end}]");

	/*
	템플릿 함수
	*/
	function title_img_func($d1Array){
		//빈 링크인지 검사
		if($d1Array['href']=="bm"){
			$link = "#";
		}else{
			$link = "?article={$d1Array['callName']}";
		}
		//링크 출력
		if($d1Array['title_img']!=""&&CALLED_VIEWDIR==""){
			return "<a href=\"{$link}\"><img src=\"".__URL_PATH__."upload/siteInformations/{$d1Array['title_img']}\" /></a>";
		}else{
			return "<a href=\"{$link}\">".$d1Array['name']."</a>";
		}
	}
	function sidebar_menu_img_func($d2Array){
		//detprh가 3인 경우, li에 추가할 class 선언
		if($d2Array['depth']>2){
			$d3Class = "depth3";
		}else{
			$d3Class = "";
		}
		//빈 링크인지 검사
		if($d2Array['href']=="bm"){
			$link = "#";
		}else{
			$link = "?article={$d2Array['callName']}";
		}
		//링크 출력
		if($d2Array['depth']==3&&CALLED_VIEWTYPE=="m"){
			$menu_img = "";
		}else if((CALLED_ARTICLE==$d2Array['callName'])||(CALLED_DEPTH==3&&CALLED_PARENT==$d2Array['idno'])){
			if($d2Array['img2']&&CALLED_VIEWDIR==""){
				$menu_img = "<li class=\"active $d3Class\"><a href=\"{$link}\"><img src=\"".__URL_PATH__."upload/siteInformations/".$d2Array['img2']."\" /></a></li>";
			}else if($array['img']&&CALLED_VIEWDIR==""){
				$menu_img = "<li class=\"active $d3Class\"><a href=\"{$link}\"><img src=\"".__URL_PATH__."upload/siteInformations/".$d2Array['img']."\" /></a></li>";
			}else{
				$menu_img = "<li class=\"active $d3Class\"><a href=\"{$link}\">".$d2Array['name']."</a></li>";
			}
		}else{
			if($d2Array['img']&&$d2Array['img2']&&CALLED_VIEWDIR==""){
				$menu_img = "<li class=\"$d3Class\"><a href=\"{$link}\"><img src=\"".__URL_PATH__."upload/siteInformations/".$d2Array['img']."\" onmouseover=\"this.src='".__URL_PATH__."upload/siteInformations/".$d2Array['img2']."';\" onmouseout=\"this.src='".__URL_PATH__."upload/siteInformations/".$d2Array['img']."';\" /></a></li>";
			}else if($d2Array['img']&&CALLED_VIEWDIR==""){
				$menu_img = "<li class=\"$d3Class\"><a href=\"{$link}\"><img src=\"".__URL_PATH__."upload/siteInformations/".$d2Array['img']."\" /></a></li>";
			}else{
				$menu_img = "<li class=\"$d3Class\"><a href=\"{$link}\">".$d2Array['name']."</a></li>";
			}
		}
		return $menu_img;
	}
	
	/*
	템플릿 치환
	*/
	//header
	$header->skin_modeling("[title_img]",title_img_func($d1Array));
	echo $header->skin_echo();
	//loop
	if($mysql->numRows()>0){
		do{
			$mysql->fetchArray("callName,name,link,href,linkDoc,img,img2,depth,class,idno,useMenu_side,parent");
			$d2Array = $mysql->array;
			//useMenu_side 값이 'Y'인 경우와, 부모의 useMenu_side 값이 'Y'인 경우에만 출력,
			if($d2Array['useMenu_side']=="Y"&&$notDisplay!=$d2Array['parent']){
				$loop->skin_modeling("[menu]",sidebar_menu_img_func($d2Array));
				echo $loop->skin_echo();
			//그렇지 않은 경우 $notDisplay 변수에 idno 값을 담아 3차 하위 메뉴가 노출되지 않도록 함.
			}else if($d2Array['depth']==2){
				$notDisplay = $d2Array['idno'];
			}
		}while($mysql->nextRec());
	}
	//footer
	echo $footer->skin_echo();
?>