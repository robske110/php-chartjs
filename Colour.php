<?php
class Colour{
	/** @var int */
	private $r;
	/** @var int */
	private $g;
	/** @var int */
	private $b;
	/** @var null|float */
	private $a;
	
	public function __construct(int $r, int $g, int $b, float $a = null){
		$this->r = $r;
		$this->g = $g;
		$this->b = $b;
		$this->a = $a;
	}
	
	public function parse(): string{
		if($this->a === null){
			return "rgb(".$this->r.",".$this->g.",".$this->b.")";
		}else{
			return "rgba(".$this->r.",".$this->g.",".$this->b.",".$this->a.")";
		}
	}
	
	public function __toString(): string{
		return $this->parse();
	}
}