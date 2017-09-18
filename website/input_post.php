
<!-- input_post.php -->

<html>
	<head>
		<meta charset = "utf-8"/>
		<title>Submission Successful!</title>
	</head>
	<body>
	<font size="+5">

	<?php

	/* Working directory for scripts accessed by this website
		Scripts used by this file:
		set_cron.py
		det_fstop_on_update.py
		set_runtime.py
	*/
	
	$directory = "/home/pi/irrigation_system/";

	
	/* set_cron.py will set schedule for sprinkler on root crontab with times inputted from cmd line. 
		det_fstop_on_update.py will determine, if after new times are submitted, that the sprinkler system
		needs to be force stopped. This means that if the system is currently running while the schedule
		has changed, and the updated times are outside the old time, the system will turn off.
	*/
	
	$cmd = escapeshellcmd("sudo ".$directory."set_cron.py ");
	$cmd3 = escapeshellcmd("sudo ".$directory."det_fstop_on_update.py ");
	
	/* appends each time from times[] to cmd and cmd3, they will be inputs for 
		set_cron.py and det_fstop_on_update.py */
	$count = 0;
	foreach($_POST[times] as $item){
		if(is_numeric($item)){
			$cmd .= $item;
			$cmd .= " ";
			$cmd3 .= $item;
			$cmd3 .= " ";
		}
		if(strpos($item,":")==true){
			$item_split = explode(":",$item);
			if(is_numeric($item_split[0])&&is_numeric($item_split[1])&&
							    count($item_split)==2){
				$cmd.=$item;
				$cmd.= " ";
				$cmd3 .= $item;
				$cmd3 .= " ";
			}
		}

	}
	
	/* execute set_cron.py and det_fstop_on_update.py scripts */
	$output = shell_exec($cmd);
	$output = shell_exec($cmd3);

	/* set_runtime.py will set the run time for each zone in the config file for the system
		with times inputted from cmd line. */
		
	/* appends times to set_runtime.py from runtime[] */
	$cmd2 = escapeshellcmd("sudo ".$directory."set_runtime.py ");
	foreach($_POST[runtime] as $item){
		if(is_numeric($item)){
			$cmd2 .= $item;
			$cmd2 .= " ";
		}
	}
	/* execute set_runtime.py */
	$output2 = shell_exec($cmd2);
	?>

	<!-- This section displays the updates made to schedule to the user -->
	Irrigaton Schedule Updated!<br><br>Added Times:<br>
	<?php foreach($_POST[times] as $i){
		if(is_numeric($i)&&strlen($i)==4 || strlen($i)==3){

			if(strlen($i) == 3){
				$hour = substr($i,0,1);
				$min = substr($i,1,3);
				echo(substr($hour,0).":".$min."<br>");
			}
			else{
				$hour = substr($i,0,2);
				$min = substr($i,2,4);

				/* checks for if time is in the PM and displays this */
				if(intval($hour)>12){
					echo($hour.":".$min);
					echo(" (".(intval($hour)%12).":".$min." PM)<br>");
				}
				elseif(intval($hour) == 12){
					echo($hour.":".$min."<br>");
				}
				else{
					echo(substr($hour,1).":".$min."<br>");
				}
			}
		}

		if(strpos($i,":")==true){
			$item_split = explode(":",$i);
			if(is_numeric($item_split[0])&&is_numeric($item_split[1])&&count($item_split)==2){
				if(intval($item_split[0])>12){
					echo($item_split[0].":".$item_split[1]);
					echo(" (".(intval($item_split[0])%12).":".$item_split[1]." PM)<br>");
				}
				else{
					echo($item_split[0].":".$item_split[1]."<br>");
				}
			}
		}
	}
	
	/* Displays changes made to runtime to the user */
	echo("<br>Run Times:<br>");
	$count = 0;
	foreach($_POST[runtime] as $t){
		if(is_numeric($t)){
			echo("Zone ".$count.": ".$t." minutes<br>");
		}
		$count++;
	}
	?>
	</font>
	</body>
</html>

