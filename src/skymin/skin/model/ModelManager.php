<?php
declare(strict_types = 1);

namespace skymin\skin\model;

final class ModelManager{
	
	private array $bones;
	private string $name;
	private float $width;
	private float $height;
	private array $offset;
	
	public function __constuct(private array $model){
		$this->init();
	}
	
	public function init(){
		$model = $this->model['minecraft:geometry'][0];
		$des = $model['description'];
		$this->name = $des['identfifier'];
		$this->width = $des['visible_bounds_width'];
		$this->height = $des['visible_bounds_height'];
		$this->offset = $des['visible_bounds_offset'];
		foreach($model['bones'] as $key => $value){
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
	
	public function getName() : string{
		return $this->name;
	}
	
	public function getWidth() : float{
		return $this->width;
	}
	
	public function getHeight() : float{
		return $this->float;
	}
	
	public function getOffset() : array{
		return $this->offset;
	}
	
}