<?php
if ($analyze_response_codes=="yes")
{
	if (count($response_codes)>12)
	{
		$new_suggestion =  array (
			'severity' => $viol_list['methods_count']['severity'],
			'section' => $viol_list['methods_count']['section'],
			'score' =>$viol_list['methods_count']['score'],
			'txt'=>$viol_list['methods_count']['txt']
		);
		array_push($suggestions, $new_suggestion);		
	}
}

?>