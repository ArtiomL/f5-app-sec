<?php 
if(!isset($_GET["policy"]))
{
	exit();
}	
$config_files = "config_files/" . $_GET["policy"];
$string = file_get_contents($config_files."/suggestions.txt");
$suggestions_initial = json_decode($string, true);

$error = 0;
$info = 0;
$warning = 0;
$score = 0;
foreach ($suggestions_initial as $key) {
	if ($key['severity'] == 'info')
		$info++;
	if ($key['severity'] == 'warning')
		$warning++;
	if ($key['severity'] == 'error')
		$error++;
	$score = $score + (int)$key['score'];
}
$final_score = 100 - $score;
	

echo '{"error":"'.$error.'","warning":"'.$warning.'","info":"'.$info.'","score":'.$score.'}';
	
?>
