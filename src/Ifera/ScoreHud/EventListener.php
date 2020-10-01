<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud;

use Ifera\ScoreHud\session\PlayerManager;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class EventListener implements Listener{

	/** @var ScoreHud */
	private $plugin;

	public function __construct(ScoreHud $plugin){
		$this->plugin = $plugin;
	}

	public function onWorldChange(EntityLevelChangeEvent $event){
		if(!ScoreHudSettings::isMultiWorld()){
			return;
		}

		$player = $event->getEntity();
		$world = $event->getTarget()->getFolderName();

		if(!$player instanceof Player){
			return;
		}

		PlayerManager::getNonNull($player)->handle($world);
	}
}