<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\factory\listener;

use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\ScoreHud\ScoreHud;
use Ifera\ScoreHud\ScoreHudSettings;
use pocketmine\event\Listener;
use pocketmine\utils\Process;
use function count;
use function date;
use function explode;
use function number_format;
use function round;

class TagResolveListener implements Listener {

	public function __construct(
		private ScoreHud $plugin
	) {}

	public function onTagResolve(TagsResolveEvent $event) {
		$player = $event->getPlayer();
		$tag = $event->getTag();
		$tags = explode('.', $tag->getName(), 2);
		$value = "";

		if ($tags[0] !== 'scorehud' || count($tags) < 2) return;

		switch ($tags[1]) {
			case "name":
			case "real_name":
				$value = $player->getName();
			break;

			case "display_name":
				$value = $player->getDisplayName();
			break;

			case "online":
				$value = count($player->getServer()->getOnlinePlayers());
			break;

			case "max_online":
				$value = $player->getServer()->getMaxPlayers();
			break;

			case "item_name":
				$value = $player->getInventory()->getItemInHand()->getName();
			break;

			case "item_id":
				$value = $player->getInventory()->getItemInHand()->getId();
			break;

			case "item_meta":
				$value = $player->getInventory()->getItemInHand()->getMeta();
			break;

			case "item_count":
				$value = $player->getInventory()->getItemInHand()->getCount();
			break;

			case "x":
				$value = (int) $player->getPosition()->getX();
			break;

			case "y":
				$value = (int) $player->getPosition()->getY();
			break;

			case "z":
				$value = (int) $player->getPosition()->getZ();
			break;

			case "load":
				$value = $player->getServer()->getTickUsage();
			break;

			case "tps":
				$value = $player->getServer()->getTicksPerSecond();
			break;

			case "level_name":
			case "world_name":
				$value = $player->getWorld()->getDisplayName();
			break;

			case "level_folder_name":
			case "world_folder_name":
				$value = $player->getWorld()->getFolderName();
			break;

			case "ip":
				$value = $player->getNetworkSession()->getIp();
			break;

			case "ping":
				$value = $player->getNetworkSession()->getPing();
			break;

			case "health":
				$value = (int) $player->getHealth();
			break;

			case "max_health":
				$value = $player->getMaxHealth();
			break;

			case "xp_level":
				$value = (int) $player->getXpManager()->getXpLevel();
			break;

			case "xp_progress":
				$value = (int) $player->getXpManager()->getXpProgress();
			break;

			case "xp_remainder":
				$value = (int) $player->getXpManager()->getRemainderXp();
			break;

			case "xp_current_total":
				$value = (int) $player->getXpManager()->getCurrentTotalXp();
			break;

			case "time":
				$value = date(ScoreHudSettings::getTimeFormat());
			break;

			case "date":
				$value = date(ScoreHudSettings::getDateFormat());
			break;

			case "world_player_count":
				$value = count($player->getWorld()->getPlayers());
			break;
		}

		if (ScoreHudSettings::areMemoryTagsEnabled()) {
			$rUsage = Process::getRealMemoryUsage();
			$mUsage = Process::getAdvancedMemoryUsage();

			$globalMemory = "MAX";
			if ($this->plugin->getServer()->getConfigGroup()->getProperty("memory.global-limit") > 0) {
				$globalMemory = number_format(round($this->plugin->getServer()->getConfigGroup()->getProperty("memory.global-limit"), 2), 2) . " MB";
			}

			switch ($tags[1]) {
				case "memory_main_thread":
					$value = number_format(round(($mUsage[0] / 1024) / 1024, 2), 2) . " MB";
				break;

				case "memory_total":
					$value = number_format(round(($mUsage[1] / 1024) / 1024, 2), 2) . " MB";
				break;

				case "memory_virtual":
					$value = number_format(round(($mUsage[2] / 1024) / 1024, 2), 2) . " MB";
				break;

				case "memory_heap":
					$value = number_format(round(($rUsage[0] / 1024) / 1024, 2), 2) . " MB";
				break;

				case "memory_global":
					$value = $globalMemory;
				break;
			}
		}

		$tag->setValue((string) $value);
	}
}