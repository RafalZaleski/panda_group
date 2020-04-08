<?php
		
	require_once 'engine.php';
	
	if($GLOBALS['SESSION'])
	{
		$allow_method = ['Get','Save','Del','Edit','Signin','Logout','RegistrationForm','ChangePasswordForm','ResetPasswordForm','Registration','ChangePassword','ResetPassword'];
		$allow_class = ['Login','Panel','Users','News','Csv'];
	}
	else
	{
		$allow_method = ['Get','Signin','RegistrationForm','Registration','ResetPasswordForm','ResetPassword'];
		$allow_class = ['Login','News'];
	}
	
	if(isset($_REQUEST['action']) && $_REQUEST['action'] != '' && in_array($_REQUEST['action'],$allow_method))
		$function = $_REQUEST['action'];
	else
		$function = 'Get'; //gdy coś się nie zgadza to wczytanie formularza do logowania + button do rejestracji
	
	if(isset($_REQUEST['base']) && $_REQUEST['base'] != '' && in_array($_REQUEST['base'],$allow_class))
		$class = $_REQUEST['base'];
	else
		$class = 'Login'; //gdy coś się nie zgadza to wczytanie formularza do logowania + button do rejestracji
	
	require_once 'class/' . $class . '.php';
	
	$id = $_REQUEST['id'] ?? 0;
	
	$class::$function($id);

?>