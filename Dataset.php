<?php
class Dataset{
	/** @var string */
	private $label;
	
	/** @var string A GraphDisplayType constant */
	private $type;
	
	/** @var Colour */
	private $colour;
	
	/** @var Colour|null */
	private ?Colour $backgroundColour;
	
	/** @var array|null */
	private $data = [];
	
	/** @var bool */
	private $steppedLine = false;
	
	/** @var bool */
	private $hidden;
	
	/** @var string|null */
	private $yAxisID = null;
	
	public function __construct(string $label, array $data = [], ?Colour $colour = null, ?Colour $backgroundColour = null, bool $hidden = false, string $type = GraphDisplayType::LINE){
		$this->label = $label;
		$this->type = $type;
		$this->data = $data;
		$this->hidden = $hidden;
		if($colour === null){
			$this->colour = new Colour(255, 255, 255);
		}else{
			$this->colour = $colour;
		}
		$this->backgroundColour = $backgroundColour;
	}
	
	public function setLabel(string $label){
		$this->label = $label;
	}
	
	public function setData(array $data){
		$this->data = $data;
	}
	
	public function setColour(Colour $colour){
		$this->colour = $colour;
	}
	
	public function setAssociatedYaxis(Yaxis $yaxis){
		$this->yAxisID = $yaxis->getID();
	}
	
	public function getColour(): Colour{
		return $this->colour;
	}
	
	public function getBackgroundColour(){
		return $this->backgroundColour;
	}
	
	public function setSteppedLine(bool $steppedLine = true){
		$this->steppedLine = $steppedLine;
	}
	
	public function setHidden(bool $hidden = true){
		$this->hidden = $hidden;
	}
	
	public function parse(): string{
		$parser = new ConfigParser();
		$parser->addValue("label", $this->label);
		$parser->addValue("borderColor", $this->colour);
		if($this->backgroundColour !== null){
			$parser->addValue("backgroundColor", $this->backgroundColour);
		}
		$parser->addValue("data","JSON.parse('".json_encode($this->data)."')", false);
		$parser->addValue("pointHoverRadius", 4);
		$parser->addValue("pointBorderColor", new Colour(0, 0, 0, 0));
		$parser->addValue("pointBackgroundColor", new Colour(0, 0, 0, 0));
		$parser->addValue("pointHoverBackgroundColor", $this->colour);
		$parser->addValue("pointHoverBorderColor", $this->colour);
		$parser->addValue("borderWidth", 1.5);
		$parser->addValue("type", $this->type); //bar or line etc
		$parser->addValue("steppedLine", $this->steppedLine);
		$parser->addValue("hidden", $this->hidden);
		if($this->yAxisID !== null){
			$parser->addValue("yAxisID", $this->yAxisID); //associated yAxis
		}
		return $parser->getParsed();
	}
}