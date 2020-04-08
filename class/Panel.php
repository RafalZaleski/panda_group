<?php
	require_once 'class/InterfaceSimple.php';
	
	class Panel implements InterfaceSimple
	{
		static function Save(int $id = 0)
		{
			require_once 'class/Users.php';
			Users::Save($_POST);
			self::Edit();
		}
		
		static function Edit(int $id = 0)
		{
			require_once 'class/Users.php';
			$dane = Users::Get($id > 0 ? $id : $GLOBALS['USER_ID']);
			$html = file_get_contents('template/form_dane.html');
			if($dane['gender']==1)
			{
				$dane['genderboy'] = 'selected';
				$dane['gendergirl'] = '';
			}
			else if($dane['gender'] == 2)
			{
				$dane['genderboy'] = '';
				$dane['gendergirl'] = 'selected';
			}
			else
			{
				$dane['genderboy'] = '';
				$dane['gendergirl'] = '';
			}
			
			foreach($dane as $key => $val)
			{
				$html = str_replace('#'.$key.'#',$val,$html);
			}
			if(isset($dane['updated_at']) && $dane['updated_at'] == '0000-00-00 00:00:00') $html = removeFieldFromHtml($html,'updated_at');
			
			addTopHtml();
			echo $html;
			echo file_get_contents('template/footer.html');
		}
		
		static function Del(int $id = 0)
		{
			
		}
		
		static function Get(int $id = 0)
		{
			
		}
		
	}
	
?>