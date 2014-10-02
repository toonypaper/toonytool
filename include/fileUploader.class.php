<?php
	/*
	파일 첨부 클래스
	*/
	class fileUploader{
		var $savePath = "";
		var $saveFile = "";
		var $file_type_filter = array("html","htm","shtm","php","php3","asp","jsp","cgi","js","css","conf","dot");
		//파일명 검사
		function fileNameCheck(){
			$this->checkVar = false;
			$this->s_file1_type=array_pop(explode(".",strtolower($this->saveFile[name])));
			for($i=0;$i<=sizeof($this->file_type_filter)-1;$i++){
				if($this->s_file1_type==$this->file_type_filter[$i]){
					$this->checkVar = true;
				}
			}
			return $this->checkVar;
		}
		//파일 용량 검사
		function fileByteCheck($byteLimit){
			$this->checkVar = true;
			if($this->saveFile['size']>$byteLimit){
				$this->checkVar = false;
			}
			return $this->checkVar;
		}
		//저장 위치 검사 및 생성
		function filePathCheck(){
			if(!is_dir($this->savePath)){
				@mkdir($this->savePath,0707);
				@chmod($this->savePath,0707);
			}
		}
		//파일 확장명 추출
		function fileNameType(){
			return array_pop(explode(".",strtolower($this->saveFile['name'])));	
		}
		//파일 복사
		function fileCopy($old_file,$file){
			@copy($old_file,$file);
		}
		//파일 저장
		function fileUpload($saveFileName){
			$this->checkVar = true;
			if(!$this->file1_upload = move_uploaded_file($this->saveFile['tmp_name'],$this->savePath.$saveFileName)){
				$this->checkVar = false;
			}
			return $this->checkVar;
		}
		//파일 삭제
		function fileDelete($file){
			unlink($this->savePath.$file);
		}
	}
?>