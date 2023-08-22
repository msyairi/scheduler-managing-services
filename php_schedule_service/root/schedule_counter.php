<?php
	$getFile = file_get_contents("config.json");
	if (strlen($getFile) < 1) {
		$getFile = "";
	}
	else {
		$jsonFile = json_decode($getFile);
	}

	if ($jsonFile->timeloop_counter == "1") {
		$data = 
		array(
			"option_setting" 		=> $jsonFile->option_setting,
			"timeloop_settime" 		=> $jsonFile->timeloop_settime,
			"timeloop_timeloopset" 	=> $jsonFile->timeloop_timeloopset,
			"timeloop_counter" 		=> '0',
			"onetime_setdate" 		=> $jsonFile->onetime_setdate,
			"onetime_settime" 		=> $jsonFile->onetime_settime,
			"everyday_settime" 		=> $jsonFile->everyday_settime,
			"selday_settime" 		=> $jsonFile->selday_settime,
			"selday_setday" 		=> $jsonFile->selday_setday
		);

		file_put_contents('config.json', json_encode($data, JSON_PRETTY_PRINT));
	}
?>