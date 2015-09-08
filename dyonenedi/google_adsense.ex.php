<?php
	include('google_adsense.php');
	
	$Google_adsense = new Google_adsense();
	$Google_adsense->set_client('ca-mb-pub-2118719375937578');
	$Google_adsense->set_slotname('9151270842');
	$Google_adsense->set_markup('xhtml');
	$Google_adsense->set_output('xhtml');

	if ($Google_adsense->exec()){
		echo $Google_adsense->getAdsense();
	} else {
		echo "No google adsense ad found.";
	}