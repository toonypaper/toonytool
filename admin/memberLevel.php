<?php
	$tpl = new skinController();
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/memberLevel.html");

	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[level_1_value]",$member_type_var['1']);
	$tpl->skin_modeling("[level_2_value]",$member_type_var['2']);
	$tpl->skin_modeling("[level_3_value]",$member_type_var['3']);
	$tpl->skin_modeling("[level_4_value]",$member_type_var['4']);
	$tpl->skin_modeling("[level_5_value]",$member_type_var['5']);
	$tpl->skin_modeling("[level_6_value]",$member_type_var['6']);
	$tpl->skin_modeling("[level_7_value]",$member_type_var['7']);
	$tpl->skin_modeling("[level_8_value]",$member_type_var['8']);
	$tpl->skin_modeling("[level_9_value]",$member_type_var['9']);
	
	echo $tpl->skin_echo();
?>