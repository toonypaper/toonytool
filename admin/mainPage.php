<?php
	$tpl = new skinController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("GET","vtype");
	
	/*
	기본 정보 로드
	*/
	if($vtype=="m"){
		$table_field = "ad_msite_main";
	}else{
		$table_field = "ad_site_main";
	}
	$mysql->select("
		SELECT $table_field as site_html
		FROM toony_admin_siteconfig
		WHERE 1
		ORDER BY 1 DESC
	");
	$mysql->htmlspecialchars = 0;
	$mysql->nl2br = 0;
	$site_html = $mysql->fetch("site_html");
	
	/*
	템플릿 로드
	*/
	if($vtype=="m"){
		$load_page = "admin/_tpl/mainPage.m.html";
	}else{
		$load_page = "admin/_tpl/mainPage.html";
	}
	$tpl->skin_file_path($load_page);
	
	/*
	템플릿 함수
	*/
	function board_selecbox_options(){
		global $mysql,$vtype;
		if($vtype=="m"){
			$where = " vtype='m' AND href='pm' AND instr(link,'?m=board&board_id')>0 AND drop_regdate IS NULL";
		}else{
			$where = " vtype='p' AND href='pm' AND instr(link,'?m=board&board_id')>0 AND drop_regdate IS NULL";
		}
		$option = "<option value=\"null\" label=\"게시판 메뉴를 선택 하세요.\">";
		$mysql->select("
			SELECT *,
			REPLACE(link,'?m=board&board_id=','') AS board_id	
			FROM toony_admin_menuInfo
			WHERE $where
			ORDER BY zindex ASC,depth ASC
		");
		do{
			if($mysql->numRows()>0){
				$option .= "<option value=\"".$mysql->fetch("callName")."\" board_id=\"".$mysql->fetch("board_id")."\">".$mysql->fetch("name")." (".$mysql->fetch("callName").")</option>\n";
			}else{
				$option .= "<option value=\"null\">생성한 게시판 메뉴 없음</option>\n";
			}
		}while($mysql->nextRec());
		return $option;
	}
	function skin_selecbox_options(){
		$path = opendir(__DIR_PATH__."modules/board/latestskin/");
		$i = 0;
		while($dir = readdir($path)){
			if($dir!="."&&$dir!=".."){
				$skins[$i] = $dir;
			}
			$i++;
		}
		$option = "";
		foreach($skins as $key=>$val){
			$selected_var = "";
			$option .= "<option value=\"".$val."\" ".$selected_var.">".$val."</option>\n";
		}
		return $option;
	}
	
	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[latest_selecbox_options]",board_selecbox_options());
	$tpl->skin_modeling("[type_selecbox_options]",skin_selecbox_options());
	$tpl->skin_modeling("[site_html]",$site_html);
	echo $tpl->skin_echo();
?>