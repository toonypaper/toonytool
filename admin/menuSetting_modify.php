<?php
	$tpl = new skinController();
	$method = new methodController();
	$mysql = new mysqlConnection();
	$lib = new libraryClass();
	
	$method->method_param("GET","type,org,class,vtype,depth,parent");
	
	/*
	변수 처리
	*/
	if(!$vtype||($vtype!="p"&&$vtype!="m")){
		$vtype = "p";
	}
	
	/*
	검사
	*/
	if(!$type||($type=="modify"&&!$org)){
		$lib->error_alert_location("호출 값이 올바르지 않습니다.",$site_config['ad_site_url'],"A");
	}
	if($vtype=="m"&&$depth==3){
		$lib->error_alert_location("모바일은 3차 메뉴를 지원하지 않습니다.",$site_config['ad_site_url'],"A");	
	}
	
	/*
	수정모드인 경우 기본 정보를 부름
	*/
	if($type=="modify"){
		$mysql->select("
			SELECT *
			FROM toony_admin_menuInfo
			WHERE idno='$org' AND drop_regdate IS NULL
		");
		$mysql->htmlspecialchars = 0;
		$mysql->nl2br = 0;
		$mysql->fetchArray("callName,forward,name,vtype,title_img,img,img2,module,page,class,link,linkDoc,lockMenu,useMenu,useMenu_side,depth,href");
		$array = $mysql->array;
		if($mysql->numRows()<1){
			$lib->error_alert_location("메뉴가 존재하지 않습니다.",$site_config['ad_site_url'],"A");
		}
	}else{
		$array = NULL;
	}
	
	/*
	2차 메뉴인 경우, 1차 메뉴의 기본 정보를 부름
	*/
	if($array['depth']==2||$depth==2){
		if($type=="modify"){
			$pr_idno = $array['class'];
		}else{
			$pr_idno = $parent;
		}
		$mysql->select("
			SELECT *
			FROM toony_admin_menuInfo
			WHERE idno='$pr_idno' AND drop_regdate IS NULL
		");
		$prArray = $mysql->fetchArray("useMenu");
		$prArray = $mysql->array;
	}else{
		$prArray = $array;
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/menuSetting_modify.html");
	
	/*
	템플릿 함수
	*/
	function id_value_func(){
		global $type,$array;
		if($type=="new"){
			return "";
		}else if($type=="modify"){
			return $array['name'];
		}
	}
	function forward_selectbox_option(){
		global $array,$org,$mysql,$vtype;
		$option = "<option value=\"\">포워딩할 메뉴를 선택 하세요.</option>";
		$mysql->select("
			SELECT *
			FROM toony_admin_menuInfo
			WHERE vtype='$vtype' AND drop_regdate IS NULL
			ORDER BY zindex ASC,depth ASC
		");
		do{
			if($mysql->numRows()>0){
				//1차 메뉴이면 optgroup으로 묶음
				if($mysql->fetch("depth")==1){
					$optgroup_s = "<optgroup label=\"".$mysql->fetch("name")."\">";
					$optgroup_e = "</optgroup>";
				}else{
					$optgroup_s = "";
					$optgroup_e = "";
				}
				//메뉴Depth구분
				if($mysql->fetch("depth")==2){
					$depth = "　　┖ ";
				}else if($mysql->fetch("depth")==3){
					$depth = "　　　┖ ";
				}else{
					$depth = "";
				}
				//option 합침
				$selected_var = "";
				if($mysql->fetch("callName")==$array['forward']){
					$selected_var = "selected";
				}else{
					$selected_var = "";
				}
				$option .= $optgroup_s."<option value=\"".$mysql->fetch("callName")."\" ".$selected_var.">".$depth.$mysql->fetch("name")." (".$mysql->fetch("callName").")</option>\n".$optgroup_e;
			}
		}while($mysql->nextRec());
		return $option;
	}
	
	function page_selectbox_option(){
		global $array,$mysql,$vtype,$lib;
		//페이지 정보를 가장 위에 노출 (홈페이지, 모바일을 구분하여 각각 출력)
		$option = "<option value=\"\">연결할 페이지 또는 모듈을 선택 하세요.</option>";
		$mysql->select("
			SELECT *
			FROM toony_page_list
			WHERE vtype='$vtype'
			ORDER BY regdate DESC
		");
		$option .= "<optgroup label=\"페이지\">";
		do{
			if($mysql->numRows()>0){
				$linkRe = "?m=page&p=".$mysql->fetch("name");
				$selected_var = "";
				if($linkRe==$lib->htmldecode($array['link'])){
					$selected_var = "selected";
				}else{
					$selected_var = "";
				}
				$option .= "<option value=\"".$linkRe."\" ".$selected_var.">".$mysql->fetch("memo")." (".$mysql->fetch("name").")</option>\n";
			}
		}while($mysql->nextRec());
		$option .= "</optgroup>";
		//모듈에서 불러옴
		$path = opendir(__DIR_PATH__."modules/");
		while($dir = readdir($path)){
			if($dir!="."&&$dir!=".."){
				$modules_pageOptions[$dir] = new skinController();
				$modules_pageOptions[$dir]->skin_file_path("modules/{$dir}/configure/menuSelectboxOptions.inc.php");
				if($array['link']!=""){
					$modules_pageOptions[$dir]->skin_modeling("value=\"".$array['link']."\"","value=\"".$array['link']."\" selected");
				}
			}
		}
		if(count($modules_pageOptions)>0){
			foreach($modules_pageOptions as $val){
				$option .= $val->skin_echo();
			}
		}
		return $option;
	}
	function useMenu_checked_value(){
		global $array,$type,$prArray,$depth;
		if((($array['useMenu']=="Y"||$type=="new")&&($array['depth']!=3&&$depth!=3))&&$prArray['useMenu']=="Y"){
			return "checked";
		}else{
			return "";
		}
	}
	function useMenu_disabled_value(){
		global $array,$depth;
		if($array['depth']>=3||$depth>=3){
			return "disabled";
		}else{
			return "";
		}
	}
	function useMenu_side_checked_value(){
		global $array,$type;
		if($array['useMenu_side']=="Y"||$type=="new"){
			return "checked";
		}else{
			return "";
		}
	}
	function useMenu_side_disabled_value(){
		global $array,$depth;
		if($array['depth']>=2||$depth>=2){
			return "";
		}else{
			return "disabled";
		}
	}
	function callName_url_func(){
		global $array,$site_config;
		if(isset($array['callName'])&&$array['vtype']=="p"){
			return "<a href=\"".$site_config['ad_site_url']."?article=".$array['callName']."\" target=\"_blank\">".$site_config['ad_site_url']."?article={$array['callName']}"."</a>";
		}else if(isset($array['callName'])&&$array['vtype']=="m"){
			return "<a href=\"".$site_config['ad_site_url']."?article=".$array['callName']."\" target=\"_blank\">".$site_config['ad_msite_url']."?article={$array['callName']}"."</a>";
		}else{
			return "";
		}
	}
	
	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[href_value]",$array['href']);
	$tpl->skin_modeling("[class_value]",$class);
	$tpl->skin_modeling("[callName_value]",$array['callName']);
	$tpl->skin_modeling("[callName_url]",callName_url_func());
	$tpl->skin_modeling("[id_value]",id_value_func());
	$tpl->skin_modeling("[depth_value]",$depth);
	$tpl->skin_modeling("[type_value]",$type);
	$tpl->skin_modeling("[parent_value]",$parent);
	$tpl->skin_modeling("[link_value]",$array['link']);
	$tpl->skin_modeling("[forward_value]",$array['forward']);
	$tpl->skin_modeling("[href_value]",$array['href']);
	$tpl->skin_modeling("[linkDoc_value]",$array['linkDoc']);
	$tpl->skin_modeling("[linkDoc_value]",$array['linkDoc']);
	$tpl->skin_modeling("[vtype_value]",$vtype);
	$tpl->skin_modeling("[org_value]",$org);
	$tpl->skin_modeling("[title_img_file_name]",$array['title_img']);
	$tpl->skin_modeling("[img_file_name]",$array['img']);
	$tpl->skin_modeling("[img2_file_name]",$array['img2']);
	$tpl->skin_modeling("[page_selectbox_options]",page_selectbox_option());
	$tpl->skin_modeling("[forward_selectbox_options]",forward_selectbox_option());
	$tpl->skin_modeling("[useMenu_checked_value]",useMenu_checked_value());
	$tpl->skin_modeling("[useMenu_header_disabled_value]",useMenu_disabled_value());
	$tpl->skin_modeling("[useMenu_side_checked_value]",useMenu_side_checked_value());
	$tpl->skin_modeling("[useMenu_side_disabled_value]",useMenu_side_disabled_value());
	if($type=="modify"){
		$tpl->skin_modeling_hideArea("[{callName_modify_start}]","[{callName_modify_end}]","hide");
		$tpl->skin_modeling_hideArea("[{callName_new_start}]","[{callName_new_end}]","show");
	}else if($type=="new"){
		$tpl->skin_modeling_hideArea("[{callName_modify_start}]","[{callName_modify_end}]","show");
		$tpl->skin_modeling_hideArea("[{callName_new_start}]","[{callName_new_end}]","hide");
	}
	if($array['lockMenu']!="Y"){
		if($type=="new"){
			$tpl->skin_modeling_hideArea("[{deleteBtn_start}]","[{deleteBtn_end}]","hide");
		}else{
			$tpl->skin_modeling_hideArea("[{deleteBtn_start}]","[{deleteBtn_end}]","show");
		}
		$tpl->skin_modeling_hideArea("[{not_mainPage_start}]","[{not_mainPage_end}]","show");
		$tpl->skin_modeling_hideArea("[{have_lockMenu_start}]","[{have_lockMenu_end}]","hide");
	}else{
		$tpl->skin_modeling_hideArea("[{deleteBtn_start}]","[{deleteBtn_end}]","hide");
		$tpl->skin_modeling_hideArea("[{not_mainPage_start}]","[{not_mainPage_end}]","hide");
		$tpl->skin_modeling_hideArea("[{have_lockMenu_start}]","[{have_lockMenu_end}]","show");
	}
	if($array['title_img']==""){
		$tpl->skin_modeling_hideArea("[title_img_hidden_start]","[title_img_hidden_end]","hide");
	}else{
		$tpl->skin_modeling_hideArea("[title_img_hidden_start]","[title_img_hidden_end]","show");
	}
	if($array['img']==""){
		$tpl->skin_modeling_hideArea("[img_hidden_start]","[img_hidden_end]","hide");
	}else{
		$tpl->skin_modeling_hideArea("[img_hidden_start]","[img_hidden_end]","show");
	}
	if($array['img2']==""){
		$tpl->skin_modeling_hideArea("[img2_hidden_start]","[img2_hidden_end]","hide");
	}else{
		$tpl->skin_modeling_hideArea("[img2_hidden_start]","[img2_hidden_end]","show");
	}
	if($array['depth']>1||$class||$array['callName']=="main"||$vtype=="m"){
		$tpl->skin_modeling_hideArea("[{1depth_titleImg_start}]","[{1depth_titleImg_end}]","hide");
	}else{
		$tpl->skin_modeling_hideArea("[{1depth_titleImg_start}]","[{1depth_titleImg_end}]","show");
	}
	if($vtype=="m"&&$array['depth']==2){
		$tpl->skin_modeling_hideArea("[{menu_Img_start}]","[{menu_Img_end}]","hide");
		$tpl->skin_modeling_hideArea("[{menuOver_Img_start}]","[{menuOver_Img_end}]","hide");
	}else{
		$tpl->skin_modeling_hideArea("[{menu_Img_start}]","[{menu_Img_end}]","show");
		$tpl->skin_modeling_hideArea("[{menuOver_Img_start}]","[{menuOver_Img_end}]","show");
	}
	
	echo $tpl->skin_echo();
?>