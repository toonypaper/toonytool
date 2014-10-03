<?php
	$tpl = new skinController();
	$header = new skinController();
	$loop = new skinController();
	$footer = new skinController();
	$mysql = new mysqlConnection();
	
	/*
	메뉴 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_admin_menuInfo
		WHERE vtype='".CALLED_VIEWTYPE."' AND drop_regdate IS NULL AND useMenu='Y' AND depth<=2
		ORDER BY zindex ASC, class ASC, depth ASC
	");
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("_tpl/{$viewDir}header.inc.html");
	$header->skin_html_load($tpl->skin);
	$header->skin_loop_header("[{menu_start}]");
	$loop->skin_html_load($tpl->skin);
	$loop->skin_loop_array("[{menu_start}]","[{menu_end}]");
	$footer->skin_html_load($tpl->skin);
	$footer->skin_loop_footer("[{menu_end}]");

	/*
	템플릿 함수
	*/
	//사이트 주소
	function site_url_func(){
		global $site_config,$viewType;
		if($viewType=="p"){
			return $site_config['ad_site_url'];
		}else{
			return $site_config['ad_msite_url'];
		}
	}
	//사이트 로고
	function site_logo(){
		global $site_config;
		if($site_config['ad_logo']){
			return __URL_PATH__."upload/siteInformations/{$site_config['ad_logo']}";
		}else{
			return __URL_PATH__."admin/images/siteDefaultInfo_logo.jpg";
		}
	}
	function status_func(){
		global $site_config,$member,$viewDir;
		if($member['me_level']>9){
			//현재 페이지의 uri를 변수에 저장
			$nowUri = urlencode("?".$_SERVER['QUERY_STRING']);
			
			$btn = "<li><a href=\"".__URL_PATH__."{$viewDir}?article=login&redirect={$nowUri}\">회원로그인</a></li>";
			$btn .= "<li><a href=\"".__URL_PATH__."{$viewDir}?article=account\">신규 회원가입</a></li>";
			$btn .= "<li><a href=\"".__URL_PATH__."{$viewDir}?article=findPassword\">비밀번호 찾기</a></li>";
		}else{
			$btn = "<li><a href=\"".__URL_PATH__."{$viewDir}?article=mypage\"><strong>{$member['me_nick']}</strong>님 접속중</a></li>";
			$btn .= "<li><a href=\"".__URL_PATH__."{$viewDir}?article=mypage\">마이페이지</a></li>";
			$btn .= "<li><a href=\"".__URL_PATH__."{$viewDir}?article=member&p=logout.submit\">로그아웃</a></li>";
		}
		//관리자인 경우
		if($member['me_level']==1){
			$btn .= "<li><a href=\"".__URL_PATH__."admin/\" target=\"_blank\">관리모드</a></li>";
		}
		return $btn;
	}
	
	/*
	템플릿 치환
	*/
	//header
	$header->skin_modeling("[logo]",site_logo());
	$header->skin_modeling("[site_url]",site_url_func());
	$header->skin_modeling("[site_name]",$site_config['ad_site_name']);
	$header->skin_modeling("[status]",status_func());
	
	echo $header->skin_echo();
	//loop
	$max_repeat = $mysql->numRows();
	if($mysql->numRows()>0){
		$depthRound = 0;
		$repeatCount = 0;
		do{
			$mysql->fetchArray("idno,callName,name,class,depth,zindex,link,linkDoc,img,img2,lockMenu,href");
			$array = $mysql->array;
			
			//현재 페이지가 메뉴의 링크와 같을 경우 '.active' 클래스 추가,
			//메뉴 이미지가 있는 경우 메뉴명을 이미지로 대체
			if((CALLED_ARTICLE==$array['callName']||CALLED_CLASS==$array['idno'])&&$array['lockMenu']=="N"){
				if($array['img2']){
					$menu_img = "<img src=\"".__URL_PATH__."upload/siteInformations/".$array['img2']."\" />";
				}else if($array['img']){
					$menu_img = "<img src=\"".__URL_PATH__."upload/siteInformations/".$array['img']."\" />";
				}else{
					$menu_img = $array['name'];
				}
				$li = "<li class=\"active\">";
			}else{
				if($array['img']&&$array['img2']&&$array['depth']==1){
					$menu_img = "<img src=\"".__URL_PATH__."upload/siteInformations/".$array['img']."\" onmouseover=\"this.src='".__URL_PATH__."upload/siteInformations/".$array['img2']."';\" onmouseout=\"this.src='".__URL_PATH__."upload/siteInformations/".$array['img']."';\" />";
				}else if($array['img']&&$array['depth']==1){
					$menu_img = "<img src=\"".__URL_PATH__."upload/siteInformations/".$array['img']."\" />";
				}else{
					$menu_img = $array['name'];
				}
				$li = "<li>";
			}
			//링크 설정
			if($array['href']=="bm"){
				$header_link = "#";
			}else{
				$header_link = "?article={$array['callName']}";
			}
			//메뉴 출력
			if($array['depth']>1){
				
				if($depthRound==0){
					$menu = "<ul>{$li}<a href=\"{$header_link}\">{$menu_img}</a>";
				}else{
					$menu = "</li>{$li}<a href=\"{$header_link}\">{$menu_img}</a>";
				}
				if($repeatCount+1==$max_repeat){
					$menu = $menu."</li></ul></li>";
				}
				$depthRound++;
			}else{
				if($repeatCount!=0){
					$menu = "</li>";
				}
				if($depthRound>0){
					$menu .= "</ul></li>";
				}
				$menu .= "{$li}<a href=\"{$header_link}\">{$menu_img}</a>";
				if($repeatCount+1==$max_repeat){
					$menu = $menu."</li>";
				}
				$depthRound = 0;
			}
			$repeatCount++;
			$loop->skin_modeling("[menu]",$menu);
			echo $loop->skin_echo();
			$menu = "";
		}while($mysql->nextRec());
	}
	//footer
	echo $footer->skin_echo();
?>