<?php
	require_once 'class/Db/DbTableBase.php';
	
	class DbUsers extends DbTableBase
	{
		private $data = array();
		protected $TABLE_NAME = 'users';
		protected $COL_NAME = ['id','first_name','last_name','email','gender','is_active','password','created_at','updated_at'];
		protected $COL_LIMIT = [16777214,'s100','s100','s100',2,2,'s100','s19','s19'];
	}
	
?>