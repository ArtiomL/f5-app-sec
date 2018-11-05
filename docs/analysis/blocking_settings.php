<?php

foreach ($blocking_settings as $var)
{
	if (in_array($var['name'], $violations_high))
	{
		
		if ($var['block']=="No" && $var['alarm']=="No" && $var['learn']!="Yes")
		{
			$new_suggestion =  array (
				'severity' => $viol_list['violation_disabled']['severity'],
				'section' => $viol_list['violation_disabled']['section'],
				'score' =>$viol_list['violation_disabled']['score'],
				'txt'=>'"'. $var['name'] .'" '. $viol_list['violation_disabled']['txt']
			);
			array_push($suggestions, $new_suggestion);		
		}
		else
		{
			if ($var['block']=="No")
			{
				$new_suggestion =  array (
					'severity' => $viol_list['violation_block']['severity'],
					'section' => $viol_list['violation_block']['section'],
					'score' =>$viol_list['violation_block']['score'],
					'txt'=>'"'. $var['name']. '" '. $viol_list['violation_block']['txt']
				);
				array_push($suggestions, $new_suggestion);		
			}			
			if ($var['alarm']=="No")
			{
				$new_suggestion =  array (
					'severity' => $viol_list['violation_alarm']['severity'],
					'section' => $viol_list['violation_alarm']['section'],
					'score' =>$viol_list['violation_alarm']['score'],
					'txt'=>'"'. $var['name']. '" '. $viol_list['violation_alarm']['txt']
				);
				array_push($suggestions, $new_suggestion);		
			}				
			if ($var['learn']=="No")
			{
				$new_suggestion =  array (
					'severity' => $viol_list['violation_learn']['severity'],
					'section' => $viol_list['violation_learn']['section'],
					'score' =>$viol_list['violation_learn']['score'],
					'txt'=>'"'. $var['name']. '" '. $viol_list['violation_learn']['txt']
				);
				array_push($suggestions, $new_suggestion);		
			}				
		}
	}
	if (in_array($var['name'], $violations_low))
	{
		if ($var['block']=="No" && $var['alarm']=="No" && $var['learn']!="Yes")
		{
			$new_suggestion =  array (
				'severity' => "warning",
				'section' => $viol_list['violation_disabled']['section'],
				'score' =>1,
				'txt'=>'"'. $var['name']. '" '. $viol_list['violation_disabled']['txt']
			);
			array_push($suggestions, $new_suggestion);		
		}
		else
		{
			if ($var['block']=="No")
			{
				$new_suggestion =  array (
					'severity' => "warning",
					'section' => $viol_list['violation_block']['section'],
					'score' =>1,
					'txt'=>'"'. $var['name'] .'" '. $viol_list['violation_block']['txt']
				);
				array_push($suggestions, $new_suggestion);		
			}			
			if ($var['alarm']=="No")
			{
				$new_suggestion =  array (
					'severity' => $viol_list['violation_alarm']['severity'],
					'section' => $viol_list['violation_alarm']['section'],
					'score' =>$viol_list['violation_alarm']['score'],
					'txt'=>'"'. $var['name']. '" '. $viol_list['violation_alarm']['txt']
				);
				array_push($suggestions, $new_suggestion);		
			}				
			if ($var['learn']=="No")
			{
				$new_suggestion =  array (
					'severity' => $viol_list['violation_learn']['severity'],
					'section' => $viol_list['violation_learn']['section'],
					'score' =>$viol_list['violation_learn']['score'],
					'txt'=>'"'. $var['name']. '" '. $viol_list['violation_learn']['txt']
				);
				array_push($suggestions, $new_suggestion);		
			}				
		}
	}
}


?>