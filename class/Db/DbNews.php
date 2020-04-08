<?php
	require_once 'class/Db/DbTableBase.php';
	
	class DbNews extends DbTableBase
	{
		private $data = array();
		protected $TABLE_NAME = 'news';
		protected $COL_NAME = ['id','name','description','is_active','created_at','updated_at','author_id'];
		protected $COL_LIMIT = [16777214,'s200','s65535','2',2000000000,2000000000,16777214];

		public function GetSpecific(int $sesion, int $offset = 0, int $limit=10)
		{
			$sql = 'SELECT ' . implode(',',$this->COL_NAME) . ' FROM ' . $this->TABLE_NAME . ($sesion ? '' : ' WHERE is_active=1') . ' LIMIT ' . esc($offset) . ',' . esc($limit+$offset);
			if($res = query($sql)) return $res;
		}
	
	}	
?>