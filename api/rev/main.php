<?
require("config.php");
@mysql_connect($host, $user, $pass) or die ("Database Error 1");
@mysql_select_db($db) or die ("Database Error 2");
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET utf8");
mysql_query("SET COLLATION_CONNECTION = 'utf8_general_ci'");
$sql = new quicksql();

function zaman()
{
	$zaman = explode(" ", microtime());
	$zaman = $zaman[1];
	return $zaman;
}

function replace_tr($text) 
{
	$q = $text;
	$q = str_replace ("ç","c",$q);
	$q = str_replace ("ç","c",$q); 
	$q = str_replace ("ğ","g",$q); 
	$q = str_replace ("İ","I",$q); 
	$q = str_replace ("ı","i",$q); 
	$q = str_replace ("ş","s",$q); 
	$q = str_replace ("ö","o",$q); 
	$q = str_replace ("ü","u",$q); 
	$q = str_replace ("Ü","U",$q); 
	$q = str_replace ("Ç","c",$q); 
	$q = str_replace (".","-",$q); 
	$q = str_replace ("ğ","g",$q); 
	$q = str_replace ("Ş","S",$q); 
	$q = str_replace ("Ö","O",$q); 
	$q = str_replace (" ","-",$q); 
	return $q;
}

function filesizeinfo($fs) 
{ 
 $bytes = array('KB', 'KB', 'MB', 'GB', 'TB'); 
 if ($fs <= 999) { 
  $fs = 1; 
 } 
 for ($i = 0; $fs > 999; $i++) { 
  $fs /= 1024; 
 } 
 return ($fs%1024).$bytes[$i]; 
} 

class user
{
	public $id;
	public $name;
	public $pass;
	public $email;
	public $limit;
}

class file
{
	public $path;
	public $id;
	public $type;
	public $name;
	public $pass;
	public $size;
	public $title;
	public $active;
	public $dir;
	public $owner;
	public $pub;
	public $utime;
	
	public function __construct($_path, $_id, $_type, $_name, $_pass, $_size, $_title, $_active, $_directory, $_pub, $_time)
	{
		$this->id = $_id;
		$this->path = $_path;
		$this->type = $_type;
		$this->name = $_name;
		$this->pass = $_pass;
		$this->size = $_size;
		$this->title = $_title;
		$this->active = $_active;
		$this->dir = $_directory;
		$this->pub = $_pub == 1;
		$this->utime = $_time; 
	}
}

class klasor
{
	public $id;
	public $kimin;
	public $isim;
	public $acik;
	public $pass;
	public $aktif;
	public $ustdizin;
}

class sahiplik
{
	public $dosya;
	public $kullanici;
	public $id;
	
	public function __construct($dosya, $kullanici, $id)
	{
		$this->dosya = $dosya;
		$this->kullanici = $kullanici;
		$this->id = $id;
	}
}

class quicksql
{
	public function userfromid($id)
	{
		$query = mysql_query("select * from kullanicilar where id='".$id."'");
		return $this->userfromquery($query);
	}
	
	public function login($name, $pass)
	{
		$user = $this->userfromname($name);
		if (isset($name) && $name != "" && isset($pass) && $pass != "" && $user->name == $name)
			return ($user->pass == $pass);
		else
			return false;
	}
	
	public function userfromname($name)
	{
		$query = mysql_query("select * from kullanicilar where kullanici='".$name."'");
		return $this->userfromquery($query);
	}
	
	public function userfrommail($email)
	{
		$query = mysql_query("select * from kullanicilar where mail='".$email."'");
		return $this->userfromquery($query);
	}
	
	private function userfromquery($query)
	{
		$user = new user();
		$user->id = @mysql_result($query, 0, "id");
		$user->name = @mysql_result($query, 0, "kullanici");
		$user->pass = @mysql_result($query, 0, "sifre");
		$user->email = @mysql_result($query, 0, "mail");
		$user->limit = @mysql_result($query, 0, "dosya_limiti");
		return $user;
	}
	
	public function serversetting($key)
	{
		$query = mysql_query("select * from ayarlar where ayarisim='".$key."'");
		return mysql_result($query, 0, "deger");
	}
	
	public function IDFromVer($fileID, $rev)
	{
		$query = mysql_query("select * from versioning where base_file='".$fileID."' ORDER BY file DESC");
		if (mysql_num_rows($query) < 1 || ($rev == 0 && is_integer($rev))) return $fileID;
		return $this->IDFromVer(mysql_result($query, 0, "file"), $rev == "latest" ? $rev : $rev - 1);
	}
	
	public function BaseOfRev($ID)
	{
		$query = mysql_query("select * from versioning where file='".$ID."'");
		if (mysql_num_rows($query) < 1) return $ID;
		return $this->BaseOfRev(mysql_result($query, 0, "base_file"));
	}
	
	public function filefromid($id)
	{
		$query = mysql_query("select * from yuklemeler where id='".$id."'");
		return $this->FileFromQuery($query);
	}
	
	public function FileFromQuery($query)
	{
		$file = new file(
		$this->serversetting("script_adress")."/".$this->serversetting("up_path")."/".@mysql_result($query, 0, "depolanandosya"), 
		@mysql_result($query, 0, "id"), 
		@mysql_result($query, 0, "contenttype"), 
		@mysql_result($query, 0, "dosyaismi"), 
		@mysql_result($query, 0, "sifre"), 
		@mysql_result($query, 0, "boyut"), 
		@mysql_result($query, 0, "baslik"), 
		@mysql_result($query, 0, "aktif"), 
		@mysql_result($query, 0, "dizin"), 
		@mysql_result($query, 0, "pub"), 
		@mysql_result($query, 0, "zaman"));
		return $file;
	}
	
