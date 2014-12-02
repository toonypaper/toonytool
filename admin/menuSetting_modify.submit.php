<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$fileUploader = new fileUploader();
	$validator = new validator();
	
	$method->method_param("POST","href,depth,parent,forward,callName,type,org,module,page,name,class,title_img_ed,img_ed,img2_ed,del_title_img,del_img,del_img2,link,linkDoc,vtype,useMenu,useMenu_header_val,useMenu_side,useMenu_side_val");
	$method->method_param("FILE","title_img,img,img2");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	변수 처리
	*/
	if(!$vtype){
		$vtype = "p";
	}
	if($useMenu_side_val=="disabled"||$useMenu_side=="checked"){
		$useMenu_side = "Y";
	}else{
		$useMenu_side = "N";
	}
	if($useMenu_header_val=="disabled"||$useMenu=="checked"){
		$useMenu = "Y";
	}else{
		$useMenu = "N";
	}
	
	/*
	검사
	*/
	//수정 모든인 경우 검사
	if($type=="modify"){
		$mysql->select("
			SELECT *
			FROM toony_admin_menuInfo
			WHERE idno='$org'
		");
		$lockMenu = $mysql->fetch("lockMenu");
		$thisDepth = $mysql->fetch("depth");
		$thisClass = $mysql->fetch("class");
		
		$validator->validt_null("name","");
		if($href=="pm"&&trim($link)==""&&$lockMenu!="Y"){
			$validator->validt_diserror("link","연결할 페이지 또는 모듈을 선택해 주세요.");
		}
		if($href=="mp"&&trim($linkDoc)==""&&$lockMenu!="Y"){
			$validator->validt_diserror("linkDoc","");
		}
		if($href=="fm"&&trim($forward)==""&&$lockMenu!="Y"){
			$validator->validt_diserror("forward","포워딩 메뉴를 선택해 주세요.");
		}
		//1차 메뉴의 header 노출 옵션이 꺼져 있으면, 2차 메뉴는 옵션 활성화 불가
		if($thisDepth==2&&$useMenu=="Y"){
			$mysql->select("
				SELECT useMenu
				FROM toony_admin_menuInfo
				WHERE idno='$thisClass' AND depth=1
			");
			if($mysql->fetch("useMenu")=="N"){
				$validator->validt_diserror("useMenu","1차메뉴가 비활성화 되어 있어 해당 메뉴를 활성화 할 수 없습니다.");
			}
		}
	}
	//등록 모드인 경우 검사
	if($type=="new"){
		$validator->validt_idx("callName",1,"");
		$mysql->select("
			SELECT *
			FROM toony_admin_menuInfo
			WHERE callName='$callName' AND drop_regdate IS NULL AND vtype='$vtype'
		");
		if($mysql->numRows()>0){
			$validator->validt_diserror("callName","이미 존재하는 코드명입니다.");
		}
		$validator->validt_null("name","");
		if($href=="pm"&&trim($link)==""){
			$validator->validt_diserror("link","연결할 페이지 또는 모듈을 선택해 주세요.");
		}
		if($href=="mp"&&trim($linkDoc)==""){
			$validator->validt_diserror("linkDoc","");
		}
		if($href=="fm"&&trim($forward)==""){
			$validator->validt_diserror("forward","포워딩 메뉴를 선택해 주세요.");
		}
		//1차 메뉴의 header 노출 옵션이 꺼져 있으면, 2차 메뉴는 옵션 활성화 불가
		if($depth==2&&$useMenu=="Y"){
			$mysql->select("
				SELECT useMenu
				FROM toony_admin_menuInfo
				WHERE idno='$parent' AND depth=1
			");
			if($mysql->fetch("useMenu")=="N"){
				$validator->validt_diserror("useMenu","1차메뉴가 비활성화 되어 있어 해당 메뉴를 활성화 할 수 없습니다.");
			}
		}
	}
	//삭제 모드인 경우 검사
	if($type=="delete"){
		$mysql->select("
			SELECT 
			(SELECT count(*) totalNum
			FROM toony_admin_menuInfo
			WHERE class='$org' AND drop_regdate IS NULL) totalNum,
			zindex,img,img2,title_img,lockMenu
			FROM toony_admin_menuInfo
			WHERE idno='$org' AND drop_regdate IS NULL
		");
		$mysql->fetchArray("img,img2,title_img,lockMenu,totalNum");
		$array = $mysql->array;
		//메뉴가 락이 걸려있는 경우 삭제 불가
		if($array['lockMenu']=="Y"){
			$validator->validt_diserror("lockMenu","삭제가 불가능한 메뉴입니다.");
		}
		//자식이 있는 경우 삭제 불가
		if($array['totalNum']>1){
			$validator->validt_diserror("","자식이 있는 메뉴는 삭제가 불가능합니다.");
		}
	}
	
	/*
	첨부 이미지 저장
	*/
	//이미지 저장 옵션
	$fileUploader->savePath = __DIR_PATH__."upload/siteInformations/";
	$fileUploader->filedotType = "jpg,bmp,gif,png";
	if($type=="modify"||$type=="new"){
		//메뉴 타이틀 이미지 업로드
		$title_img_name = "";
		if($title_img['size']>0){
			$fileUploader->saveFile = $title_img;
			//경로 및 파일 검사
			$fileUploader->filePathCheck();
			if($fileUploader->fileNameCheck()==false){
				$validator->validt_diserror("","지원되지 않는 타이틀 이미지입니다.");
			}
			//파일저장
			$title_img_name = date("ymdtis",mktime())."_".substr(md5($title_img['name']),4,10).".".$fileUploader->fileNameType();
			$title_img_name = str_replace(" ","_",$title_img_name);
			if($fileUploader->fileUpload($title_img_name)==false){
				$validator->validt_diserror("lockMenu","타이틀 이미지 저장에 실패 하였습니다.");
			}
			//이전에 첨부한 파일이 있다면 삭제
			if($title_img_ed&&$del_title_img!="checked"){
				$fileUploader->fileDelete($title_img_ed);
			}
		}
		if($del_title_img=="checked"){ 
			$fileUploader->fileDelete($title_img_ed);
		}
		if($title_img_ed!=""&&!$title_img['name']&&$del_title_img!="checked"){
			$title_img_name=$title_img_ed;
		}
		
		//메뉴 이미지 업로드
		$img_name = "";
		if($img['size']>0){
			$fileUploader->saveFile = $img;
			//경로 및 파일 검사
			$fileUploader->filePathCheck();
			if($fileUploader->fileNameCheck()==false){
				$validator->validt_diserror("","지원되지 않는 메뉴 이미지입니다.");
			}
			//파일저장
			$img_name = date("ymdtis",mktime())."_".substr(md5($img['name']),4,10).".".$fileUploader->fileNameType();
			$img_name = str_replace(" ","_",$img_name);
			if($fileUploader->fileUpload($img_name)==false){
				$validator->validt_diserror("","메뉴 이미지 저장에 실패 하였습니다.");
			}
			//이전에 첨부한 파일이 있다면 삭제
			if($img_ed&&$del_img!="checked"){
				$fileUploader->fileDelete($img_ed);
			}
		}
		if($del_img=="checked"){ 
			$fileUploader->fileDelete($img_ed);
		}
		if($img_ed!=""&&!$img['name']&&$del_img!="checked"){ 
			$img_name=$img_ed;
		}
		
		//마우스 오버 메뉴 이미지 업로드
		$img2_name = "";
		if($img2['size']>0){
			$fileUploader->saveFile = $img2;
			//경로 및 파일 검사
			$fileUploader->filePathCheck();
			if($fileUploader->fileNameCheck()==false){ 
				$validator->validt_diserror("","지원되지 않는 마우스 오버 이미지입니다.");
			}
			//파일저장
			$img2_name = date("ymdtis",mktime())."_".substr(md5($img2['name']),4,10)."2.".$fileUploader->fileNameType();
			$img2_name = str_replace(" ","_",$img2_name);
			if($fileUploader->fileUpload($img2_name)==false){
				$validator->validt_diserror("","마우스 오버 이미지 저장에 실패 하였습니다.");
			}
			//이전에 첨부한 파일이 있다면 삭제
			if($img2_ed&&$del_img2!="checked"){
				$fileUploader->fileDelete($img2_ed);
			}
		}
		if($del_img2=="checked"){
			$fileUploader->fileDelete($img2_ed);
		}
		if($img2_ed!=""&&!$img2['name']&&$del_img2!="checked"){
			$img2_name=$img2_ed;
		}
	}
	
	/**************************************************
	수정 모드인 경우
	**************************************************/
	if($type=="modify"){
		//DB 수정
		$mysql->query("
			UPDATE toony_admin_menuInfo
			SET name='$name',title_img='$title_img_name',img='$img_name',img2='$img2_name',link='$link',linkDoc='$linkDoc',useMenu='$useMenu',useMenu_side='$useMenu_side',href='$href',forward='$forward'
			WHERE idno='$org'
		");
		
		//1차 메뉴의 header 노출 옵션을 바꾼 경우 자식 메뉴들의 옵션도 바꿈
		if($thisDepth==1&&$useMenu=="N"){
			$mysql->query("
				UPDATE toony_admin_menuInfo
				SET useMenu='$useMenu'
				WHERE depth=2 AND class=$thisClass
			");
		}
		
		//완료 후 리턴
		$validator->validt_success("성공적으로 수정 되었습니다.","admin/?p=menuSetting_modify&type=modify&vtype={$vtype}&org={$org}");
		
	/**************************************************
	추가 모드인 경우
	**************************************************/
	}else if($type=="new"){
		//각종 변수 최대 값 구함
		$mysql->select("
			SELECT *,
			(SELECT zindex FROM toony_admin_menuInfo WHERE drop_regdate IS NULL AND vtype='$vtype' ORDER BY zindex DESC LIMIT 1) max_zindex,
			(SELECT idno FROM toony_admin_menuInfo ORDER BY idno DESC LIMIT 1) max_idno
			FROM toony_admin_menuInfo
			WHERE 1
		");
		$mysql->fetchArray("max_zindex,max_idno");
		$newArray = $mysql->array;
		
		//class 값 설정
		if(trim($class)!=""){
			$class_val = $class;
		}else{
			$class_val = $newArray['max_idno']+1;
		}
		//하위 메뉴 등록인 경우 zindex 값 설정
		if(trim($class)!=""){
			//parent 변수 값이 없는 경우 2차 메뉴 등록으로 간주하여 부모 메뉴의 자식 중 가장 큰 zindex값을 가져온다.
			if($parent==""){
				$where = "class='$class' AND vtype='$vtype'";
			//parent 변수 값이 있는 경우 3차 메뉴 등록으로 간주하여 parent 의 zindex값을 가져온다.
			}else{
				$where = "class='$class' AND idno='$parent' AND vtype='$vtype'";
			}
			$mysql->select("
				SELECT zindex
				FROM toony_admin_menuInfo
				WHERE $where
				ORDER BY zindex DESC
				LIMIT 1
			");
			$zindex_val = $mysql->fetch("zindex")+1;
			//부모 메뉴의 자식 중 가장 큰 zindex+1 값을 나의 zindex값으로 설정하고, 나의 zindex값 뒤의 zindex들은 +1씩 시킨다.
			$mysql->query("
				UPDATE toony_admin_menuInfo
				SET zindex=zindex+1
				WHERE zindex>($zindex_val-1) AND vtype='$vtype'
			");
		}else{
			$zindex_val = $newArray['max_zindex']+1;
		}
		//depth 값 설정
		if(trim($class)!=""){
			$depth_val = $depth;
		}else{
			$depth_val = 1;
		}
	
		//DB 입력
		$mysql->query("
			INSERT INTO toony_admin_menuInfo
			(href,forward,callName,name,title_img,img,img2,link,linkDoc,regdate,class,zindex,depth,parent,vtype,useMenu,useMenu_side)
			VALUES
			('$href','$forward','$callName','$name','$title_img_name','$img_name','$img2_name','$link','$linkDoc',now(),'$class_val','$zindex_val','$depth_val','$parent','$vtype','$useMenu','$useMenu_side')
		");
		//완료 후 리턴
		$validator->validt_success("성공적으로 추가 되었습니다.","admin/?p=menuSetting&vtype={$vtype}");
		
	/**************************************************
	삭제 모드인 경우	
	**************************************************/
	}else if($type=="delete"){
		//자신보다 zindex가 높은 메뉴들의 zindex값을 -1 시킨다.
		$mysql->query("
			UPDATE toony_admin_menuInfo
			SET zindex=zindex-1
			WHERE zindex>(".$mysql->fetch("zindex").") AND vtype='$vtype'
		");
		//자신을 삭제 처리
		$mysql->query("
			UPDATE toony_admin_menuInfo
			SET drop_regdate=now(),zindex=0
			WHERE idno='$org'
		");
		//첨부된 메뉴 이미지가 있는 경우 이미지를 삭제
		if($array['title_img']!=""){
			$fileUploader->fileDelete($array['title_img']);
		}
		if($array['img']!=""){
			$fileUploader->fileDelete($array['img']);
		}
		if($array['img2']!=""){
			$fileUploader->fileDelete($array['img2']);
		}
		//완료 후 리턴
		$validator->validt_success("성공적으로 삭제 되었습니다.","admin/?p=menuSetting&vtype={$vtype}");
	}
?>