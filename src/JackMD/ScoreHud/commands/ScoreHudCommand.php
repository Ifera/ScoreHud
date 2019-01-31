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
 * Copyright (c) 2018 JackMD  < https://github.com/JackMD >
 *
 * Discord: JackMD#3717
 * Twitter: JackMTaylor_
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

namespace JackMD\ScoreHud\commands;

use JackMD\ScoreHud\Main;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;

class ScoreHudCommand extends PluginCommand{

	/** @var Main */
	private $plugin;

	public function __construct(Main $plugin){
		parent::__construct("scorehud", $plugin);
		$this->setDescription("Give a crate key to a player.");
		$this->setUsage("/scorehud <on|off|about>");
		$this->setAliases(["sh"]);
		$this->setPermission("sh.command.sh");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(!isset($args[0])){
			$sender->sendMessage(Main::PREFIX . "§cUsage: §a/scorehud <on|off|about>");

			return false;
		}
		switch($args[0]){
			case "about":
				$sender->sendMessage(Main::PREFIX . "§6Score§eHud §av" . $this->plugin->getDescription()->getVersion());
				$sender->sendMessage(Main::PREFIX . "§aPlugin by §dJackMD§a. Contact on §bTwitter: JackMTaylor_ §aor §bDiscord: JackMD#3717§a.");
				break;

			case "on":
				if(isset($this->plugin->disabledScoreHudPlayers[strtolower($sender->getName())])){
					unset($this->plugin->disabledScoreHudPlayers[strtolower($sender->getName())]);
					$sender->sendMessage(Main::PREFIX . "§aSuccessfully enabled ScoreHud.");
				}else{
					$sender->sendMessage(Main::PREFIX . "§cScoreHud is already enabled for you.");
				}
				break;

			case "off":
				if(!isset($this->plugin->disabledScoreHudPlayers[strtolower($sender->getName())])){
					$this->plugin->disabledScoreHudPlayers[strtolower($sender->getName())] = 1;
					$sender->sendMessage(Main::PREFIX . "§aSuccessfully disabled ScoreHud.");
				}else{
					$sender->sendMessage(Main::PREFIX . "§cScoreHud is already disabled for you.");
				}
				break;

			case "help":
			default:
				$sender->sendMessage(Main::PREFIX . "§cUsage: §a/scorehud <on|off|about>");
				break;
		}

		return false;
	}
}