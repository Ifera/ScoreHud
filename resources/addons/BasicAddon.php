<?php
declare(strict_types = 1);

/**
 * @name BasicAddon
 * @main JackMD\ScoreHud\Addons\BasicAddon
 */
namespace JackMD\ScoreHud\Addons
{
	use JackMD\ScoreHud\addon\AddonBase;
	use pocketmine\Player;

	class BasicAddon extends AddonBase{

		/**
		 * @param Player $player
		 * @param string $string
		 * @return array
		 */
		public function getProcessedTags(Player $player, string $string): array{
			$tags = [];

			if(strpos($string, "{name}") !== false){
				$tags["{name}"] = str_replace("{name}", $player->getName(), $string);
			}

			if(strpos($string, "{online}") !== false){
				$tags["{online}"] = str_replace("{online}", count($this->getScoreHud()->getServer()->getOnlinePlayers()), $string);
			}

			if(strpos($string, "{max_online}") !== false){
				$tags["{max_online}"] = str_replace("{max_online}", $this->getScoreHud()->getServer()->getMaxPlayers(), $string);
			}

			if(strpos($string, "{item_name}") !== false){
				$tags["{item_name}"] = str_replace("{item_name}", $player->getInventory()->getItemInHand()->getName(), $string);
			}

			if(strpos($string, "{item_id}") !== false){
				$tags["{item_id}"] = str_replace("{item_id}", $player->getInventory()->getItemInHand()->getId(), $string);
			}

			if(strpos($string, "{item_meta}") !== false){
				$tags["{item_meta}"] = str_replace("{item_meta}", $player->getInventory()->getItemInHand()->getDamage(), $string);
			}

			if(strpos($string, "{item_count}") !== false){
				$tags["{item_count}"] = str_replace("{item_count}", $player->getInventory()->getItemInHand()->getCount(), $string);
			}

			if(strpos($string, "{x}") !== false){
				$tags["{x}"] = str_replace("{x}", intval($player->getX()), $string);
			}

			if(strpos($string, "{y}") !== false){
				$tags["{y}"] = str_replace("{y}", intval($player->getY()), $string);
			}

			if(strpos($string, "{z}") !== false){
				$tags["{z}"] = str_replace("{z}", intval($player->getZ()), $string);
			}

			if(strpos($string, "{load}") !== false){
				$tags["{load}"] = str_replace("{load}", $this->getScoreHud()->getServer()->getTickUsage(), $string);
			}

			if(strpos($string, "{tps}") !== false){
				$tags["{tps}"] = str_replace("{tps}", $this->getScoreHud()->getServer()->getTicksPerSecond(), $string);
			}

			if(strpos($string, "{level_name}") !== false){
				$tags["{level_name}"] = str_replace("{level_name}", $player->getLevel()->getName(), $string);
			}

			if(strpos($string, "{level_folder_name}") !== false){
				$tags["{level_folder_name}"] = str_replace("{level_folder_name}", $player->getLevel()->getFolderName(), $string);
			}

			if(strpos($string, "{ip}") !== false){
				$tags["{ip}"] = str_replace("{ip}", $player->getAddress(), $string);
			}

			if(strpos($string, "{ping}") !== false){
				$tags["{ping}"] = str_replace("{ping}", $player->getPing(), $string);
			}

			if(strpos($string, "{name}") !== false){
				$tags["{time}"] = str_replace("{time}", date($this->getScoreHud()->getConfig()->get("time-format")), $string);
			}

			if(strpos($string, "{date}") !== false){
				$tags["{date}"] = str_replace("{date}", date($this->getScoreHud()->getConfig()->get("date-format")), $string);
			}

			return $tags;
		}
	}
}