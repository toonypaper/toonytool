<?php
	/*
	파일 첨부 및 관리 클래스
	*/
	class fileUploader{
	
		public $savePath = "";
		public $saveFile = "";
		public $filedotType = "html,htm,shtm,php,php3,asp,jsp,cgi,js,css,conf,dot";
		
		//파일명 검사
		public function fileNameCheck(){
			return libraryClass::func_fileType_check($this->saveFile['name'],$this->filedotType);
		}
		//파일이 존재하는지 검사
		public function fileExists($file){
			if(@is_file($file)){
				return TRUE;
			}else{
				return FALSE;
			}
		}
		//파일 용량 검사
		public function fileByteCheck($byteLimit){
			$checkVar = TRUE;
			if($this->saveFile['size']>$byteLimit){
				$checkVar = FALSE;
			}
			return $checkVar;
		}
		//저장 위치 검사 및 생성
		public function filePathCheck(){
			if(!is_dir($this->savePath)){
				@mkdir($this->savePath,0707);
				@chmod($this->savePath,0707);
			}
		}
		//파일 확장명 추출
		public function fileNameType(){
			return array_pop(explode(".",strtolower($this->saveFile['name'])));	
		}
		//파일 복사
		public function fileCopy($old_file,$file){
			@copy($old_file,$file);
		}
		//파일 저장
		public function fileUpload($saveFileName){
			$checkVar = TRUE;
			if(!$this->file1_upload = move_uploaded_file($this->saveFile['tmp_name'],$this->savePath.$saveFileName)){
				$checkVar = FALSE;
			}
			return $checkVar;
		}
		//파일 삭제
		public function fileDelete($file){
			if($this->fileExists($this->savePath.$file)){
				unlink($this->savePath.$file);
			}
		}
		//글 내용에 삽입된 스마트에디터 사진 삭제
		public function sEditor_fileDelete($article){
			$this->savePath = __DIR_PATH__."upload/smartEditor/";
			preg_match_all("/smartEditor\/[a-zA-Z0-9-_\.]+.(jpg|gif|png|bmp)/i",$article,$sEditor_images_ex);
			for($i=0;$i<count($sEditor_images_ex[0]);$i++){
				$this->fileName = str_replace("smartEditor/","",$this->sEditor_images_ex[0][$i]);
				if($this->fileExists($this->fileName)){
					$this->fileDelete($this->fileName);
				}
			}
		}
	}
?>