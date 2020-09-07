<?php
function validate_address($address) {
	global $address_regexp;
	if(preg_match($address_regexp,$address)) return TRUE;
	else return FALSE;
}

function set_variable($name,$value) {
	$name_escaped=db_escape($name);
	$value_escaped=db_escape($value);
	db_query("INSERT INTO `variables` (`name`,`value`) VALUES ('$name_escaped','$value_escaped')
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)");
}
?>
