<?php
	require_once 'class/InterfaceSimple.php';
	
	class Users implements InterfaceSimple
	{
		static function Save($dane)
		{
			require_once 'class/Db/DbUsers.php';
			$user = new DbUsers($dane);
			if(isset($GLOBALS['USER_ID'])) $user->Set('id',$GLOBALS['USER_ID']);
			$user->Set('updated_at',date('Y-m-d H:i:s'));
			if($user->AddNewRowDb(array('id','email','password','created_at','is_active'))) messege('Zmieniono dane.');
			else messege('Wystąpił błąd podczas zapisu danych.');
			
		}
		
		static function Edit(int $id)
		{
			
		}
		
		static function Del(int $id)
		{
			
		}
		
		static function Get(int $id)
		{
			require_once 'class/Db/DbUsers.php';
			$dane = new DbUsers();
			return $dane->GetDataById($id);
		}
		
	}
	
?>