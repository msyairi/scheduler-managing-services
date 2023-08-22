<?php
	if( !array_key_exists('HTTP_REFERER', $_SERVER) ) exit('No direct script access allowed');

	date_default_timezone_set('Asia/Jakarta');

	ini_set('max_execution_time', 86400);

	session_start();

	if(!isset($_POST['func'])) { $_POST['func'] = ''; }
	if(function_exists($_POST['func'])) {$_POST['func']();}

	function showproject() {
		$status 			= 201;
		$option_setting 	= "";
		$getFileJson 		= "Tidak ditemukan file config.JSON";
		$getFileCmd 		= "Tidak ditemukan file schedule_bath.CMD";
		$dec_info_project 	= "-";

		if (file_exists("projects.json")) {
		    $getProjectsJson 	= file_get_contents("projects.json");
			$arr_projects 		= json_decode($getProjectsJson);

			// var_dump('test : ' . $arr_projects[1]->schedule_name);
			if (count($arr_projects) < 1) {
				$getProjectsJson = "<span class='text-danger'>Belum ada data projects.</span>";
				$status = 204;
			}
			else {
				$getProjectsJson = "";
				$status = 200;
				foreach ($arr_projects as $val) {
					if (($val->schedule_name == $_POST['name']) && $val->folder_name == $_POST['folder']) {
						$option_setting = $val->option_setting;

						if (file_exists("projects/".$val->folder_name."/config.json")) {
						    $getFileJson = file_get_contents("projects/".$val->folder_name."/config.json");
							if (strlen($getFileJson) < 1) {
								$getFileJson = "File kosong";
							}
							else {
								$getFileJson = $getFileJson;
								$jsonFileJson = json_decode($getFileJson);

							    if ($option_setting == "time loop") {
							    	$dec_info_project = $jsonFileJson->timeloop_settime." - ".$jsonFileJson->timeloop_timeloopset;
							    }
							    elseif ($option_setting == "one time") {
							    	$dec_info_project = $jsonFileJson->onetime_setdate.", ".$jsonFileJson->onetime_settime;
							    }
							    elseif ($option_setting == "every day") {
							    	$dec_info_project = $jsonFileJson->everyday_settime;
							    }
							    elseif ($option_setting == "select day") {
							    	$dec_info_project = $jsonFileJson->selday_settime." - ".json_encode($jsonFileJson->selday_setday);
							    }
							    else {
							    	$dec_info_project = "-";
							    }
							}
						}
						else {
						    $getFileJson = "File config.JSON belum disetting.";
						}

						if (file_exists("projects/".$val->folder_name."/schedule_bath.cmd")) {
							$getFileCmd = file_get_contents("projects/".$val->folder_name."/schedule_bath.cmd");
							if (strlen($getFileCmd) < 1) {
								$getFileCmd = "File kosong";
							}
						} 
						else {
						    $getFileCmd = "File schedule_bath.CMD belum disetting.";
						}

						$info_project = "
							<div class='small'>
								<label class='text-muted'>Project Name</label>
								<p class='p-0 mb-1 text-capitalize'>".$val->schedule_name."</p>
								<label class='text-muted'>Setting</label>
								<p class='p-0 mb-1'>".$val->option_setting."</p>
								<label class='text-muted'>Folder Name</label>
								<p class='p-0 mb-1'>".$val->folder_name."</p>
								<label class='text-muted'>Create Date</label>
								<p class='p-0 mb-1'>".$val->date_create."</p>
								<label class='text-muted'>Description</label>
								<p class='p-0 mb-1'>".$dec_info_project."</p>
							</div>";

						$_SESSION['infoproj'] = $info_project;
						$_SESSION['filejson'] = $getFileJson;
						$_SESSION['filebath'] = $getFileCmd;
					}
				}
			}
		}
		else {
		    $getProjectsJson = "<span class='text-danger'>File projects.JSON belum ada.</span>";
		    $status = 204;
		}

		$data = 
			array(
				"status" 				=> $status,
				"option_setting" 		=> $option_setting,
				"file_config" 			=> $getFileJson,
				"file_bath" 			=> $getFileCmd
			);

		echo json_encode($data);
	}

	function delproject() {
		$status 			= 201;
		$option_setting 	= "";
		$getFileJson 		= "Tidak ditemukan file config.JSON";
		$getFileCmd 		= "Tidak ditemukan file schedule_bath.CMD";
		$dec_info_project 	= "-";

		if (file_exists("projects.json")) {
		    $getProjectsJson 	= file_get_contents("projects.json");
			$arr_projects 		= json_decode($getProjectsJson);
			$arr_projects_new	= array();

			if (count($arr_projects) < 1) {
				$getProjectsJson = "<span class='text-danger'>Belum ada data projects.</span>";
				$status = 204;
			}
			else {
				$getProjectsJson = "";
				$status = 200;
				$i = 0;
				$folder_name = "";
				foreach ($arr_projects as $val) {
					if (($val->schedule_name == $_POST['name']) && $val->folder_name == $_POST['folder']) {
						$folder_name = $val->folder_name;
					}
					else {
						$projects_add = 
							array(
								"schedule_name" 		=> ucwords($val->schedule_name),
								"option_setting" 		=> $val->option_setting,
								"folder_name" 			=> $val->folder_name,
								"date_create" 			=> $val->date_create
							);

						$arr_projects_new[] 	= $projects_add;
					}

					$i++;
				}

				$arr_projects_new = json_encode($arr_projects_new, JSON_PRETTY_PRINT);   
				file_put_contents('projects.json', $arr_projects_new);

				$dirFrom 	= "projects/".$folder_name;
				$dirTo 		= "delete-projects/".$folder_name;

				delFileDirLoop($dirFrom, $dirTo, $dirTo);

				$status = 200;
			}
		}
		else {
		    $getProjectsJson = "<span class='text-danger'>File projects.JSON belum ada.</span>";
		    $status = 204;
		}

		$data = 
			array(
				"status" 				=> $status
			);

		echo json_encode($data);
	}

	function delFileDirLoop($dirFrom, $dirTo, $folder_root_name) {
		$root 		= __DIR__."/".$dirFrom;
		$postDir 	= rawurldecode($root.(isset($_POST['dir']) ? $_POST['dir'] : null ));

		if ($dirTo == $folder_root_name) {
			$dirTo = $dirTo."_".date('Ymd_His');
		}
		else {
			$dirTo = $dirTo;
		}

		if( file_exists($postDir) ) {
			$files		= scandir($postDir);
			$returnDir	= substr($postDir, strlen($root));

			if(!is_dir($dirTo)) {
		    	mkdir($dirTo, 0777, true);
		    }

			natcasesort($files);

			if( count($files) > 2 ) {
				foreach( $files as $file ) {
					$htmlRel	= htmlentities($returnDir . $file,ENT_QUOTES);
					$htmlName	= htmlentities($file);
					$ext		= preg_replace('/^.*\./', '', $file);

					if($file != '.' && $file != '..' ) {
						if (pathinfo($htmlName, PATHINFO_EXTENSION)) {
							if (copy($dirFrom."/".$htmlName, $dirTo."/".$htmlName)) {
								unlink($dirFrom."/".$htmlName);
							}
						}
						else {
							delFileDirLoop($dirFrom."/".$htmlName, $dirTo."/".$htmlName, $folder_root_name);
						}
					}
				}
			}
		}

		rmdir($dirFrom);
	}
?>
