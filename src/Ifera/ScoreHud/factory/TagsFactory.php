<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\factory;

use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\event\ServerTagsUpdateEvent;
use Ifera\ScoreHud\factory\listener\FactoryListener;
use Ifera\ScoreHud\factory\listener\TagResolveListener;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\ScoreHud;
use Ifera\ScoreHud\ScoreHudSettings;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Process;

class TagsFactory {

	public static function init(ScoreHud $plugin) {
		$server = $plugin->getServer();

		$server->getPluginManager()->registerEvents(new FactoryListener($plugin), $plugin);
		$server->getPluginManager()->registerEvents(new TagResolveListener($plugin), $plugin);

		$task = new ClosureTask(function() use ($plugin): void{
			$server = $plugin->getServer();

			foreach($server->getOnlinePlayers() as $player){
				(new PlayerTagUpdateEvent($player, new ScoreTag("scorehud.ping", (string) ($player->getNetworkSession()->getPing()))))->call();
			}

			(new ServerTagsUpdateEvent([
				new ScoreTag("scorehud.load", (string) $server->getTickUsage()),
				new ScoreTag("scorehud.tps", (string) $server->getTicksPerSecond()),
				new ScoreTag("scorehud.time", date(ScoreHudSettings::getTimeFormat())),
				new ScoreTag("scorehud.date", date(ScoreHudSettings::getDateFormat()))
			]))->call();

			if(ScoreHudSettings::areMemoryTagsEnabled()){
				$rUsage = Process::getRealMemoryUsage();
				$mUsage = Process::getAdvancedMemoryUsage();

				$globalMemory = "MAX";
				if($server->getConfigGroup()->getProperty("memory.global-limit") > 0){
					$globalMemory = number_format(round($server->getConfigGroup()->getProperty("memory.global-limit"), 2), 2) . " MB";
				}

				(new ServerTagsUpdateEvent([
					new ScoreTag("scorehud.memory_main_thread", number_format(round(($mUsage[0] / 1024) / 1024, 2), 2) . " MB"),
					new ScoreTag("scorehud.memory_total", number_format(round(($mUsage[1] / 1024) / 1024, 2), 2) . " MB"),
					new ScoreTag("scorehud.memory_virtual", number_format(round(($mUsage[2] / 1024) / 1024, 2), 2) . " MB"),
					new ScoreTag("scorehud.memory_heap", number_format(round(($rUsage[0] / 1024) / 1024, 2), 2) . " MB"),
					new ScoreTag("scorehud.memory_global", $globalMemory)
				]))->call();
			}
		});

		$plugin->getScheduler()->scheduleRepeatingTask($task, ScoreHudSettings::getTagFactoryUpdatePeriod() * 20);
	}
}