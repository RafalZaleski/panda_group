<?php

	//require_once 'config.php';
	
	require_once 'class/ConnectDB.php';
	$GLOBALS['actual_db'] = new ConnectDB();
	$GLOBALS['SESSION'] = checkSession();
	
	if(!$GLOBALS['SESSION'] && strpos($_SERVER['PHP_SELF'],'login.php')===false) forward();
	
	function forward($url = 'login.php')
	{
		header('Location: ' . $url);
		die();
	}
	
	function esc($str)
	{
		return $GLOBALS['actual_db']->esc($str);
	}		
	
	function query($sql)
	{
		return $GLOBALS['actual_db']->query($sql);
	}
	
	function fetchAssoc($res)
	{
		return $res->fetch_assoc();
	}
	
	function fetchArray($res)
	{
		if($res !== null)
			return $res->fetch_array();
		else
			return false;
	}
	
	function numRows($res)
	{
		return $res->num_rows;
	}
	
	function fastQuery($sql)
	{
		$cb = $GLOBALS['actual_db']->query($sql);
		return fetchArray($cb)[0];
	}
	
	function checkSession()
	{
		if(isset($GLOBALS['USER_ID']) && $GLOBALS['USER_ID'] > 0) return true;
		
		if(isset($_COOKIE['sesja']))
		{
			$user_id = fastQuery('SELECT user_id FROM users_session WHERE code="' . esc(substr(md5(COOKIE_CODE . $_COOKIE['sesja']), 3, 10)) . '" AND user_ip="' . esc($_SERVER['REMOTE_ADDR']) . '" LIMIT 1');
			//zapytanie do bazy sprawdzające czy kod sesji jest przypisany do jakiegoś usera i IP się zgadza
			if($user_id > 0)
			{
				$GLOBALS['USER_ID'] = $user_id;
				query('UPDATE users_session SET login_time="'.date('Y-m-d H:i:s', time() + 60*60).'" WHERE user_id="'.esc($GLOBALS['USER_ID']).'" LIMIT 1');
				return true;
			}
		}
		
		return false;
	}
	
	function removeFieldFromHtml($html, $recog)
	{
		$start_tag = mb_strpos($html,$recog);
		if($start_tag >= 0)
		{
			$end_tag = $start_tag;
			
			while($html[$start_tag] != '<')
				$start_tag--;
			
			while($html[$end_tag] != '>')
				$end_tag++;
			
			$html1 = mb_substr($html,0,$start_tag-1);
			$html = $html1 . mb_substr($html,$end_tag);
		}
		
		return $html;
	}
	
	function removeTagFromHtml($html, $tag)
	{
		$start_tag = mb_strpos($html,'<'.$tag.'>');
		if($start_tag >= 0)
		{
			$end_tag = mb_strpos($html,'</'.$tag.'>',$start_tag);
				
			while($html[$end_tag] != '>')
				$end_tag++;
			
			$html1 = mb_substr($html,0,$start_tag-1);
			$html = $html1 . mb_substr($html,$end_tag+1);
		}
		
		return $html;
	}
	
	function loguj($info)
	{
		if(is_array($info))
		{
			ob_start();
			var_dump($info);
			$info = PHP_EOL . ob_get_contents();
			ob_end_clean();
		}
		if(isset($GLOBALS['USER_ID'])) $who = 'user: ' . $GLOBALS['USER_ID']; 
		else $who =	'IP: ' . $_SERVER['REMOTE_ADDR'];
		$trace = debug_backtrace();
		$place1 = end($trace);
		$place = 'file: ' . mb_substr($place1['file'],mb_strrpos($place1['file'],'\\')+1) . ' line: ' . $place1['line'];
		file_put_contents('logi.txt', date('Y-m-d H:i:s') . ' ' . $who . ' ' . $place . ' -> ' . $info . "\n", FILE_APPEND);	
	}
	
	function changeWord($word, $number)
	{
		$arr = ['dział' => ['dział','działy','działów'], 'ocena' => ['ocena','oceny','ocen'], 'lekcja' => ['lekcja', 'lekcje', 'lekcji']];
		if($number > 1 && $number < 5) $number = 2;
		else if($number >= 5) $number = 3;
		$number--;
		
		return $arr[$word][$number];
	}
	
	function getAjaxCode($action)
	{
		return substr(md5($GLOBALS['USER_ID'].$action.MD5CODE), 5, 10);
	}
	
	function messege($txt)
	{
		$GLOBALS['msg'][] = '<div class="msg">' . $txt . '<span class="msg_close" onclick="this.parentElement.parentElement.removeChild(this.parentElement);">X</span></div>';
	}
	
	function addTopHtml()
	{
		$top = file_get_contents('template/top.html');
		if(!$GLOBALS['SESSION'])
			$dane = ['act' => '?action=Signin&base=Login', 'button' => 'Zaloguj', 'is_login' => ''];
		else
			$dane = ['act' => '?action=Logout&base=Login', 'button' => 'Wyloguj', 'is_login' => '<a href="?action=Get&base=Csv" class="nav_el">Wczytanie CSV</a><a href="?action=Edit&base=Panel" class="nav_el">Panel</a>'];
		
		foreach($dane as $key => $val)
		{
			$top = str_replace('#'.$key.'#',$val,$top);
		}
		echo $top;
		
		if(isset($GLOBALS['msg']))
		{
			foreach($GLOBALS['msg'] as $val)
			{
				echo $val;
			}
		}
	}
	
?>