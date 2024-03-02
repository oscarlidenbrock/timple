<?php

	/* Timple (Simple TPL Engine) v. 1.00 #08/11/2013# by Oscar González García (https://oscarlidenbrock.es). All rights reserved. */
	
	require('timple.class.php');
	
	$tpl = new timple();
	
	$html = $tpl->parse('tpl/html.tpl', ['name' => 'Oscar']);
	print $html;
	
?>