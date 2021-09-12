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

namespace Ifera\ScoreHud\event;

use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\player\Player;

/**
 * Same as PlayerTagUpdateEvent but provides an easier way
 * to send updates for multiple tags at the same time.
 *
 * @see PlayerTagUpdateEvent
 */
class PlayerTagsUpdateEvent extends PlayerEvent{

	/** @var ScoreTag[] */
	private array $tags = [];

	/**
	 * @param ScoreTag[] $tags
	 */
	public function __construct(Player $player, array $tags){
		$this->tags = $tags;

		parent::__construct($player);
	}

	/**
	 * @param ScoreTag[] $tags
	 */
	public function setTags(array $tags): void{
		$this->tags = $tags;
	}

	/**
	 * @return ScoreTag[]
	 */
	public function getTags(): array{
		return $this->tags;
	}

	public function addTag(ScoreTag $tag): void{
		$this->tags[] = $tag;
	}
}