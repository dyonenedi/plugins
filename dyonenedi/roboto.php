<?php
	/**********************************************************
	* Roboto API 1.0
	*
	* @Created 22/10/2014
	* @Author  Dyon Enedi <dyonenedi@hotmail.com>
	* @By Dyon Enedi <dyonenedi@hotmail.com>
	*
	**********************************************************/
	
	/*
	|--------------------------------------------------------------------------
	| API 
	|--------------------------------------------------------------------------
	|
	| This class is responsible for get contents from websites or sistens bu inner html
	|
	*/

	set_time_limit(0);
	ini_set('memory_limit', '256M');

	class Roboto
	{
		public $error = false;
		public $errorMessage = "";

		public $json_decode = false;
		public $utf8_encode = false;
		public $strip_tags = false;
		public $addslashes = false;
		public $domain = false;
		public $param = false;
		public $content = array();

		public function getContent(){
			if ($this->domain) {
				if (!empty($this->param) && is_array($this->param)) {
					foreach ($this->param as $setup) {
						if (!empty($setup) && is_array($setup)) {
							if (!empty($setup['url'])) {
								if (!empty($setup['query']) && is_array($setup['query'])) {
									$this->content = $this->_getContent($setup['url'], $setup['query']);
								} else {
									$this->error = true;
									$this->errorMessage = "The values for 'query' key is required and must be an array in setup values.";
									break;
								}
							} else if (!empty($this->content['url']) && count($this->content['url']) > 0) {
								if (!empty($setup['query']) && is_array($setup['query'])) {
									$this->content = $this->_getContent($this->content['url'], $setup['query']);
								} else {
									$this->error = true;
									$this->errorMessage = "The values for 'query' key is required and must be an array in setup values.";
									break;
								}
							} else {
								$this->error = true;
								$this->errorMessage = "The 'url' key is required in setup values.";
								break;
							}
						} else {
							$this->error = true;
							$this->errorMessage = "The setup must be an array.";
							break;
						}
					}
				} else {
					$this->error = true;
					$this->errorMessage = "The param must be an array.";
				}
			} else {
				$this->error = true;
				$this->errorMessage = "The domanin property is required.";
			}
		}

		public function showContent() {
			$this->getContent();
			echo '<pre>';
			print_r($this->content);
			echo '</pre>';
			exit;
		}

		#####################################################################
		########################## PRIVATES METHODS #########################
		#####################################################################

		private function _getContent($urls,$querys) {
			$content = array();

			if (!is_array($urls)) {
				$urls[] = $urls;		
			}

			foreach ($querys as $key => $query) {
				$j = 0;
				foreach ($urls as $url) {
					$url = (substr($url, 0,1) == 'h' || substr($url, 0,1) == 'w') ? $url : $this->domain.$url;
					$dom = new DOMDocument;
					$dom->preserveWhiteSpace = false;
					@$dom->loadHTMLFile($url);
					$domxpath = new DOMXPath($dom);

					$i = 0;
				    $filtered = $domxpath->query($query);
				    while ($item = $filtered->item($i++)) {
				    	if ($key == 'img' || $key == 'url') {
				    		$link = trim($item->textContent);
				    		$link = (substr($link, 0,1) == 'h' || substr($link, 0,1) == 'w') ? $link: $this->domain.$link;
				    	} else if($key == 'text') {
				    		$link = trim($item->textContent);
				    		
				    		if ($this->utf8_encode) {
				    			$link = utf8_decode($link);
				    		}
				    		if ($this->strip_tags) {
				    			$link = strip_tags($link);
				    		}
				    		if ($this->addslashes) {
				    			$link = addslashes($link);
				    		}
				    	} else {
				    		$link = trim($item->textContent);
				    	}

				    	$link = ($this->json_decode) ? json_decode($link) : $link; 
				    	$content[$key][$j] = $link;
				    	$j++;
				    }
				}
			}

			return $content;
		}
	}