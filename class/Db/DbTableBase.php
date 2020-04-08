<?php

	class DbTableBase
	{
		private $data = array();
		protected $TABLE_NAME = '';
		protected $COL_NAME = [];
		protected $COL_LIMIT = [];
		
		function __construct($data = array(), int $id = 0)
		{
			if($id > 0)
				$data = $this->GetDataById($id);
			
			foreach($this->COL_NAME as $val) $this->Set($val, $data[$val] ?? null);
		}
		
		public function Set($name, $val)
		{
			if(in_array($name,$this->COL_NAME))
			{
				$limit = $this->COL_LIMIT[array_keys($this->COL_NAME,$name)[0]];
				if(intval($limit))
				{
					if($val < $limit)
						$this->data[$name] = $val;
					else
						$this->data[$name] = $limit;
					
					return true;
				}
				else
				{
					if(substr($limit,0,1) == 's')
					{
						$limit = (int)substr($limit,1);
						if(strlen($val) < $limit)
							$this->data[$name] = $val;
						else
							$this->data[$name] = substr($val,0,$limit);
						
						return true;
					}
				}
			}
			
			return false;
		}
		
		public function Get($name)
		{
			return $this->data[name];
		}
		
		public function GetAll()
		{
			$data = array();
			foreach($this->data as $key => $val) $data[$key] = $val; 
			return $data;
		}
			
		public function AddNewRowDb($except = array('id'))
		{	
			$data = $this->data;
			if($data['id'] > 0) return $this->UpdateRowDb($except);
			unset($data['id']);
			$sql_val = '';
			foreach($data as $val) $sql_val .= '"'.esc($val).'",';
			$sql = 'INSERT INTO ' . $this->TABLE_NAME . '(' . implode(',',array_keys($data)) . ') VALUES(' . substr($sql_val, 0, -1) . ')';
			if(query($sql)) return true;
			
			return false;
		}
		
		public function UpdateRowDb($except)
		{	
			$data = $this->data;
			foreach($except as $val)
			{
				$$val = $data[$val];
				unset($data[$val]);
			}
			$sql_val = '';
			foreach($data as $key => $val) $sql_val .= $key . '="'.esc($val).'",';
			
			$sql = 'UPDATE ' . $this->TABLE_NAME . ' SET ' . mb_substr($sql_val, 0, -1) . ' WHERE id=' . esc($id) . ' LIMIT 1';
			if(query($sql)) return true;
			
			return false;
		}
		
		public function GetDataById(int $id)
		{
			if($id > 0)
			{
				$sql = 'SELECT ' . implode(',',$this->COL_NAME) . ' FROM ' . $this->TABLE_NAME . ' WHERE id IN(' . esc($id) . ') LIMIT 1';
				if($res = query($sql)) return fetchAssoc($res);
			}
			
			return false;
		}
		
		public function DeleteRowById(int $id)
		{
			if($id > 0)
			{
				$sql = 'DELETE FROM ' . $this->TABLE_NAME . ' WHERE id=' . esc($id) . ' LIMIT 1';
				if(query($sql)) return true;
			}
			
			return false;
		}
		
	}
	
?>