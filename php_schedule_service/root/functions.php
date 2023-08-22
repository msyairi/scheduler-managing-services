<?php
	// -- this load helper
	include "helper.php";

	//--- this default process
	function thisProcess($option_setting, $title_str, $str_date, $str_message) {
		// -----------------------------
		// -----------------------------
		// --- this execution process --
		// -----------------------------
		// -----------------------------


		// --- write code process is here


	    // -----------------------------
		// -----------------------------
		// --- this execution process --
		// -----------------------------
		// -----------------------------

	    $str_option_setting = "Setting : ".$title_str."\n";
	    $str_message = "Status : Belum dibuatkan process (edit functions.php)"."\n";

		echo "\n"."-----------------------------"."\n";
		echo $str_option_setting;
		echo $str_date;
		echo $str_message;
		echo "-----------------------------"."\n";

		// -- this call write log
		writeLog($option_setting, $title_str, $str_date, $str_message);
	}
?>