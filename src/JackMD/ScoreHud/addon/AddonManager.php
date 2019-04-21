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

namespace JackMD\ScoreHud\addon;

use JackMD\ScoreHud\ScoreHud;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\ClosureTask;

class AddonManager{

	/** @var Addon[] */
	protected $addons = [];
	/** @var ScoreHud */
	private $scoreHud;
	/** @var string */
	private $addonDirectory;

	/**
	 * AddonManager constructor.
	 *
	 * @param ScoreHud $scoreHud
	 * @param string   $addonDirectory
	 */
	public function __construct(ScoreHud $scoreHud, string $addonDirectory){
		$this->scoreHud = $scoreHud;
		$this->addonDirectory = $addonDirectory;

		if(!is_dir($addonDirectory)){
			mkdir($addonDirectory);
		}

		/* This task enables addons to only start loading after complete server load */
		$task = new ClosureTask(function(int $currentTick): void{
			$this->loadAddons($this->addonDirectory);
		});

		$scoreHud->getScheduler()->scheduleDelayedTask($task, 0);

	}

	/**
	 * @param string $file
	 * @return AddonDescription|null
	 */
	private function getAddonDescription(string $file): ?AddonDescription{
		$content = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

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

		if($insideHeader){
			return new AddonDescription($data);
		}

		return null;
	}

	/**
	 * @param string $name
	 * @return Addon|null
	 */
	public function getAddon(string $name): ?Addon{
		if(isset($this->addons[$name])){
			return $this->addons[$name];
		}

		return null;
	}

	/**
	 * @return Addon[]
	 */
	public function getAddons(): array{
		return $this->addons;
	}

	/**
	 * @param string $directory
	 * @return array
	 */
	private function loadAddons(string $directory): array{
		if(!is_dir($directory)){
			return [];
		}

		$addons = [];
		$loadedAddons = [];
		$dependencies = [];

		foreach(glob($directory . "*.php") as $file){
			$description = $this->getAddonDescription($file);

			if(is_null($description)){
				continue;
			}

			$name = $description->getName();

			if(strpos($name, " ") !== false){
				throw new AddonException("§cCould not load $name addon since spaces found.");
			}

			if((isset($addons[$name]) )|| ($this->getAddon($name) instanceof Addon)){
				$this->scoreHud->getLogger()->error("§cCould not load addon §4{$name}§c. Addon with the same name already exists.");

				continue;
			}

			$addons[$name] = $file;
			$dependencies[$name] = $description->getDepend();
		}

		$pluginManager = $this->scoreHud->getServer()->getPluginManager();
		$loadedPlugins = $pluginManager->getPlugins();

		while(count($addons) > 0){
			$missingDependency = true;

			foreach($addons as $name => $file){
				if(isset($dependencies[$name])){
					foreach($dependencies[$name] as $key => $dependency){
						if(isset($loadedPlugins[$dependency]) || ($pluginManager->getPlugin($dependency) instanceof Plugin)){

							unset($dependencies[$name][$key]);
						}else{
							$this->scoreHud->getLogger()->error("§cCould not load addon §4{$name}§c. Unknown dependency: §4$dependency");

							unset($addons[$name]);
							continue 2;
						}
					}

					if(count($dependencies[$name]) === 0){
						unset($dependencies[$name]);
					}
				}

				if(!isset($dependencies[$name])){
					unset($addons[$name]);

					$missingDependency = false;
					$addon = $this->loadAddon($file);

					if($addon instanceof Addon){
						$loadedAddons[$name] = $addon;
					}else{
						$this->scoreHud->getLogger()->error("§cCould not load addon §4{$name}§c.");
					}
				}
			}

			if($missingDependency){
				foreach($addons as $name => $file){
					if(!isset($dependencies[$name])){
						unset($addons[$name]);

						$missingDependency = false;
						$addon = $this->loadAddon($file);

						if($addon instanceof Addon){
							$loadedAddons[$name] = $addon;
						}else{
							$this->scoreHud->getLogger()->error("§cCould not load addon §4{$name}§c.");
						}
					}
				}

				if($missingDependency){
					foreach($addons as $name => $file){
						$this->scoreHud->getLogger()->error("§cCould not load addon §4{$name}§c. Circular dependency detected.");
					}

					$addons = [];
				}
			}
		}

		return $loadedAddons;
	}

	/**
	 * @param string $path
	 * @return Addon|null
	 */
	private function loadAddon(string $path): ?Addon{
		$description = $this->getAddonDescription($path);

		if($description instanceof AddonDescription){
			include_once $path;

			$mainClass = $description->getMain();

			if(!class_exists($mainClass, true)){
				$this->scoreHud->getLogger()->error("Main class for addon " . $description->getName() . " not found.");

				return null;
			}

			if(!is_a($mainClass, Addon::class, true)){
				$this->scoreHud->getLogger()->error("Main class for addon " . $description->getName() . " is not an instance of " . Addon::class);

				return null;
			}

			try{
				$name = $description->getName();

				/** @var Addon $addon */
				$addon = new $mainClass($this->scoreHud, $description);
				$addon->onEnable();

				$this->addons[$name] = $addon;

				$this->scoreHud->getLogger()->notice("§aAddon §6$name §asuccessfully enabled.");

				return $addon;
			}
			catch(\Throwable $e){
				$this->scoreHud->getLogger()->logException($e);

				return null;
			}
		}

		return null;
	}
}
