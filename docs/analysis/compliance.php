<?php

	$compliance_disabled = 0;
	$compliance_total = 0;
	
	foreach ($compliance as $var)
	{
		$compliance_total++;
		if($var['enabled']=="No")
			$compliance_disabled++;
	}
	if ($compliance_disabled>0 && $compliance_disabled<$compliance_total)
	{
		$new_suggestion =  array (
			'severity' => $viol_list['compliance_disabled']['severity'],
			'section' => $viol_list['compliance_disabled']['section'],
			'score' =>$viol_list['compliance_disabled']['score'],
			'txt'=>$compliance_disabled . ' ' .$viol_list['compliance_disabled']['txt']
		);
		array_push($suggestions, $new_suggestion);	
	}
	if ($compliance_disabled==$compliance_total)
	{
		$new_suggestion =  array (
			'severity' => $viol_list['all_compliance_disabled']['severity'],
			'section' => $viol_list['all_compliance_disabled']['section'],
			'score' =>$viol_list['all_compliance_disabled']['score'],
			'txt'=>$viol_list['all_compliance_disabled']['txt']
		);
		array_push($suggestions, $new_suggestion);	
	}

?>