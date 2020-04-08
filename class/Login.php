<?php
	require_once 'class/InterfaceSimple.php';
	
	class Login implements InterfaceSimple
	{
		static function Save(int $id = 0)
		{
			
		}
		
		static function Edit(int $id = 0)
		{
			
		}
		
		static function Del(int $id = 0)
		{
			
		}
		
		static function Get(int $id = 0)
		{
			self::SigninForm();
		}
		
		static function SigninForm()
		{
			if($GLOBALS['SESSION'])
			{
				require_once 'class/Panel.php';
				return Panel::Edit($GLOBALS['USER_ID']);
			}
			
			addTopHtml();
			echo file_get_contents('template/form_login.html');
			echo file_get_contents('template/footer.html');
			die();
		}
		
		static function RegistrationForm()
		{
			addTopHtml();
			echo file_get_contents('template/form_rejestration.html');
			echo file_get_contents('template/footer.html');
		}
		
		static function ChangePasswordForm()
		{
			if(isset($_REQUEST['resetPasswordEmail']) && $_REQUEST['resetPasswordEmail'] != '')
			{
				$dane = fetchAssoc(query('SELECT id,email FROM users WHERE code_reset_password="'.esc($_REQUEST['resetPasswordEmail']).'" LIMIT 1'));
				$dane['resetPasswordEmail'] = $_REQUEST['resetPasswordEmail'];
			}
			else if(isset($GLOBALS['USER_ID']))
			{
				$dane['email'] = fastQuery('SELECT email FROM users WHERE id="'.esc($GLOBALS['USER_ID']).'"');
				$dane['id'] = $GLOBALS['USER_ID'];
				$dane['resetPasswordEmail'] = '';
			}
			else
			{
				return self::SigninForm();
			}
			
			if(!$dane['id'])
				return self::SigninForm();
			
			addTopHtml();
			$html = file_get_contents('template/form_change_password.html');
					
			foreach($dane as $key => $val)
			{
				$html = str_replace('#'.$key.'#',$val,$html);
			}
			
			if($dane['resetPasswordEmail'] != '')
				$html = removeFieldFromHtml($html,'old_password');
			
			echo $html;
			echo file_get_contents('template/footer.html');
			die();
		}
		
		static function ResetPasswordForm()
		{
			addTopHtml();
			echo file_get_contents('template/form_reset_password.html');
			echo file_get_contents('template/footer.html');
		}
		
		//gdy jest wysłane zapytanie o logowanie sprawdzamy w bazie czy dane się zgadzają (bronić przed sql injection) - generujemy kod sesji - zapisujemy go do bazy dla tego użytkowniak, zapisujmey ciasteczko u użytkownika, zapisujemy ip użytkowniak w bazie, przy każdej stronie sprawdzamy czy oba parametry się zgadzają
		static function Signin()
		{
			if($GLOBALS['SESSION'])
			{
				require_once 'class/Panel.php';
				return Panel::Edit($GLOBALS['USER_ID']);
			}
			
			$user_id = 0;
			if(isset($_POST['email']) && isset($_POST['password']))
				$user_id = fastQuery('SELECT id FROM users WHERE email="' . esc($_POST['email']) . '" AND password="' . esc(md5($_POST['password'])) . '" LIMIT 1');
			
			if(intval($user_id) && $user_id>0)
			{
				query('DELETE FROM unsuccessful_login_try WHERE time_try < "' . date('Y-m-d H:i:s', time() - 60*60) . '"');
				query('DELETE FROM users_session WHERE login_time < "' . date('Y-m-d H:i:s', time() - 60*60) . '"');
				$kod = substr(md5(rand()), 0, 10); // to zapisujemy do ciasteczka
				
				setcookie('sesja', $kod, time() + 60*60);
			
				$kod = substr(md5(COOKIE_CODE . $kod), 3, 10); // to zapisujemy do bazy
				
				if(fastQuery('SELECT COUNT(user_id) FROM users_session WHERE user_id="' . esc($user_id) . '"'))
					query('DELETE FROM users_session WHERE user_id="' . esc($user_id) . '"');
				
				query('INSERT INTO users_session (user_id, code, user_ip, login_time) VALUES ("'.esc($user_id).'","' . esc($kod) . '","' . esc($_SERVER['REMOTE_ADDR']) . '","' . date('Y-m-d H:i:s', time() + 60*60) . '")');
				
				$GLOBALS['SESSION'] = 1;
				$GLOBALS['USER_ID'] = $user_id;
				require_once 'class/Panel.php';
				return Panel::Edit($user_id);
			}
			else
			{
				if(!isset($_POST['email'])) $_POST['email'] = '';
				
				query('INSERT INTO unsuccessful_login_try(email,ip_address,time_try) VALUES("' . esc($_POST['email']) . '","' . esc($_SERVER['REMOTE_ADDR']) . '","' . date('Y-m-d H:i:s', time()) . '")');
				self::CheckTryForIp();
				return self::SigninForm();
			}
		}
		
		//gdy rejestracja to zapisujemy w bazie i od razu logujemy (czy czekamy na potwierdzenei maila??) i również zapisujemy kod sesji i IP, oraz ustawiamy ciasteczko
		static function Registration()
		{
			if(!isset($_POST['password']) || !isset($_POST['password2']) || !isset($_POST['email']) || $_POST['password'] != $_POST['password2'] || fastQuery('SELECT COUNT(id) FROM users WHERE email="'.esc($_POST['email']).'"'))
			{
				messege('Nie udało się utworzyć konta. Spróbuj ponownie.');
				//dodać wyświetlanie błędów - tutaj, że istnieje już konto o takim loginie
				return self::RegistrationForm();
			}
			
			$_POST['password'] = md5($_POST['password']);
			$_POST['created_at'] = date('Y-m-d H:i:s', time());
			require_once 'class/Users.php';
			if(Users::Save($_POST)) messege('Rejestracja udana. Można się zalogować.');
			else messege('Rejestracja nieudana. Spróbuj ponownie.');
				
			//czy logujemy czy czekamy na potwierdzenie maila?
			return self::Signin();
		}
		
		static function Logout()
		{
			query('DELETE FROM users_session WHERE user_id = "' . $GLOBALS['USER_ID'] . '"');
			$GLOBALS['USER_ID'] = 0;
			$GLOBALS['SESSION'] = 0;
			setcookie('sesja','',time()-1);
			return self::SigninForm();
		}
			
		static function ChangePassword()
		{
			if(!isset($_POST['password']) || !isset($_POST['password2']) || $_POST['password'] != $_POST['password2'])
				return self::ChangePasswordForm();
			
			if(isset($_REQUEST['resetPasswordEmail']) && $_REQUEST['resetPasswordEmail'] != '')
			{
				$id = fastQuery('SELECT id FROM users WHERE code_reset_password="'.esc($_REQUEST['resetPasswordEmail']).'" LIMIT 1');
				if($id)
				{
					if(isset($_REQUEST['id']) && $_REQUEST['id']==$id)
					{
						query('UPDATE users SET password="'.esc(md5($_REQUEST['password'])).'", code_reset_password="" WHERE id="'.$id.'" LIMIT 1');
						return self::SigninForm();
					}
				}
			}
			else if($id = fastQuery('SELECT id FROM users WHERE id="'.esc($_REQUEST['id']).'" AND email="'.esc($_REQUEST['email']).'" AND password="'.esc(md5($_REQUEST['old_password'])).'" LIMIT 1'))
			{
				query('UPDATE users SET password="'.esc(md5($_REQUEST['password'])).'", code_reset_password="" WHERE id="'.$id.'" LIMIT 1');
				return self::SigninForm();
			}
			
			return self::ChangePasswordForm();
		}
		
		static function ResetPassword()
		{
			if(!isset($_REQUEST['email']) || !fastQuery('SELECT COUNT(id) FROM users WHERE email="'.esc($_REQUEST['email']).'"'))
			{
				return self::resetPasswordForm();
			}
			
			$code_reset_password = substr(md5(rand()), 0, 20);
			if(query('UPDATE users SET code_reset_password="'.esc($code_reset_password).'" WHERE email="'.esc($_REQUEST['email']).'"'))
			{
				mail($_REQUEST['email'], 'Panda Group - zresetowanie hasła', '<a href="'.DOMENA.'login.php?action=changePasswordForm&resetPasswordEmail='.esc($code_reset_password).'">Link do zmiany hasła</a>', 'From: Panda Group <kontakt@pandagroup.pl>'.PHP_EOL.'MIME-Version: 1.0'.PHP_EOL.'Content-type:text/html;charset=UTF-8'.PHP_EOL);
			}
			
			return self::SigninForm();
		}
		
		static function CheckTryForIp()
		{
			if(fastQuery('SELECT COUNT(ip_address) FROM unsuccessful_login_try WHERE ip_address = "' . esc($_SERVER['REMOTE_ADDR']) . '" AND time_try > "' . date('Y-m-d H:i:s', time() - 60) . '"') >= 3)
			{
				messege('Poczekaj minutę, aby spróbować zalogować się ponownie');
				return self::SigninForm();
			}
			if(isset($_REQUEST['email']) && fastQuery('SELECT COUNT(email) FROM unsuccessful_login_try WHERE email = "' . esc($_REQUEST['email']) . '" AND time_try > "' . date('Y-m-d H:i:s', time() - 60) . '"') >= 3)
			{
				messege('Poczekaj minutę, aby spróbować zalogować się ponownie');
				return self::SigninForm();
			}
		}
		
	}