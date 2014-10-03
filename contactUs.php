<?php
	include_once "include/pageJustice.inc.php";
	
	$tpl = new skinController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("_tpl/{$viewDir}contactUs.html");
	
	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[nick]",$member['me_nick']);
	$tpl->skin_modeling("[id_value]",$member['me_id']);
	$tpl->skin_modeling("[phone_value]",$member['me_phone']);
	if($member['me_level']<10){
		$tpl->skin_modeling_hideArea("[{nick_member_start}]","[{nick_member_end}]","show");
		$tpl->skin_modeling_hideArea("[{nick_guest_start}]","[{nick_guest_end}]","hide");
	}else{
		$tpl->skin_modeling_hideArea("[{nick_member_start}]","[{nick_member_end}]","hide");
		$tpl->skin_modeling_hideArea("[{nick_guest_start}]","[{nick_guest_end}]","show");
	}
	if(!isset($__toony_member_idno)){
		$tpl->skin_modeling_hideArea("[{capcha_start}]","[{capcha_end}]","show");
		$tpl->skin_modeling("[capcha_img]","<img id=\"zsfImg\" src=\"".__URL_PATH__."capcha/zmSpamFree.php?zsfimg\" alt=\"코드를 바꾸시려면 여기를 클릭해 주세요.\" title=\"코드를 바꾸시려면 여기를 클릭해 주세요.\" style=\"cursor:pointer\" onclick=\"this.src='".__URL_PATH__."capcha/zmSpamFree.php?re&amp;zsfimg='+new Date().getTime()\" />");
	}else{
		$tpl->skin_modeling_hideArea("[{capcha_start}]","[{capcha_end}]","hide");
	}
	
	echo $tpl->skin_echo();
?>