<?php


foreach ($signature_sets as $var)
{
	if ($var['learn'] == "No")
	{
		$new_suggestion =  array (
			'severity' => $viol_list['sig_set_learn']['severity'],
			'section' => $viol_list['sig_set_learn']['section'],
			'score' =>$viol_list['sig_set_learn']['score'],
			'txt'=>'"'.$var['name'].'" '.$viol_list['sig_set_learn']['txt']
		);
		array_push($suggestions, $new_suggestion);
	}
	if ($var['alarm'] == "No")
	{
		$new_suggestion =  array (
			'severity' => $viol_list['sig_set_alarm']['severity'],
			'section' => $viol_list['sig_set_alarm']['section'],
			'score' =>$viol_list['sig_set_alarm']['score'],
			'txt'=>'"'.$var['name'].'" '.$viol_list['sig_set_alarm']['txt']
		);
		array_push($suggestions, $new_suggestion);
	}
	if ($var['block'] == "No")
	{
		$new_suggestion =  array (
			'severity' => $viol_list['sig_set_block']['severity'],
			'section' => $viol_list['sig_set_block']['section'],
			'score' =>$viol_list['sig_set_block']['score'],
			'txt'=>'"'.$var['name'].'" '.$viol_list['sig_set_block']['txt']
		);
		array_push($suggestions, $new_suggestion);
	}	
}

if ($signatures_overview['enabled'] < 30 && $signatures_overview['enabled'] > 0)
{
	$new_suggestion =  array (
		'severity' => $viol_list['sig_disabled_low']['severity'],
		'section' => $viol_list['sig_disabled_low']['section'],
		'score' =>$viol_list['sig_disabled_low']['score'],
		'txt'=>$signatures_overview['enabled'].' ' .$viol_list['sig_disabled_low']['txt']
	);
	array_push($suggestions, $new_suggestion);	
}

if ($signatures_overview['enabled'] >= 30 && $signatures_overview['enabled'] < 250)
{
	$new_suggestion =  array (
		'severity' => $viol_list['sig_disabled_medium']['severity'],
		'section' => $viol_list['sig_disabled_medium']['section'],
		'score' =>$viol_list['sig_disabled_medium']['score'],
		'txt'=>$signatures_overview['enabled'].' ' .$viol_list['sig_disabled_medium']['txt']
	);
	array_push($suggestions, $new_suggestion);	
}
if ($signatures_overview['enabled'] >= 250 && $signatures_overview['enabled'] < $signatures_overview['total'] )
{
	$new_suggestion =  array (
		'severity' => $viol_list['sig_disabled_high']['severity'],
		'section' => $viol_list['sig_disabled_high']['section'],
		'score' =>$viol_list['sig_disabled_high']['score'],
		'txt'=>$signatures_overview['enabled'].' ' .$viol_list['sig_disabled_high']['txt']
	);
	array_push($suggestions, $new_suggestion);	
}

if ($signatures_overview['enabled'] == $signatures_overview['total'] )
{
	$new_suggestion =  array (
		'severity' => $viol_list['sig_disabled_all']['severity'],
		'section' => $viol_list['sig_disabled_all']['section'],
		'score' =>$viol_list['sig_disabled_all']['score'],
		'txt'=>$viol_list['sig_disabled_all']['txt']
	);
	array_push($suggestions, $new_suggestion);	
}



if ($signatures_overview['staging'] < 30 && $signatures_overview['staging'] > 0)
{
	$new_suggestion =  array (
		'severity' => $viol_list['sig_staging_low']['severity'],
		'section' => $viol_list['sig_staging_low']['section'],
		'score' =>$viol_list['sig_staging_low']['score'],
		'txt'=>$signatures_overview['staging'].' ' .$viol_list['sig_staging_low']['txt']
	);
	array_push($suggestions, $new_suggestion);	
}

if ($signatures_overview['staging'] >= 30 && $signatures_overview['staging'] < 250)
{
	$new_suggestion =  array (
		'severity' => $viol_list['sig_staging_medium']['severity'],
		'section' => $viol_list['sig_staging_medium']['section'],
		'score' =>$viol_list['sig_staging_medium']['score'],
		'txt'=>$signatures_overview['staging'].' ' .$viol_list['sig_staging_medium']['txt']
	);
	array_push($suggestions, $new_suggestion);	
}
if ($signatures_overview['staging'] >= 250 && $signatures_overview['staging'] < $signatures_overview['total'] )
{
	$new_suggestion =  array (
		'severity' => $viol_list['sig_staging_high']['severity'],
		'section' => $viol_list['sig_staging_high']['section'],
		'score' =>$viol_list['sig_staging_high']['score'],
		'txt'=>$signatures_overview['staging'].' ' .$viol_list['sig_staging_high']['txt']
	);
	array_push($suggestions, $new_suggestion);	
}

if ($signatures_overview['staging'] == $signatures_overview['total'] )
{
	$new_suggestion =  array (
		'severity' => $viol_list['sig_staging_all']['severity'],
		'section' => $viol_list['sig_staging_all']['section'],
		'score' =>$viol_list['sig_staging_all']['score'],
		'txt'=>$viol_list['sig_staging_all']['txt']
	);
	array_push($suggestions, $new_suggestion);	
}

if ($signatures_overview['signatureStaging'] != "Yes")
{
	$new_suggestion =  array (
		'severity' => $viol_list['signatureStaging']['severity'],
		'section' => $viol_list['signatureStaging']['section'],
		'score' =>$viol_list['signatureStaging']['score'],
		'txt'=>$viol_list['signatureStaging']['txt']
	);
	array_push($suggestions, $new_suggestion);	
}



?>