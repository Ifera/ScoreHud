<?php
declare(strict_types = 1);

namespace Ifera\ScoreHud\scoreboard;

class ScoreTag{

	/** @var string */
	private $name;
	/** @var string */
	private $value = "";

	public function __construct(string $name, string $value){
		$this->name = $name;
		$this->value = $value;
	}

	public function getId(): string{
		return "{" . $this->name . "}";
	}

	public function getName(): string{
		return $this->name;
	}

	public function getValue(): string{
		return $this->value;
	}

	public function setValue(string $value): void{
		$this->value = $value;
	}

	public function __toArray(): array{
		return [
			"name"  => $this->name,
			"value" => $this->value
		];
	}
}