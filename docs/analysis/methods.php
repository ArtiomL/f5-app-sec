<?php

if ($analyze_methods=="yes")
{	
	if (count($methods)>8)
	{
		$new_suggestion =  array (
			'severity' => $viol_list['methods_count']['severity'],
			'section' => $viol_list['methods_count']['section'],
			'score' =>$viol_list['methods_count']['score'],
			'txt'=>$viol_list['methods_count']['txt']
		);
		array_push($suggestions, $new_suggestion);		
	}
	foreach ($methods as $var)
	{
		if ($var['name'] == "DELETE")
		{
			$new_suggestion =  array (
				'severity' => $viol_list['delete_method']['severity'],
				'section' => $viol_list['delete_method']['section'],
				'score' =>$viol_list['delete_method']['score'],
				'txt'=>$viol_list['delete_method']['txt']
			);
			array_push($suggestions, $new_suggestion);	
		}
	}
}
?>