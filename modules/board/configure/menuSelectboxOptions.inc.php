<?php
	$mysql = new mysqlConnection();
	$mysql->select("
		SELECT *
		FROM toony_module_board_config
		WHERE 1
		ORDER BY regdate DESC
	");
	
	$moduleOption = "<optgroup label=\"게시판\">";
	do{
		if($mysql->numRows()>0){
			$linkRe = "?m=board&board_id=".$mysql->fetch("board_id");
			$selected_var = "";
			if($linkRe==$array[link]){
				$selected_var = "selected";
			}
			$moduleOption .= "<option value=\"".$linkRe."\" ".$selected_var.">".$mysql->fetch("name")." (".$mysql->fetch("board_id").")</option>\n";
		}
	}while($mysql->nextRec());
	$moduleOption .= "</optgroup>";
	echo $moduleOption;
?>