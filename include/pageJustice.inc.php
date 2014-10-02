<?php
	/*
	Index.php 파일만 접근 가능하도록 제어
	*/
	if(strstr($_SERVER['PHP_SELF'],"index.php")!=true){
		exit;
	}
?>