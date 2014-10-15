<?php
class RapiAuth
{
	public function __construct($apiKey)
	{
		$this->apiKey = $apiKey;
	}
	
	public function Kontrol($kullanici, $kod)
	{		
		$adres = "http://auth.fatihbakir.net/v2/yeniAuth.php?api=".$this->apiKey."&userToken=".$kullanici."&kod=".$kod;
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $adres); 		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch); 
		curl_close($ch);
		$bilgiler = json_decode($output);  
		return ($bilgiler->result == "success");
	}
}
?>