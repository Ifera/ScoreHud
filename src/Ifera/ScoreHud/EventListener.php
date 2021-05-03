<?php
declare(strict_types = 1);

/**
 *     _____                    _   _           _
 *    /  ___|                  | | | |         | |
 *    \ `--.  ___ ___  _ __ ___| |_| |_   _  __| |
 *     `--. \/ __/ _ \| '__/ _ \  _  | | | |/ _` |
 *    /\__/ / (_| (_) | | |  __/ | | | |_| | (_| |
 *    \____/ \___\___/|_|  \___\_| |_/\__,_|\__,_|
 *
 * ScoreHud, a Scoreboard plugin for PocketMine-MP
 * Copyright (c) 2020 Ifera  < https://github.com/Ifera >
 *
 * Discord: Ifera#3717
 * Twitter: ifera_tr
 *
 * This software is distributed under "GNU General Public License v3.0".
 * This license allows you to use it and/or modify it but you are not at
 * all allowed to sell this plugin at any cost. If found doing so the
 * necessary action required would be taken.
 *
 * ScoreHud is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License v3.0 for more details.
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 * ------------------------------------------------------------------------
 */

namespace Ifera\ScoreHud;

use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\event\ServerTagsUpdateEvent;
use Ifera\ScoreHud\event\ServerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\session\PlayerManager;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use function is_null;

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

		if(!$player instanceof Player){
			return;
		}

		PlayerManager::getNonNull($player)->handle($event->getTarget()->getFolderName());
	}

	public function onServerTagUpdate(ServerTagUpdateEvent $event){
		$this->updateServerTag($event->getTag());
	}

	public function onServerTagsUpdate(ServerTagsUpdateEvent $event){
		foreach($event->getTags() as $tag){
			$this->updateServerTag($tag);
		}
	}

	public function onPlayerTagUpdate(PlayerTagUpdateEvent $event){
		$this->updateTag($event->getPlayer(), $event->getTag());
	}

	public function onPlayerTagsUpdate(PlayerTagsUpdateEvent $event){
		foreach($event->getTags() as $tag){
			$this->updateTag($event->getPlayer(), $tag);
		}
	}

	private function updateServerTag(ScoreTag $tag){
		foreach(PlayerManager::getAll() as $session){
			$this->updateTag($session->getPlayer(), $tag);
		}
	}

	private function updateTag(Player $player, ScoreTag $newTag): void{
		if(!$player->isOnline() || is_null($session = PlayerManager::get($player))){
			return;
		}

		$scoreboard = $session->getScoreboard();

		if(is_null($scoreTag = $scoreboard->getTag($newTag->getName()))){
			return;
		}

		$this->plugin->setScore($session->getPlayer());

		$scoreTag->setValue($newTag->getValue());
		$scoreboard->update()->display();
	}
}