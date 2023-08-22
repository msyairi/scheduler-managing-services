<?php
	// -- this load functions
	include "functions.php";

	date_default_timezone_set('Asia/Jakarta');

	$dateToday = date("Y-m-d H:i:s");
	$str_date = "Date : ". $dateToday."\n";

	$jsonFile = array();

	$getFile = file_get_contents("config.json");
	if (strlen($getFile) < 1) {
		$getFile = "";
	}
	else {
		$jsonFile = json_decode($getFile);
	}

	$getDays = date("l", strtotime($dateToday));
	$getDate = date("Y-m-d", strtotime($dateToday));
	$getTime = date("H:i", strtotime($dateToday));

	$str_message = "";

	if ($jsonFile->option_setting != "") {
		if ($jsonFile->option_setting == "time loop") {
			if ($jsonFile->timeloop_counter == "0") {
				$data = 
				array(
					"option_setting" 		=> $jsonFile->option_setting,
					"timeloop_settime" 		=> $jsonFile->timeloop_settime,
					"timeloop_timeloopset" 	=> $jsonFile->timeloop_timeloopset,
					"timeloop_counter" 		=> '1',
					"onetime_setdate" 		=> $jsonFile->onetime_setdate,
					"onetime_settime" 		=> $jsonFile->onetime_settime,
					"everyday_settime" 		=> $jsonFile->everyday_settime,
					"selday_settime" 		=> $jsonFile->selday_settime,
					"selday_setday" 		=> $jsonFile->selday_setday
				);

				file_put_contents('config.json', json_encode($data, JSON_PRETTY_PRINT));
			}
			else {
				$title_str = $jsonFile->option_setting." (".$jsonFile->timeloop_timeloopset.")";
				thisProcess($jsonFile->option_setting, $title_str, $str_date, $str_message);
			}
		}
		else if ($jsonFile->option_setting == "one time") {
			if (($getDate == $jsonFile->onetime_setdate) && ($getTime == $jsonFile->onetime_settime)) {
				$title_str = $jsonFile->option_setting." (".$jsonFile->onetime_setdate.", ".$jsonFile->onetime_settime.")";
				thisProcess($jsonFile->option_setting, $title_str, $str_date, $str_message);
			}
			else {
				echo "\n"."--------------------"."\n";
				echo $str_date;
				echo $str_message;
				echo "--------------------"."\n";
			}
		}
		else if ($jsonFile->option_setting == "every day") {
			if (($getTime == $jsonFile->everyday_settime)) {
				$title_str = $jsonFile->option_setting." (".$jsonFile->everyday_settime.")";
				thisProcess($jsonFile->option_setting, $title_str, $str_date, $str_message);
			}
			else {
				echo "\n"."--------------------"."\n";
				echo $str_date;
				echo $str_message;
				echo "--------------------"."\n";
			}
		}
		else if ($jsonFile->option_setting == "select day") {
			if ((in_array(hariIndo($getDays), $jsonFile->selday_setday)) && ($getTime == $jsonFile->selday_settime)) {
				$title_str = $jsonFile->option_setting." (".hariIndo($getDays)." - ".$jsonFile->selday_settime.")";
				thisProcess($jsonFile->option_setting, $title_str, $str_date, $str_message);
			}
			else {
				echo "\n"."--------------------"."\n";
				echo $str_date;
				echo $str_message;
				echo "--------------------"."\n";
			}
		}
		else {
			echo "\n"."--------------------"."\n";
			echo $str_date;
			echo $str_message;
			echo "--------------------"."\n";
		}
	}
?>