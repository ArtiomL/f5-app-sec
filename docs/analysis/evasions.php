<?php

	$evasions_disabled = 0;
	$evasions_total = 0;
	
	foreach ($evasions as $var)
	{
		$evasions_total++;
		if($var['enabled']=="No")
			$evasions_disabled++;
	}
	if ($evasions_disabled>0 && $evasions_disabled<$evasions_total)
	{
		$new_suggestion =  array (
			'severity' => $viol_list['evasions_disabled']['severity'],
			'section' => $viol_list['evasions_disabled']['section'],
			'score' =>$viol_list['evasions_disabled']['score'],
			'txt'=>$evasions_disabled . ' ' .$viol_list['evasions_disabled']['txt']
		);
		array_push($suggestions, $new_suggestion);	
	}
	if ($evasions_disabled==$evasions_total)
	{
		$new_suggestion =  array (
			'severity' => $viol_list['all_evasions_disabled']['severity'],
			'section' => $viol_list['all_evasions_disabled']['section'],
			'score' =>$viol_list['all_evasions_disabled']['score'],
			'txt'=>$viol_list['all_evasions_disabled']['txt']
		);
		array_push($suggestions, $new_suggestion);	
	}

?>