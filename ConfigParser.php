<?php
class ConfigParser{
	/** @var string */
	private $parsed = "{";
	
	public function __construct(?array $values = null, bool $literalStrings = true){
		if($values !== null){
			$this->addValues($values, $literalStrings);
		}
	}
	
	public function addValues(array $values, bool $literalStrings = true): ConfigParser{
		foreach($values as $key => $value){
			$this->addValue($key, $value, $literalStrings);
		}
		return $this;
	}
	
	/**
	 * @param string     $key
	 * @param string|int $content
	 * @param bool       $literalString Whether to parse content as a literal string or as an expression. This is
	 *                                  ignored if content is an integer or a boolean.
	 *
	 * @return ConfigParser This instance
	 */
	public function addValue(string $key, $content, bool $literalString = true): ConfigParser{
		if(is_bool($content)){
			$content = $content ? "true" : "false";
			$literalString = false;
		}
		if($literalString && !is_int($content)){
			$this->parsed .= $key.": '".(string) $content."',\n";
		}else{
			$this->parsed .= $key.": ".(string) $content.",\n";
		}
		return $this;
	}
	
	public function getParsed(): string{
		return $this->parsed."}";
	}
	
	public function __toString(): string{
		return $this->getParsed();
	}
}