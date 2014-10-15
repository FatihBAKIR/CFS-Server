<?php

class Listener
{
	public $event;
	public $listener;
	public $modifier;
}

class Messenger
{
	private static $Listeners = array();

	public static function AddListener ($event, $listener)
	{
		$nL = new Listener();
		$nL->event = $event;
		$nL->listener = $listener;
		$nL->modifier = false;

		self::$Listeners[] = $nL;
	}

	public static function AddModifier ($event, $listener)
	{
		$nL = new Listener();
		$nL->event = $event;
		$nL->listener = $listener;
		$nL->modifier = true;

		self::$Listeners[] = $nL;
	}

	public static function BroadcastMessage($event)
	{
	  	$args = func_get_args();

		for ($i=0; $i < count(Messenger::$Listeners); $i++) { 
			if (Messenger::$Listeners[$i]->event == $event && !Messenger::$Listeners[$i]->modifier)
			{
				call_user_func_array(self::$Listeners[$i]->listener, array_slice($args, 1));
			}
		}
	}

	public static function GetModifiers($event, $value)
	{
		$args = func_get_args();

		for ($i=0; $i < count(Messenger::$Listeners); $i++) { 
			if (Messenger::$Listeners[$i]->event == $event && Messenger::$Listeners[$i]->modifier)
			{
				$value = call_user_func_array(self::$Listeners[$i]->listener, array_slice($args, 1));
			}
		}

		return $value;
	}
}
?>