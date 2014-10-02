<?php
	/*
	페이징 클래스
	*/
	class pagingClass extends mysqlConnection{
		var $page=1; 
		var $total=0; 
		var $listPerPage=15; 
		var $totalPage; 
		var $showPerList=""; 
		var $addParam;
		function __construct(){
			global $viewType;
			if($viewType=="p"){
				$this->showPerList = 10;
			}else{
				$this->showPerList = 7;
			}
		}
		function page_param($page){
			if($page){
				$this->page=$page;
			}
		} 
		function Pagging($page=1){ 
			$this->setPage($page); 
		} 
		function setPage($page){ 
			$this->page=($page>0)?$page:1; 
		} 
		function setTotal($total){ 
			$this->total=$total; 
		} 
		function getPaggingQuery($sql){ 
			return $sql . $this->getPagging(); 
		} 
		function getPagging(){ 
			$limit=($this->page-1)*$this->listPerPage .",".$this->listPerPage; 
			return " limit $limit"; 
		} 
		function setListPerPage($listPerPage){ 
			$this->listPerPage=$listPerPage; 
		} 
		function getNoStart(){ 
			return ($this->total-($this->listPerPage*($this->page-1))); 
		} 
		function getNo($i){ 
			return $this->getNoStart() - $i; 
		} 
		function setParam($addParam){ 
			$this->addParam=$addParam; 
		} 
		function setShowPerList($showPerList){
			$this->showPerList=$showPerList;
		}
		//페이징 출력
		function Show($addParam=null){ 
			if(is_string($addParam)) $this->setParam($addParam); 
			if($this->total>0){ 
				$this->totalPage=ceil($this->total / $this->listPerPage); 
				$startPage=(floor(($this->page-1)/$this->showPerList)*$this->showPerList)+1; 
				$endPage=$startPage+$this->showPerList-1; 
				$endPage =($endPage > $this->totalPage)? $this->totalPage : $endPage; 
				$prn=array(); 
				$prePage=$startPage-1;
				$nextPage=$endPage+1;
				$prn[]="<ul class=\"__paging_area\">";
				if($startPage!=1) $prn[]="<li class=\"___paging_before2\"><a href=\"{$this->addParam}&page=1\"><img src=\"".__URL_PATH__."images/paging_before2.jpg\">처음</a></li>";
				if($startPage!=1) $prn[]="<li class=\"___paging_before\"><a href=\"{$this->addParam}&page={$prePage}\"><img src=\"".__URL_PATH__."images/paging_before.jpg\">이전</a></li>"; 
				for($i=$startPage;$i<=$endPage;$i++){ 
					if ($i == $this->page){ 
						$prn[]="<li class=\"___paging_num_active\">{$i}</li>"; 
					} else { 
						$prn[]="<li class=\"___paging_num\"><a href=\"{$this->addParam}&page={$i}\">{$i}</a></li>"; 
					} 
				} 
				if($endPage < $this->totalPage) $prn[]="<li class=\"___paging_after\"><a href=\"{$this->addParam}&page={$nextPage}\">다음<img src=\"".__URL_PATH__."images/paging_after.jpg\"></a></li>";
				if($endPage < $this->totalPage) $prn[]="<li class=\"___paging_after2\"><a href=\"{$this->addParam}&page={$this->totalPage}\">맨끝<img src=\"".__URL_PATH__."images/paging_after2.jpg\"></a></li>";
				$prn[]="</ul>";
				return join(" ",$prn); 
			}else{ 
				return false; 
			} 
		}
		//AJAX 페이징 출력
		function Show_ajax($addParam=null,$addEle){ 
			if(is_string($addParam)) $this->setParam($addParam); 
			if($this->total>0){ 
				$this->totalPage=ceil($this->total / $this->listPerPage); 
				$startPage=(floor(($this->page-1)/$this->showPerList)*$this->showPerList)+1; 
				$endPage=$startPage+$this->showPerList-1; 
				$endPage =($endPage > $this->totalPage)? $this->totalPage : $endPage; 
				$prn=array();
				$prePage=$startPage-1;
				$nextPage=$endPage+1;
				$prn[]="<ul class=\"__paging_area\">";
				if($startPage!=1) $prn[]="<li class=\"___paging_before2\"><a href=\"#\" atUrl=\"{$this->addParam}&page=1\" atEle=\"{$addEle}\"><img src=\"".__URL_PATH__."images/paging_before2.jpg\">처음</a></li>";
				if($startPage!=1) $prn[]="<li class=\"___paging_before\"><a href=\"#\" atUrl=\"{$this->addParam}&page={$prePage}\" atEle=\"{$addEle}\"><img src=\"".__URL_PATH__."images/paging_before.jpg\">이전</a></li>"; 
				for($i=$startPage;$i<=$endPage;$i++){ 
					if ($i == $this->page){ 
						$prn[]="<li class=\"___paging_num_active\">{$i}</li>"; 
					} else { 
						$prn[]="<li class=\"___paging_num\"><a href=\"#\" atUrl=\"{$this->addParam}&page={$i}\" atEle=\"{$addEle}\">{$i}</a></li>"; 
					} 
				} 
				if($endPage < $this->totalPage) $prn[]="<li class=\"___paging_after\"><a href=\"#\" atUrl=\"{$this->addParam}&page={$nextPage}\" atEle=\"{$addEle}\">다음<img src=\"".__URL_PATH__."images/paging_after.jpg\"></a></li>";
				if($endPage < $this->totalPage) $prn[]="<li class=\"___paging_after2\"><a href=\"#\" atUrl=\"{$this->addParam}&page={$this->totalPage}\" atEle=\"{$addEle}\">맨끝<img src=\"".__URL_PATH__."images/paging_after2.jpg\"></a></li>";
				$prn[]="</ul>";
				return join(" ",$prn); 
			}else{ 
				return false; 
			} 
		}
		function __destruct(){
			
		}
	}
?>