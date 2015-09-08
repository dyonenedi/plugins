<?php
	 namespace Plugin\Dyonenedi;

    Class Google_adsense
    {
	    private $google = [];
	    private $google_dt;

	    public function exec(){
	    	// Parameters seted by google
		    $this->google['https']     = $this->read_global('HTTPS');
		    $this->google['ip']        = $this->read_global('REMOTE_ADDR');
		    $this->google['ref']       = $this->read_global('HTTP_REFERER');
		    $this->google['url']       = $this->read_global('HTTP_HOST') . $this->read_global('REQUEST_URI');
		    $this->google['useragent'] = $this->read_global('HTTP_USER_AGENT');

		    $this->google['ad_url']    = 'http://pagead2.googlesyndication.com/pagead/ads?';
		    $this->google['html_ad']   = "";

		    // Execute
		    $this->google_dt = time();
		    $this->google_set_screen_res();
	    	$this->google_set_muid();
	    	$this->google_set_via_and_accept();
	    	$this->google_get_ad_url();
	    	$this->google_get_ad_html();

	    	return ($this->google['html_ad']) ? true: false;
	    }

	    public function set_client($var='') {
	    	$this->google['client'] = $var;
	    }
	    public function set_slotname($var='') {
	    	$this->google['slotname'] = $var;
	    }
	    public function set_markup($var='') {
	    	$this->google['markup'] = $var;
	    }
	    public function set_output($var='') {
	    	$this->google['output'] = $var;
	    }

	    public function getAdsense() {
		    return $this->google['html_ad'];
	    }
	    
	    // ------------------------------- PRIVATE METHODS ---------------------------//

	    private function read_global($var) {
	      return isset($_SERVER[$var]) ? $_SERVER[$var]: '';
	    }

	    private function google_set_screen_res() {
			$screen_res = $this->read_global('HTTP_UA_PIXELS');
			if ($screen_res == '') {
				$screen_res = $this->read_global('HTTP_X_UP_DEVCAP_SCREENPIXELS');
			}
			if ($screen_res == '') {
				$screen_res = $this->read_global('HTTP_X_JPHONE_DISPLAY');
			}
			$res_array = preg_split('/[x,*]/', $screen_res);
			if (count($res_array) == 2) {
				$this->google['u_w']=$res_array[0];
				$this->google['u_h']=$res_array[1];
			}
	    }

	    private function google_set_muid() {
			$muid = $this->read_global('HTTP_X_DCMGUID');
			if ($muid != '') {
				$this->google['muid']=$muid;
				return;
			}
			$muid = $this->read_global('HTTP_X_UP_SUBNO');
			if ($muid != '') {
				$this->google['muid']=$muid;
				return;
			}
			$muid = $this->read_global('HTTP_X_JPHONE_UID');
			if ($muid != '') {
				$this->google['muid']=$muid;
				return;
			}
			$muid = $this->read_global('HTTP_X_EM_UID');
			if ($muid != '') {
				$this->google['muid']=$muid;
				return;
			}
	    }

	    private function google_set_via_and_accept() {
	    	$ua = $this->read_global('HTTP_USER_AGENT');
    		if ($ua == '') {
    			$this->google['via']=$this->read_global('HTTP_VIA');
     			$this->google['accept']=$this->read_global('HTTP_ACCEPT');
    		}
	    }

	    private function google_append_url($param, $value) {
	      $this->google['ad_url'] .= '&' . $param . '=' . urlencode($value);
	    }

	    private function google_append_color($param) {
	      $color_array = explode(',', $this->google[$param]);
	      $this->google_append_url($param, $color_array[$this->google_dt % count($color_array)]);
	    }

	    private function google_append_globals($param) {
	      $this->google_append_url($param, $this->google[$param]);
	    }
	   	
	    private function google_get_ad_url() {
			$this->google_append_url('dt', round(1000 * array_sum(explode(' ', microtime()))));
			foreach ($this->google as $param => $value) {
				if (strpos($param, 'color_') === 0) {
					$this->google_append_color($param);
				} else if (strpos($param, 'url') === 0) {
					$google_scheme = ($this->google['https'] == 'on') ? 'https://' : 'http://';
					$this->google_append_url($param, $google_scheme . $this->google[$param]);
				} else {
					$this->google_append_globals($param);
				}
			}
	    }

	    private function google_get_ad_html() {
		    $google_ad_handle = @fopen($this->google['ad_url'], 'r');
		    if ($google_ad_handle) {
		      	while (!feof($google_ad_handle)) {
		        	$this->google['html_ad'] .= fread($google_ad_handle, 8192);
		      	}
		      	fclose($google_ad_handle);
		    }
	    }

	}