	public function newfile($name, $title, $physicalfile, $password, $type, $size, $dir = "n1", $user = "n1", $pub = 0)
	{
		if ($dir == "n1") $dir = $_SESSION["klasor"]->id;
		if ($user == "n1") $user = $_SESSION["kullanici"]->id;
		$zaman = zaman();
		mysql_query("insert into yuklemeler (zaman, dosyaismi, baslik, depolanandosya, sifre, contenttype, boyut, ip, dizin, pub) VALUES ('".$zaman."','".$name."','".$title."','".$physicalfile."','".$password."','".$type."','".$size."','".$_SERVER['REMOTE_ADDR']."', '".$dir."', '".$pub."')");
		
		$query = mysql_query("select id from yuklemeler where zaman='".$zaman."' AND dosyaismi='".$name."'  AND baslik='".$title."'  AND depolanandosya='".$physicalfile."' AND sifre='".$password."' AND contenttype='".$type."'  AND boyut='".$size."' AND ip='".$_SERVER['REMOTE_ADDR']."' AND dizin='".$dir."'");
		
		mysql_query("insert into sahiplik (dosya,kullanici) VALUES ('".mysql_result($query, 0, "id")."','".$user."')");
		return mysql_result($query, 0, "id");
	}
	
	public function kimin($id)
	{
		$query = mysql_query("select * from sahiplik where dosya='".$id."'");
		$kimin = $this->userfromid(mysql_result($query, 0, "kullanici"));
		return $kimin;
	}
	
	public function dosyalari($id)
	{
		$query = mysql_query("select * from sahiplik where kullanici='".$id."'");
		
		$dosyalar = array();
		while ($row = mysql_fetch_array($query)) {
			$dosya = $this->filefromid($this->IDFromVer($row["dosya"], "latest"));
			if ($dosya->active == 1 && !in_array($dosya, $dosyalar))
				if (Messenger::GetModifiers("listing_file", true, $dosya))
					$dosyalar[] = $dosya;
		}
		
		return $dosyalar;
	}
	
	public function klasordekidosyalari($userID, $dirID)
	{
		$query = mysql_query("select * from yuklemeler where dizin = '".$dirID."'");
		
		$dosyalar = array();
		while ($row = mysql_fetch_array($query)) {
			$dosya = $this->filefromid($this->IDFromVer($row["id"], "latest"));
			$x = API_SQL::FilePermission($userID, $dosya->id);
			if ($dosya->active == 1 && !in_array($dosya, $dosyalar) && ($dirID != 0 || $x->CanGet()))
				
				if (Messenger::GetModifiers("listing_file", true, $dosya))
					$dosyalar[] = $dosya;
		}
		
		return $dosyalar;
	}
	
	public function indirme($id)
	{
		mysql_query("insert into indirmeler (dosya, ip, zaman) VALUES ('".$id."','".$_SERVER['REMOTE_ADDR']."','".zaman()."')");
	}
	
	public function kackere($id)
	{
		$query = mysql_query("select * from indirmeler where dosya='".$id."'");
		return mysql_num_rows($query);
	}
	
	public function yeniuye($isim, $sifre, $mail)
	{
		$sifre = md5($sifre);
		if ($this->userfromname($isim)->name != $isim && $this->userfrommail($mail) != $mail)
		{
			if (Messenger::GetModifiers("signing_up", true, $isim, $sifre, $mail))
			{
				mysql_query("insert into kullanicilar (kullanici, sifre, mail, dosya_limiti) VALUES ('".$isim."','".$sifre."','".$mail."','".$this->serversetting("dosya_sayisi")."')");
				return 1;
			}
			else
				return 0;
		}
		else
		{
			return 0;
		}
	}
	
	public function klasorler($ustklasor)
	{
		$query = mysql_query("select * from klasorler where kullanici='".$_SESSION["kullanici"]->id."' AND ust_klasor='".$ustklasor->id."'");
		
		$dosyalar = array();
		while ($row = mysql_fetch_array($query)) {
			$dosya = new klasor();
			$dosya->id = $row["id"];
			$dosya->kimin = $row["kullanici"];
			$dosya->aktif = $row["aktif"];
			$dosya->isim = $row["isim"];
			$dosya->acik = $row["public"];
			$dosya->pass = $row["sifre"];
			$dosya->ustdizin = $row["ust_klasor"];
			if ($dosya->aktif == 1)
				$dosyalar[] = $dosya;
		}
		
		return $dosyalar;
	}
	
	public function klasor($id)
	{
		$dosya = new klasor();
		if ($id != 0)
		{
			$query = mysql_query("select * from klasorler where id='".$id."'");
			
			$row = mysql_fetch_array($query);			
			$dosya->id = $row["id"];
			$dosya->kimin = $row["kullanici"];
			$dosya->aktif = $row["aktif"];
			$dosya->isim = $row["isim"];
			$dosya->acik = $row["public"];
			$dosya->sifre = $row["sifre"];
			$dosya->ustdizin = $row["ust_klasor"];
		}
		else
		{
			$klasor = new klasor();
			$klasor->id = "0";
			$klasor->isim = "Ana Dizin";
			$dosya = $klasor;
		}
		
		return $dosya;
	}
}
?>