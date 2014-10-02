<?php
	$header = new skinController();
	$loop = new skinController();
	$footer = new skinController();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("GET","vtype");
	
	/*
	변수 처리
	*/
	if(!$vtype||($vtype!="p"&&$vtype!="m")){
		$vtype = "p";
	}
	
	/*
	홈페이지 메뉴 정보 로드
	*/
	$mysql->select("
		SELECT *,
		(SELECT idno FROM toony_admin_menuInfo WHERE lockMenu='Y' AND vtype='$vtype' LIMIT 1) main_idno,
		(SELECT name FROM toony_admin_menuInfo WHERE lockMenu='Y' AND vtype='$vtype' LIMIT 1) main_name
		FROM toony_admin_menuInfo
		WHERE (idno!=1&&idno!=18) AND vtype='$vtype' AND drop_regdate IS NULL
		ORDER BY zindex ASC, depth ASC
	");
	
	/*
	템플릿 로드
	*/
	//Header
	$header->skin_file_path("admin/_tpl/menuSetting.html");
	$header->skin_loop_header("[{loop_start}]");
	//Loop
	$loop->skin_file_path("admin/_tpl/menuSetting.html");
	$loop->skin_loop_array("[{loop_start}]","[{loop_end}]");
	//Footer
	$footer->skin_file_path("admin/_tpl/menuSetting.html");
	$footer->skin_loop_footer("[{loop_end}]");
	
	/*
	템플릿 함수
	*/
	function tab_active($tab_vtype){
		global $vtype;
		if($vtype==$tab_vtype){
			return " class=\"active\"";
		}else{
			return "";
		}
	}
	function name_func(){
		global $array;
		if($array['useMenu']=="Y"){
			return "<span style=\"color:#000;\">".$array['name']."</span>";
		}else{
			return "<span style=\"text-decoration:line-through; color:#999;\">".$array['name']."</span>";
		}
	}
	function not_useMenu_class(){
		global $array;
		if($array['depth']==1&&$array['useMenu']=="N"){
			return "border-color:#CACACA;";
		}else{
			return "";
		}
	}
	function add_idno_func(){
		global $array;
		if($array['depth']<2){
			return $array['idno'];
		}else{
			return $array['class'];
		}
	}
	
	/*
	템플릿 치환
	*/
	//Header
	$header->skin_modeling("[tab_active_p]",tab_active("p"));
	$header->skin_modeling("[tab_active_m]",tab_active("m"));
	$header->skin_modeling("[vtype_value]",$vtype);
	$header->skin_modeling("[mainPage_name]",$mysql->fetch("main_name"));
	$header->skin_modeling("[idno]",$mysql->fetch("main_idno"));
	echo $header->skin_echo();
	//Loop
	$i = 0;
	do{
		if($mysql->numRows()>0){
			$mysql->htmlspecialchars = 1;
			$mysql->nl2br = 1;
			$mysql->fetchArray("name,class,zindex,idno,depth,useMenu");
			$array = $mysql->array;
			$loop->skin_modeling("[name]",name_func());
			$loop->skin_modeling("[zindex]",$array['zindex']);
			$loop->skin_modeling("[depth]",$array['depth']);
			$loop->skin_modeling("[class]",$array['class']);
			$loop->skin_modeling("[parent]",$array['idno']);
			$loop->skin_modeling("[idno]",$array['idno']);
			$loop->skin_modeling("[add_idno]",add_idno_func());
			$loop->skin_modeling("[vtype_value]",$vtype);
			$loop->skin_modeling("[depth_value]",$array['depth']+1);
			$loop->skin_modeling("[not_useMenu_class]",not_useMenu_class());
			if($array['depth']>1){
				$loop->skin_modeling_hideArea("[{inner_top_start}]","[{inner_top_end}]","hide");
				$loop->skin_modeling_hideArea("[{inner_bottom_start}]","[{inner_bottom_end}]","hide");
				$loop->skin_modeling_hideArea("[{depth1_red_font_start}]","[{depth1_red_font_end}]","hide");
				$loop->skin_modeling_hideArea("[{moveDisabled_start}]","[{moveDisabled_end}]","hide");
				if($array['depth']==2){
					$loop->skin_modeling_hideArea("[{depth2_bull_start}]","[{depth2_bull_end}]","show");
				}else{
					$loop->skin_modeling_hideArea("[{depth2_bull_start}]","[{depth2_bull_end}]","hide");
				}
			}else{
				$loop->skin_modeling_hideArea("[{inner_top_start}]","[{inner_top_end}]","show");
				$loop->skin_modeling_hideArea("[{inner_bottom_start}]","[{inner_bottom_end}]","show");
				$loop->skin_modeling_hideArea("[{depth2_bull_start}]","[{depth2_bull_end}]","hide");
				$loop->skin_modeling_hideArea("[{depth1_red_font_start}]","[{depth1_red_font_end}]","show");
				$loop->skin_modeling_hideArea("[{moveDisabled_start}]","[{moveDisabled_end}]","show");
			}
			if($array['depth']>2){
				$loop->skin_modeling_hideArea("[{depth2_top_start}]","[{depth2_top_end}]","hide");
				$loop->skin_modeling_hideArea("[{depth2_bottom_start}]","[{depth2_bottom_end}]","hide");
				$loop->skin_modeling_hideArea("[{add_btn_start}]","[{add_btn_end}]","hide");
				$loop->skin_modeling_hideArea("[{depth3_tr_start}]","[{depth3_tr_end}]","show");
			}else{
				$loop->skin_modeling_hideArea("[{depth2_top_start}]","[{depth2_top_end}]","show");
				if($array['depth']>1){
					$loop->skin_modeling_hideArea("[{depth2_bottom_start}]","[{depth2_bottom_end}]","show");
				}else{
					$loop->skin_modeling_hideArea("[{depth2_bottom_start}]","[{depth2_bottom_end}]","hide");
				}
				if($vtype=="m"&&$array['depth']==2){
					$loop->skin_modeling_hideArea("[{add_btn_start}]","[{add_btn_end}]","hide");
				}else{
					$loop->skin_modeling_hideArea("[{add_btn_start}]","[{add_btn_end}]","show");
				}
				$loop->skin_modeling_hideArea("[{depth3_tr_start}]","[{depth3_tr_end}]","hide");
			}
			echo $loop->skin_echo();
			$i++;
		}
	}while($mysql->nextRec());
	//Footer
	$footer->skin_modeling("[vtype]",$vtype);
	echo $footer->skin_echo();
?>