<?php


$file_type_allowed_count=0;
$file_type_disallowed_count=0;

foreach ($file_types_allowed as $var)
{
	$file_type_allowed_count++;
	if($var['name']=="*" && $analyze_file_types == 1)
	{
		$new_suggestion =  array (
			'severity' => $viol_list['file_type_star']['severity'],
			'section' => $viol_list['file_type_star']['section'],
			'score' =>$viol_list['file_type_star']['score'],
			'txt'=>$viol_list['file_type_star']['txt']
		);
		array_push($suggestions, $new_suggestion);
	}
		
	if($var['performStaging']=="Yes" && $analyze_file_type_lengths == 1)
	{
		$new_suggestion =  array (
			'severity' => $viol_list['file_type_staging']['severity'],
			'section' => $viol_list['file_type_staging']['section'],
			'score' =>$viol_list['file_type_staging']['score'],
			'txt'=>'".'. $var['name'].'" '.$viol_list['file_type_staging']['txt']
		);
		array_push($suggestions, $new_suggestion);
	}
}

foreach ($file_types_disallowed as $var)
{
	$file_type_disallowed_count++;
}

if ($file_type_disallowed_count>0 && $file_type_allowed_count>1 && $analyze_file_types == 1)
{
	$new_suggestion =  array (
		'severity' => $viol_list['both_file_types']['severity'],
		'section' => $viol_list['both_file_types']['section'],
		'score' =>$viol_list['both_file_types']['score'],
		'txt'=>$viol_list['both_file_types']['txt']
	);
	array_push($suggestions, $new_suggestion);
}		



?>