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

namespace JackMD\ScoreHud\updater\task;

use ErrorException;
use JackMD\ScoreHud\ScoreHud;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class AddonUpdateNotifyTask extends AsyncTask{

	/** @var string */
	private const ADDON_UPDATE_URL = "https://raw.githubusercontent.com/JackMD/ScoreHud-Addons/master/";

	/** @var string */
	private $addonName;
	/** @var string */
	private $addonVersion;

	/**
	 * AddonUpdateNotifyTask constructor.
	 *
	 * @param string $addonName
	 * @param string $addonVersion
	 */
	public function __construct(string $addonName, string $addonVersion){
		$this->addonName = $addonName;
		$this->addonVersion = $addonVersion;
	}

	public function onRun(): void{
		try{
			$file_handle = fopen(self::ADDON_UPDATE_URL . $this->addonName . ".php", "rb");

			$content = [];

			while(!feof($file_handle)){
				$line_of_text = fgets($file_handle);

				$content[] = $line_of_text;
			}

			fclose($file_handle);

			$data = [];
			$insideHeader = false;

			foreach($content as $line){
				if(!$insideHeader and strpos($line, "/**") !== false){
					$insideHeader = true;
				}

				if(preg_match("/^[ \t]+\\*[ \t]+@([a-zA-Z]+)([ \t]+(.*))?$/", $line, $matches) > 0){
					$key = $matches[1];
					$content = trim($matches[3] ?? "");
					$data[$key] = $content;
				}

				if($insideHeader and strpos($line, "*/") !== false){
					break;
				}
			}

			$this->setResult($data);
		}
		catch(ErrorException $errorException){
			//do nothing if error thrown :)
		}
	}

	/**
	 * @param Server $server
	 */
	public function onCompletion(Server $server): void{
		$plugin = ScoreHud::getInstance();

		if(is_null($plugin)){
			return;
		}

		$addonName = $this->addonName;
		$addonVersion = $this->addonVersion;

		$data = $this->getResult();

		if(empty($data) || !isset($data["version"])){
			$plugin->getLogger()->info("No new updates for addon $addonName were found. You are using the latest version.");

			return;
		}

		$highestVersion = $data["version"];

		if($highestVersion === $addonVersion){
			$plugin->getLogger()->info("No new updates for addon $addonName were found. You are using the latest version.");

			return;
		}

		$plugin->getLogger()->notice("Addon Update Alert! Version $highestVersion has been released for $addonName. Download the new release at https://github.com/JackMD/ScoreHud-Addons");
	}
}