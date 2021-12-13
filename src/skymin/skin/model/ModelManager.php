<?php
declare(strict_types = 1);

namespace skymin\skin\model;

use function array_map;
use function json_encode;
use const PHP_INT_MAX;

final class ModelManager{
	
	private array $bones;
	private string $name;
	private float $width;
	private float $height;
	private array $offset;
	private int $textureW;
	private int $textureH;
	
	public function __construct(private array $model){
		$this->init();
	}
	
	public function init(){
		$model = $this->model['minecraft:geometry'][0];
		$des = $model['description'];
		$this->name = $des['identfifier'];
		$this->width = $des['visible_bounds_width'];
		$this->height = $des['visible_bounds_height'];
		$this->offset = $des['visible_bounds_offset'];
		$this->textureW = $des['texture_width'];
		$this->textureH = $des['texture_height'];
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
	
	public function setBone(Bone $bone) : void{
		$this->bones[$bone->getName()] = $bone;
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
	
	public function getTextureWidth() : int{
		return $this->textureW;
	}
	
	public function getTextureHeight() : int{
		return $this->textureH;
	}
	
	public function mergeModel(ModelManager $manager) : string{
		$model = $this->model['minecraft:geometry'][0];
		if(($offset1 = $this->getOffset()) !== ($offset2 = $manager->getOffset)){
			$model['description']['visible_bounds_offset'] = array_map(function(float $i, float $j) : float{
				return ($i + $j) / 2;
			}, $offset1, $offset2);
		}
		if($this->getWidth() < $manager->getWidth()){
			$model['description']['visible_bounds_width'] = $manager->getWidth();
		}
		if($this->getHeight() < $manager->getHeight()){
			$model['description']['visible_bounds_height'] = $manager->getHeight();
		}
		if($this->getTextureWidth() < $manager->getTextureWidth){
			$model['description']['texture_width'] = $manager->getTextureWidth();
		}
		if($this->getTextureHeight() < $manager->getTextureHeight()){
			$model['description']['texture_height'] = $manager->getTextureHeight();
		}
		$resultbone = [];
		$tbones = $this->getBones;
		$mbones = $manager->getBones();
		foreach($mbones as $name => $bone){
			if(isset($tbones[$name])){
				$tbone = $this->getBone($name);
				if($tbone->getParentName() === $bone->getParentName()){
					$tbone->mergeCubes($bone->getCubes());
					$resultbone[] = $tbone->bone;
				}
				for($i = 1; $i <= PHP_INT_MAX; $i){
					if(isset($tbones[$name . $i])) continue;
					$bone->setName($name . $i);
					$resultbone[] = $bone->bone;
					break;
				}
				continue;
			}
			$resultbone[] = $bone;
		}
		foreach ($tbone as $name => $bone){
			if(!isset($mbones[$name])){
				$resultbone[] = $bone;
			}
		}
		$model['bones'] = $resultbone;
		return json_encode($model);
	}
	
}