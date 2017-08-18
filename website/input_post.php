<html>
	<head>
		<meta charset = "utf-8"/>
		<title>Submission Successful!</title>
	</head>
	<body>
	<font size="+5">



	<?php

	$directory = "/home/pi/irrigation_system/";

	$cmd = escapeshellcmd("sudo ".$directory."pythoncron.py ");
	$cmd3 = escapeshellcmd("sudo ".$directory."det_fstop_on_update.py ");
	$count = 0;
	foreach($_POST[times] as $item){
		if(is_numeric($item)){
			$cmd .= $item;
			$cmd .= " ";
			$cmd3 .= $item;
			$cmd3 .= " ";
		}
		if(strpos($item,":")!==false){
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
	$output = shell_exec($cmd);
	$output = shell_exec($cmd3);

	$cmd2 = escapeshellcmd("sudo ".$directory."set_runtime.py ");
	foreach($_POST[runtime] as $item){
		if(is_numeric($item)){
			$cmd2 .= $item;
			$cmd2 .= " ";
		}
	}
	$output2 = shell_exec($cmd2);
	?>

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

		if(strpos($i,":")!==false){
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

