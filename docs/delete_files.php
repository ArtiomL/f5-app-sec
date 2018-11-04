<?php 

$dir = getcwd() . '/reports/';
$scan = scandir($dir);

foreach($scan as $file)
{
	if (!is_dir($dir.$file))
    {
		unlink($dir.$file);
    }

}
?>