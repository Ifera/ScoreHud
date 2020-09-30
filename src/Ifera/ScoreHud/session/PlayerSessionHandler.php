<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\session;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerSessionHandler implements Listener{

	/**
	 * @param PlayerJoinEvent $event
	 * @priority LOW
	 */
	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		PlayerManager::create($event->getPlayer());
	}

	/**
	 * @param PlayerQuitEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerQuit(PlayerQuitEvent $event) : void{
		PlayerManager::destroy($event->getPlayer());
	}
}