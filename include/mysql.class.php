<?php
	/*
	Mysql 커넥터
	*/
	class mysqlConnection{
	
		private $host = __HOST__;
		private $db_name = __DB_NAME__;
		private $db_user = __DB_USER__;
		private $db_pass = __DB_PASS__;
		private $connect = "";
		
		//Mysql 연결 초기화
		public function __construct(){
			$this->connect = mysql_connect($this->host,$this->db_user,$this->db_pass ,TRUE);
			if(!$this->connect){
				die("DB에 접속할 수 없습니다.");
			}
			mysql_select_db($this->db_name,$this->connect);
			mysql_query('SET NAMES UTF8');
			$this->htmlspecialchars = 1;
			$this->nl2br = 1;
		}
		//테이블이 존재하는지 유무(true 혹은 false 반환)
		public function is_table($table){
			return mysql_query("SELECT * FROM ".$table,$this->connect);
		}
		//Select 함수
		public function select($query){
			$this->RESULT = mysql_query($query,$this->connect);
			@$this->REC_COUNT = mysql_num_rows($this->RESULT);
			@$this->ROW = mysql_fetch_assoc($this->RESULT);
			$this->ROW_NUM = 0;
		}
		//Insert, Update, Delete 함수
		public function query($query){
			$this->RESULT = mysql_query($query,$this->connect);
		}
		//레코드의 갯수를 구함
		public function numRows(){
			return $this->REC_COUNT;
		}
		//첫번째 레코드에 위치 시킴
		public function firstRec(){
			mysql_data_seek($this->RESULT,0);
			$this->ROW = mysql_fetch_assoc($this->RESULT);
		}
		//마지막 레코드에 위치 시킴
		public function lastRec(){
			mysql_data_seek($this->RESULT,-1);
			$this->ROW = mysql_fetch_assoc($this->RESULT);
		}
		//다음 레코드에 위치 시킴
		public function nextRec(){
			$this->ROW_NUM = $this->ROW_NUM+1;
			if($this->ROW_NUM<$this->REC_COUNT){
				mysql_data_seek($this->RESULT,$this->ROW_NUM);
				$this->ROW = mysql_fetch_assoc($this->RESULT);
				return TRUE;
			}else{
				return FALSE;
			}
		}
		//이전 레코드에 위치 시킴
		public function prevRec(){
			$this->ROW_NUM = $this->ROW_NUM-1;
			if($this->ROW_NUM>=0){
				mysql_data_seek($this->RESULT,$this->ROW_NUM);
				$this->ROW = mysql_fetch_assoc($this->RESULT);
				return TRUE;
			}else{
				return FALSE;
			}
		}
		//레코드의 특정 필드 값을 가져옴
		public function fetch($fieldName){
			if(isset($this->ROW[$fieldName])){
				$this->ROW_RE = stripslashes($this->ROW[$fieldName]);
				if($this->htmlspecialchars==1){
					$this->ROW_RE = htmlspecialchars($this->ROW_RE);
				}
				if($this->nl2br==1){
					$this->ROW_RE = nl2br($this->ROW_RE);
				}
				return $this->ROW_RE;
			}else{
				return "";
			}
		}
		//레코드의 모든 필드 값을 배열로 가져옴
		public function fetchArray($fieldName){
			$expl = explode(",",$fieldName);
			for($i=0;$i<sizeof($expl);$i++){
				$this->array[$expl[$i]] = stripslashes($this->fetch($expl[$i]));
			}
		}
		//Mysql 연결 해제
		public function __destruct(){
			mysql_close($this->connect);
		}
	}
?>