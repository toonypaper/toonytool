<?php
	$tpl = new skinController();
	
	/*
	템플릿 함수
	*/
	//모듈 사이드바 인클루드
	function modules_sideBarInclude(){
		global $m,$p;
		$path = opendir(__DIR_PATH__."modules/");
		while($dir = readdir($path)){
			if($dir!="."&&$dir!=".."){
				if(file_exists(__DIR_PATH__."modules/{$dir}/configure/sidebar.inc.html")){
					$modules_sidebarTpl[$dir] = new skinController();
					$modules_sidebarTpl[$dir]->skin_file_path("modules/{$dir}/configure/sidebar.inc.html");
				}
			}
		}
		if(count($modules_sidebarTpl)>0){
			$modules_sidebarTpl_outputs = "";
			foreach($modules_sidebarTpl as $val){
				$modules_sidebarTpl_outputs .= $val->skin_echo();
			}
			return $modules_sidebarTpl_outputs;
		}
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/sidebar.inc.html");
	
	/*
	템플릿 치환
	*/
	if($member['me_admin']=="Y"){
		$tpl->skin_modeling_hideArea("[{adminInfo_menuDisplay_start}]","[{adminInfo_menuDisplay_end}]","show");
	}else{
		$tpl->skin_modeling_hideArea("[{adminInfo_menuDisplay_start}]","[{adminInfo_menuDisplay_end}]","hide");
	}
	$tpl->skin_modeling("[modules_sideBarInclude]",modules_sideBarInclude());
	
	echo $tpl->skin_echo();
?>