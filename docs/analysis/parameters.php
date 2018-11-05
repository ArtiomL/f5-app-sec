<?php

$param_staging=0;
$param_sig_overrides=0;
$param_sig_disabled=0;
$param_file_uploads=0;

foreach ($parameters as $var)
{
	if ($var['performStaging'] == "Yes" && $var['name'] == "*")
	{
		$new_suggestion =  array (
			'severity' => $viol_list['param_star_staging']['severity'],
			'section' => $viol_list['param_star_staging']['section'],
			'score' =>$viol_list['param_star_staging']['score'],
			'txt'=>$viol_list['param_star_staging']['txt']
		);
		array_push($suggestions, $new_suggestion);
	}
	if ($var['dataType'] == "binary")
	{
		$param_file_uploads++;
	}
	if ($var['performStaging'] == "Yes" && $var['name'] != "*")
	{
		$param_staging++;
	}	
	if ($var['num_of_sign_overides'] >0 && $var['attackSignaturesCheck'] == "Yes")
	{
		$param_sig_overrides++;
	}
	if ($var['attackSignaturesCheck'] == "No" && $var['name'] == "*")
	{
		$new_suggestion =  array (
			'severity' => $viol_list['param_star_sig_disabled']['severity'],
			'section' => $viol_list['param_star_sig_disabled']['section'],
			'score' =>$viol_list['param_star_sig_disabled']['score'],
			'txt'=>$viol_list['param_star_sig_disabled']['txt']
		);
		array_push($suggestions, $new_suggestion);
	}
	if ($var['attackSignaturesCheck'] == "No" && $var['name'] != "*" && $var['valueType']!="ignore" && $var['valueType']!="xml" && $var['valueType']!="json" && $var['valueType']!="dynamic-content" && $var['valueType']!="static-content")
	{
		$param_sig_disabled++;
	}		
}

if($param_sig_overrides>0)
{
	$new_suggestion =  array (
		'severity' => $viol_list['param_sig_overrides']['severity'],
		'section' => $viol_list['param_sig_overrides']['section'],
		'score' =>$viol_list['param_sig_overrides']['score'],
		'txt'=>$param_sig_overrides. ' ' . $viol_list['param_sig_overrides']['txt']
	);
	array_push($suggestions, $new_suggestion);
}
if($param_sig_disabled>0)
{
	$new_suggestion =  array (
		'severity' => $viol_list['param_sig_disabled']['severity'],
		'section' => $viol_list['param_sig_disabled']['section'],
		'score' =>$viol_list['param_sig_disabled']['score'],
		'txt'=>$param_sig_disabled. ' ' . $viol_list['param_sig_disabled']['txt']
	);
	array_push($suggestions, $new_suggestion);
}

if($param_staging>0)
{
	$new_suggestion =  array (
		'severity' => $viol_list['param_staging']['severity'],
		'section' => $viol_list['param_staging']['section'],
		'score' =>$viol_list['param_staging']['score'],
		'txt'=>$param_staging. ' ' . $viol_list['param_staging']['txt']
	);
	array_push($suggestions, $new_suggestion);
}
if($param_file_uploads==0 && $analyze_file_uploads=="yes")
{
	$new_suggestion =  array (
		'severity' => $viol_list['param_file_uploads']['severity'],
		'section' => $viol_list['param_file_uploads']['section'],
		'score' =>$viol_list['param_file_uploads']['score'],
		'txt'=>$viol_list['param_file_uploads']['txt']
	);
	array_push($suggestions, $new_suggestion);	
}



?>