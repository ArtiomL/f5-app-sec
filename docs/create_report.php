<?php


if (isset($_GET["report"])) {
    if($_GET["report"]=="ltm")
	{
		$command = escapeshellcmd('create_ltm.py');
		$output = shell_exec($command);
//		sleep(8);
		if(!(strpos($output, 'ok') !== false))
		{
			header('HTTP/1.1 501 Script Error');
		}
	}
    if($_GET["report"]=="asm")
	{
		$command = escapeshellcmd('create_asm.py');
		$output = shell_exec($command);
//		sleep(8);
		if(!(strpos($output, 'ok') !== false))
		{
			 header('HTTP/1.1 501 Script Error');
		}
	}
    if($_GET["report"]=="ltm_asm")
	{
		$command = escapeshellcmd('create_ltm_asm.py');
		$output = shell_exec($command);
//		sleep(8);
		if(!(strpos($output, 'ok') !== false))
		{
			 header('HTTP/1.1 501 Script Error');
		}
	}	
}
else{  
    header('HTTP/1.1 500 No report set');
}

?>