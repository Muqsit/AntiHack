<?php
namespace Muqsit;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};
use pocketmine\{Server, Player};
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{
    
    public function onLoad(){
      @mkdir($this->getDataFolder());
      @mkdir($this->getDataFolder()."Players/");
    }

  public function onEnable(){
    @mkdir($this->getDataFolder());
    $this->saveDefaultConfig();
    $this->reloadConfig();
    $this->getServer()->getScheduler()->scheduleRepeatingTask(new banOPHackers($this, 20), 20);
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }
  
  public function ban($p, $n, $r){
    if($n === "name"){
      $this->getServer()->getNameBans()->addBan($p->getName(), $r);
    }elseif($n === "ip"){
      $this->getServer()->getIPBans()->addBan($p->getAddress(), $r);
    }elseif($n === "name+ip"){
      $this->getServer()->getNameBans()->addBan($p->getName(), $r);
      $this->getServer()->getIPBans()->addBan($p->getAddress(), $r);
    }else{
      return;
    }
  }
  
  public function getpfile(Player $player){
    return $this->getDataFolder()."Players/".$player->getName().".yml";
  }
  
  public function onJoin(PlayerJoinEvent $event){
    $player = $event->getPlayer();
    $file = ($this->getDataFolder()."Players/".$player->getName().".yml");
    $this->pfile = new Config($this->getDataFolder()."Players/".$player->getName().".yml", Config::YAML);
    $data = $this->pfile->get("ban-points", "ip-address");
    if(!file_exists($file)){
		  $this->pfile->set("ban-points", 0);
		  $this->pfile->set("ip-address", $this->getServer()->getPlayer()->getAddress());
		  $this->pfile->save();
    }
		  if(!$this->pfile->exists($data)){
        $this->pfile->set("ban-points", 0);
        $this->pfile->set("ip-address", $this->getServer()->getPlayer()->getAddress());
		    $this->pfile->save();
      }
  }
  
  public function onEntityDamage(EntityDamageEvent $e){
    $p = $event->getEntity();
    $cfg = $this->getConfig();
    $pfile = $this->getpfile();
    $why = $cfg->get("ban-enchant-hacker-reason");
    $chance = $pfile->get("ban-points");
    $max = $cfg->get("max-ban-points");
    if($cfg->get("ban-enchant-hackers") === "true"){
      if($e instanceof EntityDamageByEntityEvent){
        $damager = $e->getDamager();
        if($p instanceof Player && $damager instanceof Player){
          $weapon = $damager->getInventory()->getItemInHand();
          foreach($weapon->getEnchantments() as $en){
            if($en->getLevel() > $cfg->get("max-enchantment-level")){
            	$damager->getInventory()->removeItem($weapon);
            	$type = $cfg->get("ban-enchant-hacker-type");
              if($chance === $max){
            	$this->ban($damager, $type, $why);
              }
            }
          }
        }
      }
    }
  }
}
