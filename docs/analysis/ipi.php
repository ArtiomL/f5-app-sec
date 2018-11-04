<?php

if ($analyze_ipi=="yes")
{
	$ipi_services_disabled = 0;
	$ipi_services_total = 0;
	
	if ($ipi_enabled !='"Yes"')
	{
		$new_suggestion =  array (
			'severity' => $viol_list['ipi_enabled']['severity'],
			'section' => $viol_list['ipi_enabled']['section'],
			'score' =>$viol_list['ipi_enabled']['score'],
			'txt'=>$viol_list['ipi_enabled']['txt']
		);
		array_push($suggestions, $new_suggestion);		
	}
	
	foreach ($ipi_categories as $var)
	{
		$ipi_services_total++;
		if($var['block']=="No")
			$ipi_services_disabled++;
	}
	if ($ipi_services_disabled>0 && $ipi_services_disabled<$ipi_services_total && $ipi_enabled=='"Yes"')
	{
		$new_suggestion =  array (
			'severity' => $viol_list['ipi_services_disabled']['severity'],
			'section' => $viol_list['ipi_services_disabled']['section'],
			'score' =>$viol_list['ipi_services_disabled']['score'],
			'txt'=>$ipi_services_disabled . ' ' .$viol_list['ipi_services_disabled']['txt']
		);
		array_push($suggestions, $new_suggestion);	
	}
	
	if ($ipi_services_disabled==$ipi_services_total && $ipi_enabled=='"Yes"')
	{
		$new_suggestion =  array (
			'severity' => $viol_list['all_ipi_services_disabled']['severity'],
			'section' => $viol_list['all_ipi_services_disabled']['section'],
			'score' =>$viol_list['all_ipi_services_disabled']['score'],
			'txt'=>$viol_list['all_ipi_services_disabled']['txt']
		);
		array_push($suggestions, $new_suggestion);	
	}	
}

?>