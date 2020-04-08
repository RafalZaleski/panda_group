<?php
	require_once 'class/Db/DbTableBase.php';
	
	class DbCsv extends DbTableBase
	{
		private $data = array();
		protected $TABLE_NAME = 'csv';
		protected $COL_NAME = ['id','name','content'];
		protected $COL_LIMIT = [16777214,'s100','s65535'];
	}
	
?>