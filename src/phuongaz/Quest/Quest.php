<?php

namespace phuongaz\Quest;


use pocketmine\Server;
use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

use pocketmine\level\level;

use pocketmine\item\Item;
use pocketmine\inventory\Inventory;

use jojoe77777\FormAPI\SimpleForm;

Class Quest extends PluginBase {
	
	private $quests;
	private $questData;
	public $db;
	
	public function onEnable():void {
		$this->saveResource('quests.yml');
		$this->questData = new Config($this->getDataFolder() . "quests.yml", CONFIG::YAML);
		$this->db = new \SQLite3($this->getDataFolder() . "quest.db"); 
		$this->db->exec("CREATE TABLE IF NOT EXISTS pquests (name TEXT PRIMARY KEY COLLATE NOCASE, quest TEXT);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS pcompleted (name TEXT PRIMARY KEY COLLATE NOCASE, quests TEXT);");
		$this->quests = new Quests($this); 
	}

	public function onCommand(CommandSender $sender, Command $command, $label, array $args) : bool {
		if(strtolower($command->getName()) == "arquest"){
			if($sender instanceof Player){
				$this->sendForm($sender);
			}else $sender->sendMessage("Use command in game!");
		}	
		return true;
	}

	public function rca(Player $player, string $string) : void{
		$command = str_replace("{player}", $player->getName(), $string);
		Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), $command);
	}

	public function sendForm($sender) {
		$quest = new Quests($this);
		$form = new SimpleForm(function(Player $player, ?int $data) use ($quest){
			if($data == 0) $quest->sendQuestApplyForm($player);
			if($data == 1) $quest->Completed($player);
			if($data == 2) $quest->showQuest($player);
		});
		$form->setTitle("§l§6ARQUEST");
		$form->addButton("§l§cQuest");
		$form->addButton("§l§cComplete Quest");
		$form->addButton("§l§cYour Quest");
		$form->sendToPlayer($sender);
	}
}
		
