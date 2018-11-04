<?php 
if(!isset($_GET["policy"]))
{
	exit();
}	
$config_files = "config_files/" . $_GET["policy"];
$string = file_get_contents($config_files."/suggestions.txt");

echo '{"data": '.$string.'}';


?>