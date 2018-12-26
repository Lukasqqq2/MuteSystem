<?php

namespace Puma\MuteSystem;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\utils\Config;
use pocketmine\inventory;
use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use pocketmine\block\Block;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerTransferEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ProtectionEnchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class Main extends PluginBase implements Listener {

	public $prefix = "§cMuteSystem §7| ";
	
	public $player;
	
	public function onEnable() {
		
		@mkdir("/cloud");
		@mkdir("/cloud/user");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getLogger()->info($this->prefix . "wurde erfolgreich geladen!");
		
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		switch($command->getName()){
			case "mute":
				if ($sender->hasPermission("mute.team")) {
					if(isset($args[0])) {
						if(isset($args[1])) {
								
							$reason = $args[1];
							$sn = $sender->getName();
							$kplayer = $this->getServer()->getPlayerExact($args[0]);
							$banname = $args[0];
							$sender->sendMessage($this->prefix . "§bDu hast den Spieler §a" . $args[0] . " §bgemutet!");
							$mp = new Config("/cloud/user/" . $args[0] . ".yml", Config::YAML);
							$mp->set("Mute", true);
							$mp->save();
							if ($kplayer instanceof Player) {
								
								$kplayer->sendMessage($this->prefix . "§fDu wurdest von §c" . $sender->getName() . " §ffür " . $reason . " §fgemutet!");
							}
							foreach($this->getServer()->getOnlinePlayers() as $player){
								if($player->isOP()) {
									$player->sendMessage($this->prefix . "§b" . $args[0] . " §fwurde von §c" . $sender->getName() . " §ffür " . $args[1] . " §fgemutet!");
								}
								return true;
							}
							return true;
						}else {

							$sender->sendMessage($this->prefix . "§bEs wurde kein Grund angegeben!");
						}
					}else {

						$sender->sendMessage($this->prefix . "§bEs wurde kein Spieler angegeben!");
					}
				}
				
			return true;
			case "unmute":
				if ($sender->hasPermission("mute.team")) {
					if(isset($args[0])) {
								
						$sn = $sender->getName();
						$kplayer = $this->getServer()->getPlayerExact($args[0]);
						$banname = $args[0];
						$sender->sendMessage($this->prefix . "§bDu hast den Spieler §a" . $args[0] . " §bentmutet!");
						$mp = new Config("/cloud/user/" . $args[0] . ".yml", Config::YAML);
						$mp->set("Mute", false);
						$mp->save();
						if ($kplayer instanceof Player) {
								
							$kplayer->sendMessage($this->prefix . "§fDu wurdest von §c" . $sender->getName() . " §fentmutet!");
						}
						foreach($this->getServer()->getOnlinePlayers() as $player){
							if($player->isOP()) {
									$player->sendMessage($this->prefix . "§b" . $args[0] . " §fwurde von §c" . $sender->getName() . " §bentmutet!");
								}
								return true;
							}
							return true;
						}else {

							$sender->sendMessage($this->prefix . "§bEs wurde kein Spieler angegeben!");
						}
				}
				
			return true;
		}
	}
	
	public function onChat(PlayerChatEvent $event) {

		$player = $event->getPlayer();
		$msg = $event->getMessage();
		$mp = new Config("/cloud/user/" . $player->getName() . ".yml", Config::YAML);
		if ($mp->get("Mute") === true) {
			
			$event->setCancelled(true);
			$player->sendMessage($this->prefix . "§bDu bist noch gemutet!");
		}
		
	}

	public function onDisable() {
		
		$this->getServer()->getLogger()->info($this->prefix . "wird runtergrladen!");
		
	}
	
}