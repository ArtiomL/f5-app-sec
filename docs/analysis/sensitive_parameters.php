<?php

$sensitive_param_count = 0;
$sensitive_default = 0;

foreach ($sensitive_param as $var)
{
	$sensitive_param_count++;
	if($var['name']=="password")
		$sensitive_default=1;
}

if ($sensitive_param_count==1 && $sensitive_default==1)
{
	$new_suggestion =  array (
		'severity' => $viol_list['sensitive_param']['severity'],
		'section' => $viol_list['sensitive_param']['section'],
		'score' =>$viol_list['sensitive_param']['score'],
		'txt'=>$viol_list['sensitive_param']['txt']
	);
	array_push($suggestions, $new_suggestion);	
}

?>