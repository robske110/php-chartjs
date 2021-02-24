<?php
class Yaxis extends Axis{
	/** @var string */
	private $name;
	/** @var string */
	private $id;
	/** @var null|string */
	private $unit;
	/** @var bool */
	private $display = true;
	/** @var bool */
	private $displayGridLines = true;
	/** @var bool */
	private $stacked = false;
	/** @var null|float */
	private $min;
	/** @var null|float */
	private $max;
	
	public function __construct(string $name, ?string $unit = null, ?string $id = null){
		$this->name = $name;
		$this->unit = $unit;
		if($id === null){
			$this->id = $name;
		}else{
			$this->name = $id;
		}
	}
	
	public function getID(): string{
		return $this->id;
	}
	
	public function display(bool $display = true): Yaxis{
		$this->display = $display;
		return $this;
	}
	
	public function displayGridLines(bool $display = true): Yaxis{
		$this->displayGridLines = $display;
		return $this;
	}
	
	public function stacked(bool $stacked = true): Yaxis{
		$this->stacked = $stacked;
		return $this;
	}
	
	public function setUnit(?string $unit = null): Yaxis{
		$this->unit = $unit;
		return $this;
	}
	
	/**
	 * @param Dataset[] $datasets
	 *
	 * @return Yaxis
	 */
	public function addDatasets(array $datasets){
		foreach($datasets as $dataset){
			$this->addDataset($dataset);
		}
		return $this;
	}
	
	public function addDataset(Dataset $dataset): Yaxis{
		$dataset->setAssociatedYaxis($this);
		return $this;
	}
	
	public function setMinMax(?float $min = null, ?float $max = null): Yaxis{
		$this->min = $min;
		$this->max = $max;
		return $this;
	}
	
	public function parse(): string{
		$parser = new ConfigParser();
		$parser->addValue("id", $this->id);
		$parser->addValue("labelString", $this->name);
		$parser->addValue("display", $this->display);
		//TODO: custom ticks callback
		$parser->addValue("ticks", new ConfigParser([
			"min" => $this->min ?? "undefined",
			"max" => $this->max ?? "undefined",
			"callback" => "function(value, index, values){ return value+'".$this->unit."'; }"
		], false), false);
		$parser->addValue("unit", $this->unit); //for tooltip callback
		$parser->addValue("stacked", $this->stacked);
		$parser->addValue(
			"gridLines", new ConfigParser([
			"color" => new Colour(85, 85, 85),
			"zeroLineColor" => new Colour(85, 85, 85),
			"display" => $this->displayGridLines
		]), false
		);
		return $parser->getParsed();
	}
}