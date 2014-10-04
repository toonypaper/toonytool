<?php
	/*
	관리모드에서 설정한 본문 기본 스타일 정보를 적용
	*/
	function call_admin_design_bodyStyle($vtype){
		$mysql = new mysqlConnection();
		$mysql->select("
			SELECT *
			FROM toony_admin_design_bodyStyle
			WHERE vtype='$vtype'
		");
		$mysql->fetchArray("body_bgColor,body_txtColor,body_txtSize,link_txtColor,link_hoverColor,link_activeColor,link_visitedColor,link_txtSize,input_txtColor,input_txtSize,usedefault");
		$cssArray = $mysql->array;
		if($cssArray['usedefault']=="N"){
			return "
				<style type=\"text/css\">
					body{ background-color:{$cssArray['body_bgColor']}; }
					body,td,select,input,div,form,textarea,center,option,pre,p,span,blockquote,td,li,dd{ color:{$cssArray['body_txtColor']}; font-size:{$cssArray['body_txtSize']}px; }
					a:link{ text-decoration:none; color:{$cssArray['link_txtColor']}; font-size:{$cssArray['link_txtSize']}px; }
					a:visited { text-decoration:none; color:{$cssArray['link_visitedColor']}; font-size:{$cssArray['link_txtSize']}px; }
					a:active { text-decoration:none; color:{$cssArray['link_activeColor']}; font-size:{$cssArray['link_txtSize']}px; }
					a:hover{ text-decoration:underline; color:{$cssArray['link_hoverColor']}; font-size:{$cssArray['link_txtSize']}px; }
					input{ color:{$cssArray['input_txtColor']}; font-size:{$cssArray['input_txtSize']}px; }
				</style>
			";
		}
	}
	/*
	관리모드에서 설정한 메인 비쥬얼 디자인을 적용
	*/
	function call_admin_design_mainVisual($vtype){
		$mysql = new mysqlConnection();
		$lib = new libraryClass();
		$mysql->select("
			SELECT *
			FROM toony_admin_design_mainVisual
			WHERE vtype='$vtype'
		");
		$mysql->htmlspecialchars = 0;
		$mysql->nl2br = 0;
		$mysql->fetchArray("scriptCode,sourceCode");
		$visualArray = $mysql->array;
		return $visualArray['scriptCode']."\n".$visualArray['sourceCode'];
	}
	/*
	관리모드에서 설정한 카피라이트(푸터) 디자인을 적용
	*/
	function call_admin_design_footer($vtype){
		$mysql = new mysqlConnection();
		$lib = new libraryClass();
		$mysql->select("
			SELECT *
			FROM toony_admin_design_footer
			WHERE vtype='$vtype'
		");
		$mysql->htmlspecialchars = 0;
		$mysql->nl2br = 0;
		$mysql->fetchArray("scriptCode,sourceCode");
		$footerArray = $mysql->array;
		return $footerArray['scriptCode']."\n".stripslashes($footerArray['sourceCode']);
	}
	/*
	관리모드에서 설정한 페이지를 적용
	*/
	function call_design_page($vtype,$name){
		global $viewType,$site_config,$member,$viewDir;
		$mysql = new mysqlConnection();
		$lib = new libraryClass();
		$subpage = new skinController();
		$mysql->select("
			SELECT *
			FROM toony_page_list
			WHERE vtype='$vtype' AND name='$name'
		");
		$mysql->htmlspecialchars = 0;
		$mysql->nl2br = 0;
		$mysql->fetchArray("scriptCode,source,level,idno");
		$pageArray = $mysql->array;
		if($vtype=="m"){
			$dir = "m/";
		}else{
			$dir = "";
		}
		//만약, 페이지 정보를 찾을 수 없는 경우(소멸된 경우) 메인 페이지로 이동
		if(!$pageArray['idno']){
			$lib->error_alert_location("페이지를 찾을 수 없거나, 소멸 되었습니다.",__URL_PATH__.$dir,"A");
		}
		
		$_SERVER['QUERY_STRING'] = urlencode($_SERVER['QUERY_STRING']);
		$lib->func_page_level(__URL_PATH__."{$viewDir}?article=login&redirect=?{$_SERVER['QUERY_STRING']}",$pageArray['level']);
		//서브페이지와 디자인 결합
		if(isset($member['me_admin'])&&$member['me_admin']=="Y"){
			$modifyButton = '
				<div style="text-align:right; padding-bottom:10px;">
					<input type="button" class="__button_small_gray" value="디자인 변경" onclick="document.location.href=\''.__URL_PATH__.'admin/?p=pageList_modify&vtype='.$viewType.'&type=modify&act='.$pageArray['idno'].'\';" />
				</div>
			';
		}else{
			$modifyButton = "";
		}
		$subpage->skin_file_path("sub.php");
		$subpage->skin_modeling("[contentArea]",$pageArray['scriptCode']."\n".$modifyButton.$pageArray['source']);
		echo $subpage->skin_echo();
	}
?>