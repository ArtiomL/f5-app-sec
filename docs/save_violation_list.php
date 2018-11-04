<?php
sleep(2);

if(!isset($_POST["violation_list"]))
{
	echo "Variables not set correctly";
	exit();
}

$myfile = fopen("violation_list.json", "w") or die("Unable to open file!");
fwrite($myfile, $_POST["violation_list"]);
fclose($myfile);
	


?>