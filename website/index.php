<!DOCTYPE html>

<html>
	<head>
		<meta charset="utf-8"/>
		<title>RPi Irrigation System Config</title>
	</head>

	<body style = "background-color: white;">
		<font size="+3">
		Raspberry Pi Irrigation Configuration Page<br>
		</font>

		<font size="+1">

		<br><b>Please enter times in military time format<br>
		Enter the run time in the format: 15, 30, 90<br>
		Run times are in minutes.
		<br>
		</font>
		<p align = "center">
		<a href="?fstop=true">Force Stop</a>
		&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
		<a href="?fstart=true">Force Start</a>
		</p>

		<?php

		$directory = "/home/pi/irrigation_system/";

		if($_GET['fstop']){
			$output = shell_exec("pgrep -f sprinkler | xargs sudo kill");
			$output = shell_exec("sudo ".$directory."fstop.py");
		}
		?>


		<form action="input_post.php" method="post">
		<p align="center">
		<font size="+1">
			Current Settings:<br>
			<?php
			$cmd = escapeshellcmd("sudo ".$directory."oldcron.py");
			$output = shell_exec($cmd);
			$out_arr = preg_split("/[\s,]+/",$output);
			for($i=0; ($i<count($out_arr)-1); $i++){
				if($i%2 !== 0){
					echo($out_arr[$i]);
					echo(":".$out_arr[$i-1]." &nbsp&nbsp");
				}
			}
			$cmd2 = escapeshellcmd($directory."get_runtime.py");
			$output2 = shell_exec($cmd2);
			$out_arr2 = preg_split("/[\s,]+/",$output2);
			echo("<br><br>Run Times:<br><br>");
			for($i=0; ($i<count($out_arr2)-1); $i++){
				echo("Zone ".$i."&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
			}
			echo("<br>");
			for($i=0; ($i<count($out_arr2)-1); $i++){
				echo($out_arr2[$i]." min.&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
			}

			?>
		</font>
		</p>

		Zone 0 Run Time:<br>
		<input type = "text" name="runtime[0]"><br><br>
		Zone 1 Run Time:<br>
		<input type = "text" name="runtime[1]"><br><br>
		Zone 2 Run Time:<br>
		<input type="text" name="runtime[2]"><br><br><br><br>

		Time 1:<br>
		<input type = "text" name="times[0]"><br><br>
		Time 2:<br>
		<input type ="text" name="times[1]"><br><br>
		Time 3:<br>
		<input type = "text" name="times[2]"><br><br>
		Time 4:<br>
		<input type = "text" name="times[3]"><br><br>
		Time 5:<br>
		<input type = "text" name="times[4]"><br><br>
		Time 6:<br>
		<input type = "text" name="times[5]"<br><br>

		<br><input type="submit" value="Submit">
		</form>

	</body>
</html>
