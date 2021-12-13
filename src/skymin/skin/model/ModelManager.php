<?php
declare(strict_types = 1);

namespace skymin\skin\model;

final class ModelManager{
	
	private array $bones;
	
	public function __construct(private array $model){
		$this->init();
	}
	
	public function init(){
		foreach($this->model['minecraft:geometry'][0]['bones'] as $key => $value){
			$bone = new Bone($value, $this);
			$this->bones[$bone->getName()] = $bone;
		}
	}
	
	public function getBone(string $name) : ?Bone{
		if(isset($this->bones[$name])){
			return $this->bones[$name];
		}
		return null;
	}
	
	public function getBones() : array{
		return $this->bones;
	}
	
}