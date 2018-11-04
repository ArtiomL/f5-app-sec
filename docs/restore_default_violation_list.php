<?php

$default_list =file_get_contents('violation_list_default.json');
file_put_contents('violation_list.json', $default_list);

echo "Operation successfull"	


?>