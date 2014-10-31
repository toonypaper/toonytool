<?php
	$tpl = new skinController();
	$header = new skinController();
	$p_loop = new skinController();
	$middle = new skinController();
	$m_loop = new skinController();
	$footer = new skinController();
	$mysql = new mysqlConnection();
	$lib = new libraryClass();
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/layoutSkin.html");
	$header->skin_html_load($tpl->skin);
	$header->skin_loop_header("[{p_skin_loop_start}]");
	$p_loop->skin_html_load($tpl->skin);
	$p_loop->skin_loop_array("[{p_skin_loop_start}]","[{p_skin_loop_end}]");
	$middle->skin_html_load($tpl->skin);
	$middle->skin_loop_middle("[{p_skin_loop_end}]","[{m_skin_loop_start}]");
	$m_loop->skin_html_load($tpl->skin);
	$m_loop->skin_loop_array("[{m_skin_loop_start}]","[{m_skin_loop_end}]");
	$footer->skin_html_load($tpl->skin);
	$footer->skin_loop_footer("[{m_skin_loop_end}]");
	
	/*
	템플릿 함수
	*/
	function active_class($viewType,$layout_dir){
		global $site_config;
		if($viewType=="p"){
			$viewType = "ad_site_layout";
		}else{
			$viewType = "ad_msite_layout";
		}
		if($site_config[$viewType]==$layout_dir){
			return "active";
		}else{
			return "";
		}
	}
	
	/*
	템플릿 치환
	*/
	//Header
	echo $header->skin_echo();
	
	//Pc Layout Loop
	$layout_path = opendir(__DIR_PATH__."layoutskin/p/");
	while($layout_dir = readdir($layout_path)){
		if($layout_dir!="."&&$layout_dir!=".."){
			//스킨의 _information.xml 파일에서 스킨 정보를 불러옴
			$xml = simplexml_load_file(__DIR_PATH__."layoutskin/p/{$layout_dir}/_information.xml");
			$skin_thumb = __URL_PATH__."layoutskin/p/{$layout_dir}/_thumb.jpg";
			$skin_title = $xml->skin[0]->title; //스킨 타이틀
			$skin_description = $xml->skin[0]->description; //스킨 설명
			$skin_author = $xml->skin[0]->author; //스킨 제작자
			$skin_date = $xml->skin[0]->date; //스킨 제작 일자
			
			$p_loop->skin_modeling("[active_class]",active_class("p",$layout_dir));
			$p_loop->skin_modeling("[thumb]","<img src=\"{$skin_thumb}\" width=\"160\" height=\"160\" />");
			$p_loop->skin_modeling("[name]",$layout_dir);
			$p_loop->skin_modeling("[title]",$skin_title);
			$p_loop->skin_modeling("[description]",$skin_description);
			$p_loop->skin_modeling("[author]",$skin_author);
			$p_loop->skin_modeling("[date]",$skin_date);
			echo $p_loop->skin_echo();
		}
	}
	
	//Middle
	echo $middle->skin_echo();
	
	//Mobile Layout Loop
	$layout_path = opendir(__DIR_PATH__."layoutskin/m/");
	while($layout_dir = readdir($layout_path)){
		if($layout_dir!="."&&$layout_dir!=".."){
			//스킨의 _information.xml 파일에서 스킨 정보를 불러옴
			$xml = simplexml_load_file(__DIR_PATH__."layoutskin/m/{$layout_dir}/_information.xml");
			$skin_thumb = __URL_PATH__."layoutskin/m/{$layout_dir}/_thumb.jpg";
			$skin_title = $xml->skin[0]->title; //스킨 타이틀
			$skin_description = $xml->skin[0]->description; //스킨 설명
			$skin_author = $xml->skin[0]->author; //스킨 제작자
			$skin_date = $xml->skin[0]->date; //스킨 제작 일자
			
			$m_loop->skin_modeling("[active_class]",active_class("m",$layout_dir));
			$m_loop->skin_modeling("[thumb]","<img src=\"{$skin_thumb}\" width=\"160\" height=\"160\" />");
			$m_loop->skin_modeling("[name]",$layout_dir);
			$m_loop->skin_modeling("[title]",$skin_title);
			$m_loop->skin_modeling("[description]",$skin_description);
			$m_loop->skin_modeling("[author]",$skin_author);
			$m_loop->skin_modeling("[date]",$skin_date);
			echo $m_loop->skin_echo();
		}
	}
	
	//Footer
	echo $footer->skin_echo();
?>