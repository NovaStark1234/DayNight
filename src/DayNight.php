<?php

namespace elconguyenvuong\daynight;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\command\PluginCommand;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\world\World;

class DayNight extends PluginBase implements Listener{
	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$commands = [new PluginCommand("day", $this, $this), new PluginCommand("night", $this, $this)];
		$commandMap = $this->getServer()->getCommandMap();
		array_map(function(Command $command) use ($commandMap) {
			$command->setDescription("DayNight main command instance.");
			$command->setPermission("daynight.command.use");
			$command->setUsage("/day [worldName: string] [isStop: bool] or /night [worldName: string] [isStop: bool]");
			$commandMap->register($this->getName(), $command);
		}, $commands);
	}

	// Lmao Area
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		$commandName = strtolower($command->getName());
		$world = null;
		$worldManager = $this->getServer()->getWorldManager();
		$sender instanceof Player ?: ($world = isset($args[0]) ? $args[0] : $worldManager->getDefaultWorld()->getFolderName()); 
		$world ?? ($world = isset($args[0]) ? $args[0] : $sender->getWorld());
		if(is_string($world)) $world = $worldManager->getWorldByName($world);
		$world ?? throw new InvalidCommandSyntaxException();
		if(!$command->testPermission($sender)) return true;
		switch($commandName) {
			case "day":
				$world->setTime(World::TIME_DAY);
				break;
			case "night":
				$world->setTime(World::TIME_NIGHT);
				break;
			default:
				return false;
				break;
		}
		!isset($args[1]) ?: ($world ?? ($args[1] == "true" ? $world->stopTime() : $world->startTime()));
		$sender->sendMessage("Time changed successfully.");
		return true;
	}
}
