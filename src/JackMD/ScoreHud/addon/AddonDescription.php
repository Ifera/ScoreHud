<?php

declare(strict_types = 1);

namespace JackMD\ScoreHud\addon;

class AddonDescription{

	/** @var array */
	private $map;

	/** @var string */
	private $name;
	/** @var string */
	private $main;

	/**
	 * @param string|array $yamlString
	 */
	public function __construct($yamlString){
		$this->loadMap(!is_array($yamlString) ? yaml_parse($yamlString) : $yamlString);
	}

	/**
	 * @param array $addon
	 */
	private function loadMap(array $addon){
		$this->map = $addon;

		$this->name = $addon["name"];

		if(preg_match('/^[A-Za-z0-9 _.-]+$/', $this->name) === 0){
			throw new AddonException("Invalid AddonDescription name.");
		}

		$this->name = str_replace(" ", "_", $this->name);
		$this->main = $addon["main"];
 	}

	/**
	 * @return string
	 */
	public function getName(): string{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getMain(): string{
		return $this->main;
	}


	/**
	 * @return array
	 */
	public function getMap(): array{
		return $this->map;
	}
}
