<?php
class Graph{
	private static $isInited = false;
	
	/** @var bool */
	private $isRendered = false;
	
	/** @var bool */
	private $canvasRendered = false;
	
	/** @var string */
	private $uniqueName;
	
	/** @var string A GraphDisplayType constant */
	private $type;
	
	/** @var Yaxis[] */
	private $yAxes;
	
	/** @var Xaxis */
	private $xAxis;
	
	/** @var bool */
	private $animate = true;
	
	/** @var float */
	private $lineTension = 0.4;
	
	/**
	 * Initializes some global settings needed for every Graph
	 */
	public static function init(){
		if(self::$isInited){
			return;
		}
		?>
		<script type="text/javascript">
			Chart.defaults.global.defaultFontColor = "#fff";
			window.defaultLegendClickHandler = Chart.defaults.global.legend.onClick;
			var chartStore = {};
		</script>
		<?php
		self::$isInited = true;
	}
	
	public function __construct(string $uniqueName, string $type = GraphDisplayType::LINE){
		$this->uniqueName = $uniqueName;
		$this->type = $type;
	}
	
	public function setAnimate(bool $animate){
		$this->animate = $animate;
		return $this;
	}
	
	public function setLineTension(float $lineTension){
		$this->lineTension = $lineTension;
		return $this;
	}
	
	public function canvas(){
		if(!$this->canvasRendered){
			?><canvas id="<?php echo($this->uniqueName); ?>"></canvas><?php
			$this->canvasRendered = true;
		}
	}
	
	public function render(){
		if($this->isRendered){
			throw new \Exception("Cannot re-render a Graph!");
		}
		self::init();
		if($this->xAxis === null){
			throw new \Exception("Graph needs a xAxis!");
		}
		if(empty($this->yAxes) === null){
			throw new \Exception("Graph needs at least one yAxis!");
		}
		
		$parsedDataSets = "";
		foreach($this->xAxis->getDatasets() as $dataset){
			$parsedDataSets .= $dataset->parse().",";
			//$datasetAssociations[] = $this->yAxes[$dataset->getAssociatedYaxis()];
			$datasetColours[] = $dataset->getColour()->parse();
		}
		$this->canvas();
		?>
		<script type="text/javascript">
			function getHiddenState(c, d, i){
				let meta = c.getDatasetMeta(d);
				let hidden = meta.hidden === null ? c.data.datasets[d].hidden : meta.hidden;
				return i === d ? !hidden : hidden;
			}
			chart = new Chart(document.getElementById('<?php echo($this->uniqueName); ?>').getContext('2d'), {
				type: '<?php echo($this->type); ?>',
				data: {
					datasets: [<?php echo($parsedDataSets); ?>]
				},
				options: {
					<?php if(!$this->animate){ echo("animation: false,"); } ?>
					elements: { point: { radius: 4, pointRadius: 6}, line: { tension: <?php echo($this->lineTension);?> } },
					hover: {
						intersect: false
					},
					tooltips: {
						position: 'nearest',
						mode: 'label',
						intersect: false,
						callbacks: {
							labelColor: function(tooltipItem, chart) {
								toolTipColor = chartStore.<?php echo($this->uniqueName); ?>.datasetColours[tooltipItem.datasetIndex];
								return {
									borderColor: toolTipColor,
									backgroundColor: toolTipColor
								};
							},
							label: function(tooltipItem, data) {
								let label = data.datasets[tooltipItem.datasetIndex].label || '';

								if (label) {
									label += ': ';
								}
								label += tooltipItem.yLabel;
								
								let yAxes = chartStore.<?php echo($this->uniqueName); ?>.chart.config.options.scales.yAxes;
								for (yAxis in yAxes) {
									if(yAxes[yAxis].id == data.datasets[tooltipItem.datasetIndex].yAxisID){
										label += yAxes[yAxis].unit;
									}
								}
								return label;
							},
							title: function(tooltipItem, data) {
								return chartStore.<?php echo($this->uniqueName); ?>.chart.config.options.scales.xAxes[0].labels[tooltipItem[0].index];
							}
						}
					},
					scales: {
						xAxes: [<?php echo($this->xAxis->parse()); ?>],
						yAxes: [<?php foreach($this->yAxes as $yAxis){ echo($yAxis->parse()); echo(","); }?>],
					},
					legend: {
						onClick: function(t, e){
							//Don't touch this ever again. (Unless you suddenly learned JS properly)
							let i = e.datasetIndex,
								c = chartStore.<?php echo($this->uniqueName); ?>.chart,
								a = c.getDatasetMeta(i);
							console.log(a.yAxisID); //yAxisID
							console.log(i);
							console.log(e);
							//getting list of datasets associated with a.yAxisID
							let associatedDatasets = [];
							for(let datasetID = 0; datasetID < c.data.datasets.length; ++datasetID){
								//console.log(c.data.datasets[datasetID]);
								//console.log(c.getDatasetMeta(datasetID));
								if(c.data.datasets[datasetID].yAxisID === a.yAxisID){
									associatedDatasets.push(getHiddenState(c, datasetID, i));
									continue;
								}
								if(a.yAxisID === c.options.scales.yAxes[0].id){ //dataset is part of default, also need to search for undefined aIDs
									if(c.data.datasets[datasetID].yAxisID === undefined){
										associatedDatasets.push(getHiddenState(c, datasetID, i));
									}
								}
							}
							console.log(associatedDatasets);
							let doDisplay = false;
							for(let hidden of associatedDatasets){
								if(!hidden){
									doDisplay = true;
								}
							}
							console.log("doDisplay: "+doDisplay);
							for(let yAxisID = 0; yAxisID < c.options.scales.yAxes.length; ++yAxisID){
								//console.log(c.options.scales.yAxes[yAxisID].id);
								//console.log(a.yAxisID);
								if(c.options.scales.yAxes[yAxisID].id === a.yAxisID){
									c.options.scales.yAxes[yAxisID].display = doDisplay;
								}
							}
							a.hidden = null === a.hidden ? !c.data.datasets[i].hidden : null, c.update()
						}
					},
				}
			});
			chartStore.<?php echo($this->uniqueName)?> = {
				datasetColours: JSON.parse('<?php echo(json_encode($datasetColours)); ?>'),
				chart: chart
			};
		</script>
		<?php
	}
	
	public function setXaxis(Xaxis $xAxis){
		$this->xAxis = $xAxis;
	}
	
	public function addYaxis(Yaxis $yAxis){
		$this->yAxes[$yAxis->getID()] = $yAxis;
	}
}