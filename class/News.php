<?php
	require_once 'class/InterfaceSimple.php';
	require_once 'class/Db/DbNews.php';
			
	class News implements InterfaceSimple
	{
		static function Save(int $id = 0)
		{
			$_POST['created_at'] = date('Y-m-d H:i:s', time());
			$_POST['updated_at'] = date('Y-m-d H:i:s', time());
			$_POST['author_id'] = $GLOBALS['USER_ID'];
			$news = new DbNews($_POST);
			if($news->AddNewRowDb(['id','created_at','author_id'])) messege('Dodano/zedytowano newsa.');
			else messege('Wystąpił błąd podczas dodawania/edytowanie newsa.');
			return self::Get();
		}
		
		static function Edit(int $id = 0)
		{
			if(isset($_REQUEST['id']) && intval($_REQUEST['id'])) $id = $_REQUEST['id'];
			$html = file_get_contents('template/form_add_news.html');
			$data = ['name' => '', 'description' => ''];
			if($id>0)
			{
				$data = new DbNews();
				$data = $data->getDataById($id);
				if($data['is_active']) $data['is_active'] = ' checked';
				else $data['is_active'] = '';
			}
			foreach($data as $key => $val) $html = str_replace('#'.$key.'#',$val,$html);
			
			addTopHtml();
			echo $html;
			echo '<a id="add_news" href="?action=Edit&base=News">Dodaj nowego newsa</a>';
			echo file_get_contents('template/footer.html');
		}
		
		static function Del(int $id = 0)
		{
			$news = new DbNews();
			if($news->DeleteRowById($_REQUEST['id'])) messege('Usunięto newsa.');
			else messege('Wystąpił błąd podczas usuwania newsa.');
			return self::Get();
		}
		
		static function Get(int $id = 0)
		{
			$html_template = file_get_contents('template/show_news_list.html');
			$html = '';
			$news = new DbNews();
			$news = $news->GetSpecific($GLOBALS['SESSION'], $id);
			
			if($news !== false && numRows($news) > 0)
			{
				while($row = fetchAssoc($news))
				{
					if($GLOBALS['SESSION']) 
						$row['edit'] = '<a class="inline sf mr" href="?action=Edit&base=News&id=' . $row['id'] . '">EDYTUJ</a><a class="inline sf mr" href="?action=Del&base=News&id=' . $row['id'] . '">USUŃ</a>';
					else
						$row['edit'] = '';
					
					if($row['author_id'] > 0)
					{
						$row['author'] = fastQuery('SELECT CONCAT(first_name," ",last_name) FROM users WHERE id=' . esc($row['author_id']));
						unset($row['author_id']);
						
					}
					if($row['updated_at'] == '0000-00-00 00:00:00') $row['updated_at'] = '-';
					
					$html_helper = $html_template;
					foreach($row as $key => $val)
					{
						$html_helper = str_replace('#'.$key.'#',$val,$html_helper);
					}
					$html .= $html_helper;
				}
			}
			
			$paggination = fastQuery('SELECT COUNT(id) FROM news' . (!$GLOBALS['SESSION'] ? ' WHERE is_active=1' : ''));
			
			if($paggination > 10)
			{
				$no_page = ceil($paggination / 10);
				if($no_page > 25)
				{
					$start = $id/10 - 13;
					if($start < 0) $start = 0;
					$koniec = $start + 25;
				}
				else
				{
					$start = 0;
					$koniec = $no_page;
				}
				
				$html_tmp = '<div class="pagging">';
				
				if($start > 13) $html_tmp .= '<a class="no_page" href="href="?action=Get&base=News&id=' . (($start-1)*10) . '">...</a>';
					
				for($i = $start; $i < $koniec; $i++) $html_tmp .= '<a class="no_page' . (10*$i == $id ? ' active_page' : '') . '" href="?action=Get&base=News&id=' . 10*$i . '">' . ($i + 1) . '</a>';
				
				if($koniec < $no_page) $html_tmp .= '<a class="no_page" href="?action=Get&base=News&id=' . (($koniec)*10) . '">...</a>';
				
				$html_tmp .= '</div>';
				$html = $html_tmp . $html . $html_tmp;
			}
			
			addTopHtml();
			echo $html;
			if($GLOBALS['SESSION']) echo '<a id="add_news" href="?action=Edit&base=News">Dodaj nowego newsa</a>';
			echo file_get_contents('template/footer.html');
		}
	}
	
?>