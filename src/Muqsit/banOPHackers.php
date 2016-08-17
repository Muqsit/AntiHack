<?php
namespace Muqsit;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class addWindow extends PluginTask{

	public function __construct(Main $owner){
		$this->owner = $owner
		parent::__construct($owner);
	}

	public function onRun($currentTick){
		$this->owner->banOPHackers();
	}
}
