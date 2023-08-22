<?php
	session_start();
	date_default_timezone_set('Asia/Jakarta');

	$message_err = "";
	$set_time_bath = 59;
	$schedule_name_folder = "";

	$option_setting = isset($_POST['option_setting'])?$_POST['option_setting']:"";
	$schedule_name 	= isset($_POST['schedule_name'])?$_POST['schedule_name']:"";

	if ($schedule_name != "") {
		$schedule_name_folder = strtolower(str_replace(" ", "-", $schedule_name));
		if (!file_exists('projects/'.$schedule_name_folder)) {
		    mkdir('projects/'.$schedule_name_folder, 0777, true);
		}
		else {
			$message_err = "<div class='alert alert-danger alert-dismissible fade show mt-3' role='alert'><strong>Schedule name!</strong> Schedule name sudah ada sebelumnya.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
		}
	}

	if ($schedule_name_folder != "" && file_exists('projects/'.$schedule_name_folder)) {
		if (!empty($option_setting) && $option_setting != "") {
			$timeloop_settime 		= isset($_POST['timeloop_settime'])?$_POST['timeloop_settime']:"";
			$timeloop_timeloopset 	= isset($_POST['timeloop_timeloopset'])?$_POST['timeloop_timeloopset']:"";
			$onetime_setdate 		= isset($_POST['onetime_setdate'])?$_POST['onetime_setdate']:"";
			$onetime_settime 		= isset($_POST['onetime_settime'])?$_POST['onetime_settime']:"";
			$everyday_settime 		= isset($_POST['everyday_settime'])?$_POST['everyday_settime']:"";
			$selday_settime 		= isset($_POST['selday_settime'])?$_POST['selday_settime']:"";
			$day1 					= isset($_POST['senin'])?$_POST['senin']:"";
			$day2 					= isset($_POST['selasa'])?$_POST['selasa']:"";
			$day3 					= isset($_POST['rabu'])?$_POST['rabu']:"";
			$day4 					= isset($_POST['kamis'])?$_POST['kamis']:"";
			$day5 					= isset($_POST['jumat'])?$_POST['jumat']:"";
			$day6 					= isset($_POST['sabtu'])?$_POST['sabtu']:"";
			$day7 					= isset($_POST['minggu'])?$_POST['minggu']:"";
			$dayArrVal 				= array($day1, $day2, $day3, $day4, $day5, $day6, $day7);

			$days = array();
			for ($x = 0; $x < 7; $x++) {
				$getDay = $dayArrVal[$x];

				if ($getDay != "" || $getDay != NULL) {
					array_push($days, $getDay);
				}
			}

			if ($option_setting == "time loop") {
				if ($timeloop_settime == "" || $timeloop_timeloopset == "") {
					$message_err = "<div class='alert alert-danger alert-dismissible fade show mt-3' role='alert'><strong>Time loop!</strong> Tidak boleh ada data yang kosong.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
				}
				else {
					$timeSecond = 
						array(
							"5sec" 	=> "5",
							"10sec" => "10",
							"30sec" => "30",
							"1min" 	=> "60",
							"5min" 	=> "300",
							"10min" => "600",
							"30min" => "1800",
							"1hour" => "3600",
							"2hour" => "7200",
							"3hour" => "10800",
							"4hour" => "14400",
							"5hour" => "18000",
							"6hour" => "21600"
						);

					$set_time_bath = $timeSecond[$timeloop_timeloopset];
				}
			}
			elseif ($option_setting == "one time") {
				if ($onetime_setdate == "" || $onetime_settime == "") {
					$message_err = "<div class='alert alert-danger alert-dismissible fade show mt-3' role='alert'><strong>One time!</strong> Tidak boleh ada data yang kosong.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
				}
			}
			elseif ($option_setting == "every day") {
				if ($everyday_settime == "") {
					$message_err = "<div class='alert alert-danger alert-dismissible fade show mt-3' role='alert'><strong>Every day!</strong> Tidak boleh ada data yang kosong.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
				}
			}
			elseif ($option_setting == "select day") {
				if ($selday_settime == "") {
					$message_err = "<div class='alert alert-danger alert-dismissible fade show mt-3' role='alert'><strong>Select day!</strong> Tidak boleh ada data yang kosong.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
				}

				if (count($days) < 1) {
					$message_err = "<div class='alert alert-danger alert-dismissible fade show mt-3' role='alert'><strong>Select day!</strong> Pilih hari minimal boleh 1.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
				}
			}

			if ($message_err == "") {
				// create file projects.json
				if (!file_exists("projects.json")) {
					file_put_contents('projects.json', "");
				}

			    $getProjectsJson 	= file_get_contents("projects.json");
				$arr_projects 		= json_decode($getProjectsJson, true);

				$projects_add = 
					array(
						"schedule_name" 		=> ucwords($schedule_name),
						"option_setting" 		=> $option_setting,
						"folder_name" 			=> $schedule_name_folder,
						"date_create" 			=> date('d/m/y')
					);

				$arr_projects[] 	= $projects_add;
				$final_dtProjectAdd = json_encode($arr_projects, JSON_PRETTY_PRINT);   

				file_put_contents('projects.json', $final_dtProjectAdd);

				// create file config.json
				$data = 
					array(
						"option_setting" 		=> $option_setting,
						"timeloop_settime" 		=> $timeloop_settime,
						"timeloop_timeloopset" 	=> $timeloop_timeloopset,
						"timeloop_counter" 		=> '0',
						"onetime_setdate" 		=> $onetime_setdate,
						"onetime_settime" 		=> $onetime_settime,
						"everyday_settime" 		=> $everyday_settime,
						"selday_settime" 		=> $selday_settime,
						"selday_setday" 		=> $days
					);

				file_put_contents('projects/'.$schedule_name_folder.'/config.json', json_encode($data, JSON_PRETTY_PRINT));

				// create file service schedule_bath.cmd
				$pathExePhp = 'C:\xampp\php\php.exe';
				$codeCmd = "@echo off\ntitle SCHEDULE PHP\ncls\n\n".'C:\xampp\php\php.exe schedule_counter.php'."\n\n:start\n\necho Sending data...\n".'C:\xampp\php\php.exe'." schedule_php.php\n\n:: set waktu interval ".$set_time_bath." detik\ntimeout ".$set_time_bath." /nobreak\ngoto start";

				file_put_contents('projects/'.$schedule_name_folder.'/schedule_bath.cmd', $codeCmd);

				// create folder log
				mkdir('projects/'.$schedule_name_folder.'/log', 0777, true);
				mkdir('projects/'.$schedule_name_folder.'/log/1 - timeloop', 0777, true);
				mkdir('projects/'.$schedule_name_folder.'/log/2 - onetime', 0777, true);
				mkdir('projects/'.$schedule_name_folder.'/log/3 - everyday', 0777, true);
				mkdir('projects/'.$schedule_name_folder.'/log/4 - selectday', 0777, true);

				// copy file 
				copy("root/schedule_counter.php",'projects/'.$schedule_name_folder."/schedule_counter.php");
				copy("root/schedule_php.php",'projects/'.$schedule_name_folder."/schedule_php.php");
				copy("root/functions.php",'projects/'.$schedule_name_folder."/functions.php");
				copy("root/helper.php",'projects/'.$schedule_name_folder."/helper.php");

				// message alert success
				$message_err = "<div class='alert alert-success alert-dismissible fade show mt-3' role='alert'>Data <strong>berhasil</strong> disimpan.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";

				$_SESSION['message_err'] = $message_err;

				header("location:index.php");
				exit;
			}
		}
	}

	if (file_exists("projects.json")) {
	    $getProjectsJson 	= file_get_contents("projects.json");
		$arr_projects 		= json_decode($getProjectsJson);

		// var_dump('test : ' . $arr_projects[1]->schedule_name);
		if (count($arr_projects) < 1) {
			$getProjectsJson = "<span class='text-danger'>Belum ada data projects.</span>";
		}
		else {
			$getProjectsJson = "";
			foreach ($arr_projects as $val) {
				// echo $val->schedule_name ."<br>";
				$getProjectsJson = $getProjectsJson."
					<li class='list-group-item' aria-current='true'>
						<div class='d-flex w-100 justify-content-between'>
							<h6 class='mb-1 text-capitalize'>".$val->schedule_name."</h6>
						</div>
						<small class=''>".$val->option_setting." - ".$val->folder_name."</small>
						<div class='row'>
							<div class='col-6 pt-1 text-muted'>
								<small style='font-size:0.8em'>".$val->date_create."</small>
							</div>
							<div class='col-6'>
								<div class='d-flex flex-row-reverse w-100 mt-2'>
									<button class='btn badge rounded-pill bg-light text-muted fw-normal mx-1 border' style='cursor:pointer' onclick='delProject(this)' name='".$val->schedule_name."' folder='".$val->folder_name."'>
										Delete
									</button>
									<button class='btn badge rounded-pill bg-light text-muted fw-normal mx-1 border' style='cursor:pointer' onclick='showCode(this)' name='".$val->schedule_name."' folder='".$val->folder_name."'>
										View
									</button>
								</div>
							</div>
						</div>
					</li>";
			}
		}
	}
	else {
	    $getProjectsJson = "<span class='text-danger'>File projects.JSON belum ada.</span>";
	}
