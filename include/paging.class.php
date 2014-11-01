<?php
	/*
	페이징 클래스
	*/
	class pagingClass extends mysqlConnection{
		
		public $page = 1; 
		public $total = 0; 
		public $listPerPage = 15; 
		public $totalPage; 
		public $showPerList = "7"; 
		public $addParam;
		private $startPage;
		private $endPage;
		private $prePage;
		private $nextPage;
		
		public function page_param($page){
			if($page){
				$this->page = $page;
			}
		} 
		public function Pagging($page = 1){ 
			$this->setPage($page); 
		} 
		public function setPage($page){ 
			if($page>0){
				$this->page = $page;
			}else{
				$this->page = 1;
			} 
		} 
		public function setTotal($total){ 
			$this->total = $total; 
		} 
		public function getPaggingQuery($sql){ 
			return $sql.$this->getPagging(); 
		} 
		public function getPagging(){ 
			$limit = ($this->page-1)*$this->listPerPage.",".$this->listPerPage; 
			return " limit $limit"; 
		} 
		public function setListPerPage($listPerPage){ 
			$this->listPerPage = $listPerPage; 
		} 
		public function getNoStart(){ 
			return ($this->total-($this->listPerPage*($this->page-1))); 
		} 
		public function getNo($i){ 
			return $this->getNoStart()-$i; 
		} 
		public function setParam($addParam){ 
			$this->addParam = $addParam; 
		} 
		public function setShowPerList($showPerList){
			$this->showPerList = $showPerList;
		}
		//페이지 범위 계산
		private function SetPageNav(){
			$this->totalPage = ceil($this->total/$this->listPerPage); 
			$this->startPage = (floor(($this->page-1)/$this->showPerList)*$this->showPerList)+1; 
			$this->endPage = $this->startPage+$this->showPerList-1; 
			if($this->endPage>$this->totalPage){
				$this->endPage = $this->totalPage;
			}
			$this->prePage = $this->startPage-1;
			$this->nextPage = $this->endPage+1;
		}
		//페이징 출력
		public function Show($addParam = NULL){ 
			if(is_string($addParam)){
				$this->setParam($addParam); 
			}
			if($this->total>0){
				$this->SetPageNav();
				$prn = array();
				$prn[] = "<ul class=\"__paging_area\">";
				if($this->startPage!=1){
					$prn[] = "<li class=\"___paging_before2\"><a href=\"{$this->addParam}&page=1\"><img src=\"".__URL_PATH__."images/paging_before2.jpg\">처음</a></li>";
				}
				if($this->startPage!=1){
					$prn[] = "<li class=\"___paging_before\"><a href=\"{$this->addParam}&page={$prePage}\"><img src=\"".__URL_PATH__."images/paging_before.jpg\">이전</a></li>"; 
				}
				for($i=$this->startPage;$i<=$this->endPage;$i++){ 
					if($i==$this->page){ 
						$prn[] = "<li class=\"___paging_num_active\">{$i}</li>"; 
					}else{ 
						$prn[] = "<li class=\"___paging_num\"><a href=\"{$this->addParam}&page={$i}\">{$i}</a></li>"; 
					} 
				} 
				if($this->endPage<$this->totalPage){
					$prn[] = "<li class=\"___paging_after\"><a href=\"{$this->addParam}&page={$this->nextPage}\">다음<img src=\"".__URL_PATH__."images/paging_after.jpg\"></a></li>";
				}
				if($this->endPage<$this->totalPage){
					$prn[] = "<li class=\"___paging_after2\"><a href=\"{$this->addParam}&page={$this->totalPage}\">맨끝<img src=\"".__URL_PATH__."images/paging_after2.jpg\"></a></li>";
				}
				$prn[] = "</ul>";
				return join(" ",$prn); 
			}else{ 
				return FALSE; 
			} 
		}
		//AJAX 페이징 출력
		public function Show_ajax($addParam = null,$addEle){ 
			if(is_string($addParam)){
				$this->setParam($addParam); 
			}
			if($this->total>0){
				$this->SetPageNav();
				$prn = array();
				$prn[] = "<ul class=\"__paging_area\">";
				if($this->startPage!=1){
					$prn[] = "<li class=\"___paging_before2\"><a href=\"#\" atUrl=\"{$this->addParam}&page=1\" atEle=\"{$addEle}\"><img src=\"".__URL_PATH__."images/paging_before2.jpg\">처음</a></li>";
				}
				if($this->startPage!=1){
					$prn[] = "<li class=\"___paging_before\"><a href=\"#\" atUrl=\"{$this->addParam}&page={$prePage}\" atEle=\"{$addEle}\"><img src=\"".__URL_PATH__."images/paging_before.jpg\">이전</a></li>"; 
				}
				for($i=$this->startPage;$i<=$this->endPage;$i++){ 
					if($i==$this->page){ 
						$prn[] = "<li class=\"___paging_num_active\">{$i}</li>"; 
					}else{ 
						$prn[] = "<li class=\"___paging_num\"><a href=\"#\" atUrl=\"{$this->addParam}&page={$i}\" atEle=\"{$addEle}\">{$i}</a></li>"; 
					} 
				} 
				if($this->endPage<$this->totalPage){
					$prn[] = "<li class=\"___paging_after\"><a href=\"#\" atUrl=\"{$this->addParam}&page={$this->nextPage}\" atEle=\"{$addEle}\">다음<img src=\"".__URL_PATH__."images/paging_after.jpg\"></a></li>";
				}
				if($this->endPage<$this->totalPage){
					$prn[]="<li class=\"___paging_after2\"><a href=\"#\" atUrl=\"{$this->addParam}&page={$this->totalPage}\" atEle=\"{$addEle}\">맨끝<img src=\"".__URL_PATH__."images/paging_after2.jpg\"></a></li>";
				}
				$prn[] = "</ul>";
				return join(" ",$prn); 
			}else{ 
				return FALSE; 
			} 
		}
	}
?>