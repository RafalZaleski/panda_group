<?php
	require_once 'class/InterfaceSimple.php';
	
	class Csv implements InterfaceSimple
	{
		static function Save(int $id = 0)
		{
			$dane = array();
			$file = array_shift($_FILES);

			if($file['error'] != UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name']) || pathinfo($file['name'],PATHINFO_EXTENSION) != 'csv')
			{
				messege('Wystąpił błąd podczas wczytywania pliku. Upewnij się, że plik jest w formacie csv.');
				return self::Get();
			}
			$file_content = file_get_contents($file['tmp_name']);
			
			query('INSERT INTO csv(name, content) VALUES("' . esc($file['name']) . '","' . base64_encode(esc($file_content)) . '")');
			
			$file = fopen($file['tmp_name'],'r');
			
			$first_row = fgetcsv($file);
			$country_col_no = array_keys($first_row,'country')[0];
			$data = array();

			while($row = fgetcsv($file))
			{
				if(!array_key_exists($row[$country_col_no],$data)) $data[$row[$country_col_no]] = 0;;
				$data[$row[$country_col_no]]++;
			}

			arsort($data);
			
			require_once 'class/Csv.php';
			
			//foreach($data as $key => $val)
			//{
			//	$csvDb = new Csv(['country' => $key, 'total_amount' => $val]);
			//	$csvDb->AddNewRowDb();
			//}
			
			$dane['country'] = "'".implode("','",array_keys($data))."'";
			$dane['no_person_from_country'] = implode(',',$data);
			$dane['count_array'] = 25*count($data);
			
			$html = file_get_contents('template/csv.html');
			foreach($dane as $key => $val) $html = str_replace('#'.$key.'#',$val,$html);
			
			addTopHtml();
			echo $html;
			echo file_get_contents('template/footer.html');
		}
		
		static function Edit(int $id = 0)
		{
			
		}
		
		static function Del(int $id = 0)
		{
			
		}
		
		static function Get(int $id = 0)
		{
			addTopHtml();
			echo file_get_contents('template/form_csv.html');
			echo file_get_contents('template/footer.html');
		}
		
	}
	
?>