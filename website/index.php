<!DOCTYPE html>

<!-- index.php -->

<html>
	
	<!-- My HTML and PHP skills are not the best so this website is very basic 
	and simply extracts data in a conveinent location that can be accessed easily -->
	
	<?php
		/* Working directory for scripts accessed by this website
			Scripts used by this file:
			fstop.py
			get_old_cron.py
			get_runtime.py
		*/
		
		$directory = "/home/pi/irrigation_system/";
	?>
	
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
		/* 'fstop' will be appended to the URL if the 'Force Stop' button has been pressed.
			
			This checks if the button has been pressed. If true, this will attempt to kill
			processes with the word 'sprinkler' in it (e.g. sprinkler_main.py). 
			
			fstop.py is also called, which sets all GPIO pins connected to the zones low. */
			
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
			
		/*  The current settings are printed here to the site. 
			'get_old_cron.py' outputs the current time settings from sudo crontab
			and returns minutes/hours for each time setting. This is printed to the page.
			
			'get_runtime.py' returns the run time settings for each zone. 
			This is printed to the page. */
			
			$cmd = escapeshellcmd("sudo ".$directory."get_old_cron.py");
			$output = shell_exec($cmd); 	/* output is in format: minute0 hour0 minute1 hour1 */
			$out_arr = preg_split("/[\s,]+/",$output); /* split into array using spaces as delimiters */
			
			for($i=0; ($i<count($out_arr)-1); $i++){
				if($i%2 !== 0){ 		/* hours are on odd indices */
					echo($out_arr[$i]);	/* output hour */
					echo(":".$out_arr[$i-1]." &nbsp&nbsp"); /* output colon + minute */
				}
			}
			
			$cmd2 = escapeshellcmd($directory."get_runtime.py");
			$output2 = shell_exec($cmd2); 		/* output is in format runtime0 runtime1 runtime2 */
			$out_arr2 = preg_split("/[\s,]+/",$output2); /* split on spaces */
			
			echo("<br><br>Run Times:<br><br>");
			for($i=0; ($i<count($out_arr2)-1); $i++){ 	/* output 'Zone i' */
				echo("Zone ".$i."&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
			}
			echo("<br>");
			for($i=0; ($i<count($out_arr2)-1); $i++){	/* output each zone's runtime */
				echo($out_arr2[$i]." min.&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp");
			}
			?>
			
		</font>
		</p>

		<!-- This section will get user inputted information about runtimes and schedule time -->
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
