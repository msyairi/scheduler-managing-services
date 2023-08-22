<?php
	function hariIndo ($hariInggris) {
		switch ($hariInggris) {
			case 'Sunday':
				return 'minggu';
			case 'Monday':
				return 'senin';
			case 'Tuesday':
				return 'selasa';
			case 'Wednesday':
				return 'rabu';
			case 'Thursday':
				return 'kamis';
			case 'Friday':
				return 'jumat';
			case 'Saturday':
				return 'sabtu';
			default:
				return 'hari tidak valid';
		}
	}

	function writeLog($option_setting, $title_str, $str_date, $str_message) {
		$folderLog = 
		array(
			"time loop" 	=> "log/1 - timeloop/",
			"one time" 		=> "log/2 - onetime/",
			"every day" 	=> "log/3 - everyday/",
			"select day" 	=> "log/4 - selectday/"
		);

		$txt = date('Y-m-d H:i:s')."\n  - Setting : ".$title_str."\n  - Status : ".$str_message."-->";
		$myfile = file_put_contents($folderLog[$option_setting].date('Y-m-d')."_logs.txt", $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
?>