?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" href="root/plugins/icheck-bootstrap/icheck-bootstrap.css">
		<link rel="stylesheet" type="text/css" href="root/plugins/font-awesome/css/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="root/plugins/date-dtsel/dtsel.css">
		<link rel="stylesheet" type="text/css" href="root/plugins/jqueryfiletree/jQueryFileTree.min.css">
		<link rel="stylesheet" type="text/css" href="root/plugins/alert/css/alert.css" />
		<link rel="stylesheet" type="text/css" href="root/plugins/alert/themes/default/theme.css" />

		<title>Schedule</title>
	</head>
	<body>
		<nav class="navbar navbar-light mb-4" style="background-color: #e3f2fd;">
			<div class="container">
				<div class="row w-100">
					<div class="col-sm-12 col-md-6">
						<a class="navbar-brand text-uppercase" href="index.php">Schedule</a>
					</div>
					<div class="col-sm-12 col-md-6 text-end">
						<span class="date-time-display h6" style="letter-spacing: 2px;"></span>
					</div>
				</div>
			</div>
		</nav>
		<section class="container-fluid">
			<div class="row g-3">
				<div class="col-sm-12 col-md-3">
					<div class="card border-0 h-100" style="background-color: #F6F8FA">
						<div class="card-body">
							<h5 class="mb-3">Projects</h5>
							<ul class="list-group list-group-flush">
								<?php echo $getProjectsJson;?>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-sm-12 col-md-9">
					<div class="h-100">
						<form action="index.php" method="POST">
							<div class="row">
								<div class="col-sm-12 col-md-9 mx-auto">
									<div class="row mb-3">
										<div class="col-12 mx-auto">
								            <div class="card h-100">
												<div class="card-body">
													<div class="">
														<label for="schedule_name" class="form-label">Schedule Name</label>
														<input type="text" class="form-control" id="schedule_name" name="schedule_name" placeholder="Schedule Name" autocomplete="off" required>
													</div>
												</div>
											</div>
								        </div>
								    </div>
									<div class="row">
										<div class="col-3 mx-auto">
								            <div class="card h-100">
												<div class="card-body">
													<div class="icheck-success">
													    <input type="radio" id="opt_timeloop" name="option_setting" value="time loop" checked />
													    <label for="opt_timeloop">Time Loop</label>
													</div>
													<div class="icheck-success">
													    <input type="radio" id="opt_onetime" name="option_setting" value="one time" />
													    <label for="opt_onetime">One Time</label>
													</div>
													<div class="icheck-success">
													    <input type="radio" id="opt_everyday" name="option_setting" value="every day" />
													    <label for="opt_everyday">Every Day</label>
													</div>
													<div class="icheck-success">
													    <input type="radio" id="opt_selday" name="option_setting" value="select day" />
													    <label for="opt_selday">Select Day</label>
													</div>
												</div>
											</div>
								        </div>
								        <div class="col-9 mx-auto">
								            <div class="card h-100">
												<div class="card-body">
													<!-- tab1 -->
													<div class="tab-1 mb-3" style="display: block;">
														<label for="timeloop_settime" class="form-label">Pilih dari Jam</label>
														<div class="row">
															<div class="col-12">
																<div class="input-group mb-3">
																	<input type="text" class="form-control border-end-0" id="timeloop_settime" name="timeloop_settime" placeholder="hh : ii" aria-label="Date" aria-describedby="date" value="<?php echo date('H:i')?>">
																	<span class="input-group-text bg-white text-muted border-start-0" id="date">
																		<i class="fa fa-clock-o" aria-hidden="true"></i>
																	</span>
																</div>
															</div>
														</div>
														<label for="timeloop_timeloopset" class="form-label">Pilih Perulangan Jam</label>
														<select class="form-select" aria-label="" id="timeloop_timeloopset" name="timeloop_timeloopset">
															<option value="5sec">5 detik</option>
															<option value="10sec">10 detik</option>
															<option value="30sec">30 detik</option>
															<option value="1min">1 menit</option>
															<option value="5min">5 menit</option>
															<option value="10min">10 menit</option>
															<option value="30min">30 menit</option>
															<option value="1hour">1 Jam</option>
															<option value="2hour">2 Jam</option>
															<option value="3hour">3 Jam</option>
															<option value="4hour">4 Jam</option>
															<option value="5hour">5 Jam</option>
															<option value="6hour">6 Jam</option>
														</select>
													</div>
													<!-- tab2 -->
													<div class="tab-2 mb-3" style="display: none;">
														<label for="onetime_setdate" class="form-label">Pilih Tanggal & Jam</label>
														<div class="row">
															<div class="col-12">
																<div class="input-group mb-3">
																	<input type="text" class="form-control border-end-0" id="onetime_setdate" name="onetime_setdate" placeholder="YYYY-MM-DD" aria-label="Date" aria-describedby="date" value="<?php echo date('Y-m-d')?>">
																	<span class="input-group-text bg-white text-muted border-start-0" id="date">
																		<i class="fa fa-calendar-check-o" aria-hidden="true"></i>
																	</span>
																</div>
															</div>
															<div class="col-12">
																<div class="input-group mb-3">
																	<input type="text" class="form-control border-end-0" id="onetime_settime" name="onetime_settime" placeholder="hh : ii" aria-label="Date" aria-describedby="date" value="<?php echo date('H:i')?>">
																	<span class="input-group-text bg-white text-muted border-start-0" id="date">
																		<i class="fa fa-clock-o" aria-hidden="true"></i>
																	</span>
																</div>
															</div>
														</div>
													</div>
													<!-- tab3 -->
													<div class="tab-3 mb-3" style="display: none;">
														<label for="everyday_settime" class="form-label">Pilih Jam</label>
														<div class="row">
															<div class="col-12">
																<div class="input-group mb-3">
																	<input type="text" class="form-control border-end-0" id="everyday_settime" name="everyday_settime" placeholder="hh : ii" aria-label="Date" aria-describedby="date" value="<?php echo date('H:i')?>">
																	<span class="input-group-text bg-white text-muted border-start-0" id="date">
																		<i class="fa fa-clock-o" aria-hidden="true"></i>
																	</span>
																</div>
															</div>
														</div>
													</div>
													<!-- tab4 -->
													<div class="tab-4 mb-3" style="display: none;">
														<label for="selday_settime" class="form-label">Pilih Jam & Hari</label>
														<div class="row">
															<div class="col-12">
																<div class="input-group mb-3">
																	<input type="text" class="form-control border-end-0" id="selday_settime" name="selday_settime" placeholder="hh : ii" aria-label="Date" aria-describedby="date" value="<?php echo date('H:i')?>">
																	<span class="input-group-text bg-white text-muted border-start-0" id="date">
																		<i class="fa fa-clock-o" aria-hidden="true"></i>
																	</span>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-sm-12 col-md-3">
																<div class="icheck-success">
																    <input type="checkbox" id="senin" name="senin" value="senin" checked />
																    <label for="senin">Senin</label>
																</div>
															</div>
															<div class="col-sm-12 col-md-3">
																<div class="icheck-success">
																    <input type="checkbox" id="selasa" name="selasa" value="selasa" checked />
																    <label for="selasa">Selasa</label>
																</div>
															</div>
															<div class="col-sm-12 col-md-3">
																<div class="icheck-success">
																    <input type="checkbox" id="rabu" name="rabu" value="rabu" checked />
																    <label for="rabu">Rabu</label>
																</div>
															</div>
															<div class="col-sm-12 col-md-3">
																<div class="icheck-success">
																    <input type="checkbox" id="kamis" name="kamis" value="kamis" checked />
																    <label for="kamis">Kamis</label>
																</div>
															</div>
															<div class="col-sm-12 col-md-3">
																<div class="icheck-success">
																    <input type="checkbox" id="jumat" name="jumat" value="jumat" checked />
																    <label for="jumat">Jumat</label>
																</div>
															</div>
															<div class="col-sm-12 col-md-3">
																<div class="icheck-success">
																    <input type="checkbox" id="sabtu" name="sabtu" value="sabtu" checked />
																    <label for="sabtu">Sabtu</label>
																</div>
															</div>
															<div class="col-sm-12 col-md-3">
																<div class="icheck-success">
																    <input type="checkbox" id="minggu" name="minggu" value="minggu" checked />
																    <label for="minggu">Minggu</label>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
								        </div>
									</div>
									<?php echo isset($_SESSION['message_err'])?$_SESSION['message_err'] : $message_err;?>
									<div class="d-grid gap-2 mt-3">
										<button type="submit" class="btn btn-success">SIMPAN SETTING</button>
									</div>
						        </div>
						        <div class="col-sm-12 col-md-3">
									<div class="card border-0 h-100" style="background-color: #F6F8FA">
										<div class="card-body">
											<h5 class="mb-3">Projects Directory</h5>
											<div class="dir-projects"></div>
										</div>
									</div>
								</div>
						    </div>
						</form>
						<div class="row">
							<div class="col-12 mx-auto">
								<div class="row g-3">
									<div class="col-sm-12 col-md-3">
										<h5 class="mt-3">Projects Information</h5>
										<div class="card border-0" style="background-color: #F6F8FA">
											<div class="card-body">
												<div class="info-project pb-3">
													<?php echo isset($_SESSION['infoproj'])?$_SESSION['infoproj']:"<span style='font-size: 0.8em;color:#880000'>//-- Description</span>";?>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-12 col-md-4">
										<h5 class="mt-3">config.JSON</h5>
										<div class="card border-0" style="background-color: #F6F8FA">
											<div class="card-body">
												<pre class="prettyprint pr-config border-0" style="font-size: 0.8em"><?php echo isset($_SESSION['filejson'])?$_SESSION['filejson']:"//-- Project file schedule_bath.CMD";?></pre>
											</div>
										</div>
									</div>
									<div class="col-sm-12 col-md-5">
										<h5 class="mt-3">schedule_bath.CMD</h5>
										<div class="card border-0" style="background-color: #F6F8FA">
											<div class="card-body">
												<pre class="prettyprint pr-bath border-0" style="font-size: 0.8em"><?php echo isset($_SESSION['filejson'])?$_SESSION['filebath']:"//-- Project file config.JSON";?></pre>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php $_POST = array();session_destroy();?>
		<script type="text/javascript" src="root/plugins/jquery/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
		<script type="text/javascript" src="root/plugins/date-dtsel/dtsel.js"></script>
		<script type="text/javascript" src="root/plugins/timepicker/jquery-clock-timepicker.js"></script>
		<script type="text/javascript" src="https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js"></script>
		<script type="text/javascript" src="root/plugins/jqueryfiletree/jQueryFileTree.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui.min.js"></script>
		<script type="text/javascript" src="root/plugins/alert/js/alert.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#opt_timeloop").prop('checked', true);
				$('#timeloop_settime').val(getDateTimeToday('time'));
				$('#onetime_setdate').val(getDateTimeToday('date'));
				$('#onetime_settime').val(getDateTimeToday('time'));
				$('#everyday_settime').val(getDateTimeToday('time'));
				$('#selday_settime').val(getDateTimeToday('time'));

				$("#opt_timeloop").click(function(){

				    $('.tab-1').show();
				    $('.tab-2').hide();
				    $('.tab-3').hide();
				    $('.tab-4').hide();

				    $('#timeloop_settime').val(getDateTimeToday('time'));
				});

				$("#opt_onetime").click(function(){
				    $('.tab-1').hide();
				    $('.tab-2').show();
				    $('.tab-3').hide();
				    $('.tab-4').hide();

				    $('#onetime_setdate').val(getDateTimeToday('date'));
				    $('#onetime_settime').val(getDateTimeToday('time'));
				});

				$("#opt_everyday").click(function(){
				    $('.tab-1').hide();
				    $('.tab-2').hide();
				    $('.tab-3').show();
				    $('.tab-4').hide();

				    $('#everyday_settime').val(getDateTimeToday('time'));
				});

				$("#opt_selday").click(function(){
				    $('.tab-1').hide();
				    $('.tab-2').hide();
				    $('.tab-3').hide();
				    $('.tab-4').show();

				    $('#selday_settime').val(getDateTimeToday('time'));
				});

				instance = new dtsel.DTS('input[name="onetime_setdate"]');

				$('#timeloop_settime').clockTimePicker();
				$('#onetime_settime').clockTimePicker();
				$('#everyday_settime').clockTimePicker();
				$('#selday_settime').clockTimePicker();

				setInterval(function() {
					bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September' , 'Oktober', 'November', 'Desember'];

					var today = new Date();
					var date = today.getDate()+' '+bulanIndo[Math.abs((today.getMonth()+1))]+' '+today.getFullYear();
					var time = ("0" + today.getHours()).substr(-2) + ":" + ("0" + today.getMinutes()).substr(-2) + ":" + ("0" + today.getSeconds()).substr(-2);
					var dateTime = date+' / '+time;
					 
					$(".date-time-display").text(dateTime)
				}, 1000);

				$('.dir-projects').fileTree({
					root: '/projects/',
					script: 'jqueryFileTree.php',
					expandSpeed: 1000,
					collapseSpeed: 1000,
					multiFolder: false
				}, function(file) {
					$('.selected-file').text( $('a[rel="'+file+'"]').text() );
				});
			});

			function getDateTimeToday(type) {
				var currentdate = new Date();
				var dateToday = currentdate.getFullYear() + "-" + ((currentdate.getMonth()+1)).toString().padStart(2, '0') + "-" + (currentdate.getDate()).toString().padStart(2, '0');
				var timeToday = (currentdate.getHours()).toString().padStart(2, '0') + ":" + (currentdate.getMinutes()).toString().padStart(2, '0');

				if (type === 'date') {
					return dateToday
				}
				else {
					return timeToday;
				}
			}

			function showCode(thisCont) {
				var name 	= thisCont.getAttribute('name');
				var folder 	= thisCont.getAttribute('folder');

				$.ajax({
	        		type        : "POST",
	        		url         : "functions.php",
	        		dataType    : "JSON",
	        		data 		: {name : name, folder : folder, func : 'showproject'},
	        		beforeSend: function() {
	        			// 
	        		},
	        		success: function(data) {
		        		$(".pr-config").text(data.file_config);
		        		$(".pr-bath").text(data.file_bath);

		        		location.reload();
	        		}
	        	})
			}

			function delProject(thisCont) {
				$.alert.open('confirm', 'Akan menghapus LOG histori data! Apakah Anda yakin?', function(confirm) {
				    if (confirm == 'yes') {
				        var name 	= thisCont.getAttribute('name');
						var folder 	= thisCont.getAttribute('folder');

						$.ajax({
			        		type        : "POST",
			        		url         : "functions.php",
			        		dataType    : "JSON",
			        		data 		: {name : name, folder : folder, func : 'delproject'},
			        		beforeSend: function() {
			        			// 
			        		},
			        		success: function(data) {
			        			if (data.status === 200) {
			        				location.reload();
			        			}
			        		}
			        	});
					}
				});
			}
		</script>
	</body>
</html>