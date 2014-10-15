<?php
require_once("messenger.php");

DiscoverAddons("../addons"); 

function DiscoverAddons( $path = '.', $level = 0 ){ 
    $ignore = array( 'cgi-bin', '.', '..' ); 
    $dh = @opendir( $path );
    while( false !== ( $file = readdir( $dh ) ) ){ 
        if( !in_array( $file, $ignore ))
        { 
            $spaces = str_repeat( '&nbsp;', ( $level * 4 ) );      
            if( is_dir( "$path/$file" ) )
            { 
                DiscoverAddons( "$path/$file", ($level+1) );              
            } 
            else 
            { 
            	if (endsWith($file, ".php"))
                	FoundAddon($path."/".$file); 
			}          
        } 
    }
    closedir( $dh ); 
}

function FoundAddon($file)
{
	include_once($file);
    if (isset($Info))
    {
        if (ADDON_SQL::Is_Active($file))
            $Info["class"]::Initialize();
        else if (!ADDON_SQL::Is_Present($file)) 
            {
                ADDON_SQL::New_Addon($file);
                $Info["class"]::Install();
            }
    }
    $Info = null;
}

function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

class ADDON_SQL
{
    static function Is_Active($file)
    {
        $query = mysql_query("select * from addons where file = '".$file."'");
        return (mysql_num_rows($query) > 0 && mysql_result($query, 0, "active") == 1);
    }

    static function Is_Present($file)
    {
        $query = mysql_query("select * from addons where file = '".$file."'");
        return (mysql_num_rows($query) > 0);        
    }

    static function New_Addon($file)
    {
        mysql_query("insert into addons (file, active) values ('".$file."', 0)");
    }
}
?>