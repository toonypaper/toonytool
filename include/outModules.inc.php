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
		global $viewType,$site_config,$member,$viewDir,$member_type_var,$article,$m,$p;
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
	
	/*
	관리모드에서 설정한 메인화면 디자인을 적용
	*/
	//아이템이 존재 하는지 검사
	function call_admin_mainPage_item($file){
		$fileUploader = new fileUploader();
		if(!$fileUploader->fileExists($file)){
			return false;
		}else{
			return true;
		}
	}
	//아이템을 출력
	function call_admin_mainPage($vtype){
		global $viewType,$site_config,$member,$viewDir,$member_type_var,$article,$m,$p;
		//type 변수 내용에 따른 DB 필드명 선언
		if($vtype=="p"){
			$field = "ad_site_jsmain";
		}else{
			$field = "ad_msite_jsmain";
		}
		//DB에서 메인화면 디자인을 불러옴
		$mysql = new mysqlConnection();
		$mysql->select("
			SELECT $field
			FROM toony_admin_siteconfig
		");
		$items = $mysql->fetch($field);
		//DB에서 불러온 아이템 문자열을 나누어 각각의 아이템으로 분리
		$item = explode("#",$items);
		$item_count = count($item);
		//아이템이 어떤 형태인지 구분
		for($i=1;$i<$item_count;$i++){
			$nameLen = substr($item[$i],0,7);
			if(stristr($nameLen,"latest")){
				$itemType[$i] = "latest";
			}else if(stristr($nameLen,"banner")){
				$itemType[$i] = "banner";
			}else if(stristr($nameLen,"href")){
				$itemType[$i] = "href";
			}
		}
		//아이템을 HTML로 변환하여 출력함 (홈페이지)
		if($vtype=="p"){
			for($i=1;$i<$item_count;$i++){
				$data = explode("|",$item[$i]);
				$pixel_x = 92;
				$pixel_y = 40;
				switch($itemType[$i]){
					case "latest" :
						//박스 크기, 위치 계산
						$box_left = ($pixel_x+10)*($data[12]-1);
						$box_top = ($pixel_y*$data[13])-$pixel_y;
						$box_width = ($pixel_x*$data[14])+((($data[14]-1)*10)-10);
						$box_height = ($pixel_y*$data[15])-20;
						//게시판 모듈 최근게시물 출력
						echo "
							<div class=\"latest_".$data[4]." ".$data[2]."\" style=\"position:absolute; left:{$box_left}px; top:{$box_top}px; width:{$box_width}px; height:{$box_height}px; overflow:hidden;\">
						"
						.call_board_latest($vtype,$data[3],$data[1],$data[5],$data[6],$data[7],$data[4],$data[8],$data[9],$data[10],$data[11]).
						"
							</div>
						";
						break;
					case "banner" :
						//박스 크기, 위치 계산
						$box_left = ($pixel_x+10)*($data[6]-1);
						$box_top = ($pixel_y*$data[7])-$pixel_y;
						$box_width = ($pixel_x*$data[8])+((($data[8]-1)*10)-10);
						$box_height = $pixel_y*$data[9];
						//배너공간 출력
						if(!call_admin_mainPage_item(__DIR_PATH__."upload/siteInformations/".$data[2])){
							echo "
								<div class=\"banner\" style=\"position:absolute; left:{$box_left}px; top:{$box_top}px; width:{$box_width}px; height:{$box_height}px;\">
									설정한 배너 이미지 파일이 존재하지 않습니다.
								</div>
							";
						}else{
							echo "
								<div class=\"banner\" style=\"position:absolute; left:{$box_left}px; top:{$box_top}px; width:{$box_width}px; height:{$box_height}px; overflow:hidden;\">
									<a href=\"{$data[3]}\" target=\"_{$data[4]}\" title=\"{$data[5]}\"><img src=\"".__URL_PATH__."upload/siteInformations/".$data[2]."\" width=\"{$box_width}\" height=\"{$box_height}\" /></a>
								</div>
							";
						}
						break;
					case "href" :
						//박스 크기, 위치 계산
						$box_left = ($pixel_x+10)*($data[3]-1);
						$box_top = ($pixel_y*$data[4])-$pixel_y;
						$box_width = ($pixel_x*$data[5])+((($data[5]-1)*10)-10);
						$box_height = $pixel_y*$data[6];
						//외부 문서 출력
						if(!call_admin_mainPage_item(__DIR_PATH__.$data[2].".php")){
							echo "
								<div class=\"include\" style=\"position:absolute; left:{$box_left}px; top:{$box_top}px; width:{$box_width}px; height:{$box_height}px;\">
									설정한 외부 문서 파일이 존재하지 않습니다.
								</div>
							";
						}else{
							echo "
								<div class=\"include\" style=\"position:absolute; left:{$box_left}px; top:{$box_top}px; width:{$box_width}px; height:{$box_height}px; overflow:hidden;\">
							";
							include __DIR_PATH__.$data[2].".php";
							echo "
								</div>
							";
						}
						break;
				}
				//화면의 총 height를 구함
				$box_total_height = $box_top+$box_height;
				if($i==1){
					$total_height = $box_total_height;
				}
				if($box_total_height>$total_height){
					$total_height = $box_total_height;
				}
			}
		
		//아이템을 HTML로 변환하여 출력함 (모바일페이지)
		}else{
			$sort = array();
			$sort_html = array();
			for($i=1;$i<$item_count;$i++){
				$data = explode("|",$item[$i]);
				switch($itemType[$i]){
					case "latest" :
						if($data[4]=="list"){ $data[4]="default"; }
						//게시판 모듈 최근게시물 출력
						$sort[$i] = $data[13];
						$sort_html[$i] = "
							<div class=\"latest_".$data[4]." ".$data[2]."\" style=\"margin-bottom:10px;\">
						"
						.call_board_latest($vtype,$data[3],$data[1],$data[5],$data[6],$data[7],$data[4],$data[8],$data[9],$data[10],$data[11]).
						"
							</div>
						";
						break;
					case "banner" :
						//배너공간 출력
						$sort[$i] = $data[7];
						if(!call_admin_mainPage_item(__DIR_PATH__."upload/siteInformations/".$data[2])){
							$sort_html[$i] = "
								<div class=\"banner\" style=\"margin-bottom:10px;\">
									설정한 배너 이미지 파일이 존재하지 않습니다.
								</div>
							";
						}else{
							$sort_html[$i] = "
								<div class=\"banner\" style=\"margin-bottom:10px;\">
									<a href=\"{$data[3]}\" target=\"_{$data[4]}\" title=\"{$data[5]}\"><img src=\"".__URL_PATH__."upload/siteInformations/".$data[2]."\" style=\"width:100%;\" /></a>
								</div>
							";
						}
						break;
					case "href" :
						//외부 문서 출력
						$sort[$i] = $data[4];
						if(!call_admin_mainPage_item(__DIR_PATH__.$data[2].".php")){
							$sort_html[$i] = "
								<div class=\"include\" style=\"margin-bottom:10px;\">
									설정한 외부 문서 파일이 존재하지 않습니다.
								</div>
							";
						}else{
							ob_start();
							include __DIR_PATH__.$data[2].".php";
							$include_html = ob_get_contents();
							ob_end_clean();
							$sort_html[$i] = "
								<div class=\"include\" style=\"margin-bottom:10px;\">
							".$include_html."
								</div>
							";
						}
						break;
				}
			}
			//높이가 높은 순으로 아이템 HTML 출력
			asort($sort);
			foreach($sort as $key => $val){
				echo $sort_html[$key];
			}
		}
		//화면의 총 height만큼 임의 DIV를 생성
		if($vtype=="p"){
			echo "
				<div style=\"height:".$total_height."px;\"></div>
			";
		}
	}
?>