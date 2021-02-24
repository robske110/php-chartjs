<?php
class Xaxis extends Axis{
	/** @var bool */
	private $displayGridLines = true;
	
	/** @var Dataset[] */
	private $datasets = [];
	/** @var array */
	private $labels = [];
	
	public function __construct(?array $labels = null){
		if($labels !== null){
			$this->setLabels($labels);
		}
	}
	
	public function displayGridLines(bool $display = true): Xaxis{
		$this->displayGridLines = $display;
		return $this;
	}
	
	/**
	 * @param Dataset[] $datasets
	 *
	 * @return Xaxis
	 */
	public function addDatasets(array $datasets){
		foreach($datasets as $dataset){
			$this->addDataset($dataset);
		}
		return $this;
	}
	
	public function addDataset(Dataset $dataset){
		$this->datasets[] = $dataset;
		return $this;
	}
	
	public function setLabels(array $labels){
		$this->labels = $labels;
		return $this;
	}
	
	/**
	 * @return Dataset[]
	 */
	public function getDatasets(): array{
		return $this->datasets;
	}
	
	public function parse(): string{
		$parser = new ConfigParser();
		$parser->addValue("display", "true", false);
		$parser->addValue("labels", json_encode($this->labels), false);
		$parser->addValue("type", "time");
		$parser->addValue(
			"time", (new ConfigParser([
				"parser" => "dd.MM.yyyy HH:mm:ss",
				"unit" => "minute",
				"stepSize" => 5
			]))->addValue("displayFormats", new ConfigParser(["minute" => "dd / HH:mm"]), false), false
		);
		$parser->addValue(
			"ticks", (new ConfigParser())->addValue("source", "labels")->addValue("minRotation", 45)->addValue("maxRotation", 90)->addValue("autoSkip", true)->addValue("autoSkipPadding", 70), false
		);
		//TODO: stacked
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