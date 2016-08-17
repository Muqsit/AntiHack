<?php
namespace Muqsit;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class banOPHackers extends PluginTask{

	public function __construct(Main $owner){
		parent::__construct($owner);
		$this->owner = $owner;
	}

	public function onRun($currentTick){
		$this->owner->opProtection();
	}
}
