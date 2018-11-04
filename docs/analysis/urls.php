<?php

$url_staging=0;
$url_sig_overrides=0;
$url_sig_disabled=0;

foreach ($urls as $var)
{
	if ($var['performStaging'] == "Yes" && $var['name'] == "*")
	{
		$new_suggestion =  array (
			'severity' => $viol_list['url_star_staging']['severity'],
			'section' => $viol_list['url_star_staging']['section'],
			'score' =>$viol_list['url_star_staging']['score'],
			'txt'=>$viol_list['url_star_staging']['txt']
		);
		array_push($suggestions, $new_suggestion);
	}
	if ($var['performStaging'] == "Yes" && $var['name'] != "*")
	{
		$url_staging++;
	}	
	if ($var['num_of_sign_overides'] >0 && $var['attackSignaturesCheck'] == "Yes")
	{
		$url_sig_overrides++;
	}
	if ($var['attackSignaturesCheck'] == "No" && $var['name'] == "*")
	{
		$new_suggestion =  array (
			'severity' => $viol_list['url_star_sig_disabled']['severity'],
			'section' => $viol_list['url_star_sig_disabled']['section'],
			'score' =>$viol_list['url_star_sig_disabled']['score'],
			'txt'=>$viol_list['url_star_sig_disabled']['txt']
		);
		array_push($suggestions, $new_suggestion);
	}
	if ($var['attackSignaturesCheck'] == "No" && $var['name'] != "*")
	{
		$url_sig_disabled++;
	}		
}

if($url_sig_overrides>0)
{
	$new_suggestion =  array (
		'severity' => $viol_list['url_sig_overrides']['severity'],
		'section' => $viol_list['url_sig_overrides']['section'],
		'score' =>$viol_list['url_sig_overrides']['score'],
		'txt'=>$url_sig_overrides. ' ' . $viol_list['url_sig_overrides']['txt']
	);
	array_push($suggestions, $new_suggestion);
}
if($url_sig_disabled>0)
{
	$new_suggestion =  array (
		'severity' => $viol_list['url_sig_disabled']['severity'],
		'section' => $viol_list['url_sig_disabled']['section'],
		'score' =>$viol_list['url_sig_disabled']['score'],
		'txt'=>$url_sig_disabled. ' ' . $viol_list['url_sig_disabled']['txt']
	);
	array_push($suggestions, $new_suggestion);
}

if($url_staging>0)
{
	$new_suggestion =  array (
		'severity' => $viol_list['url_staging']['severity'],
		'section' => $viol_list['url_staging']['section'],
		'score' =>$viol_list['url_staging']['score'],
		'txt'=>$url_staging. ' ' . $viol_list['url_staging']['txt']
	);
	array_push($suggestions, $new_suggestion);
}


?>