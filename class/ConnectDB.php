<?php

	class ConnectDB
	{
		private $db;
		
		function __construct()
		{
			require_once 'config.php';
			$this->connectDB();
		}
		
		private function connectDB()
		{
			$this->db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			if ($this->db->connect_errno)
			{
				loguj("Failed to connect to MySQL: (" . $this->db->connect_errno . ") " . $this->db->connect_error);
			}
			if(!$this->db->set_charset('utf8')) loguj('błąd przy ustawianiu charsetu na utf8 ' . $this->db->connect_errno . ' ' . $this->db->connect_error);
		}
		
		function esc($str)
		{
			return $this->db->real_escape_string($str);
		}
		
		function query($sql)
		{
			if($result = $this->db->query($sql))
			{
				//loguj($sql);
				return $result;
			}
			loguj('błąd przy zapytaniu sql ' . $this->db->connect_errno . ' ' . $this->db->connect_error . ' ' . $sql);
		}
	}
	
?>