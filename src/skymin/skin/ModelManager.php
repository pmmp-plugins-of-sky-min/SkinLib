<?php
declare(strict_types = 1);

namespace skymin\skin\model;

use function array_map;
use function json_encode;

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
		$this->name = $des['identifier'];
		$this->width = $des['visible_bounds_width'];
		$this->height = $des['visible_bounds_height'];
		$this->offset = $des['visible_bounds_offset'];
		$this->textureW = $des['texture_width'];
		$this->textureH = $des['texture_height'];
		foreach($model['bones'] as $key => $value){
			$this->bones[$value['name']] = $value;
		}
	}
	
	public function getBone(string $name) : ?array{
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
		return $this->height;
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
		$model = $this->model;
		if(($offset1 = $this->getOffset()) !== ($offset2 = $manager->getOffset())){
			$model['minecraft:geometry'][0]['description']['visible_bounds_offset'] = array_map(function(float $i, float $j) : float{
				return ($i + $j) / 2;
			}, $offset1, $offset2);
		}
		if($this->getWidth() < $manager->getWidth()){
			$model['minecraft:geometry'][0]['description']['visible_bounds_width'] = $manager->getWidth();
		}
		if($this->getHeight() < $manager->getHeight()){
			$model['minecraft:geometry'][0]['description']['visible_bounds_height'] = $manager->getHeight();
		}
		if($this->getTextureWidth() < $manager->getTextureWidth()){
			$model['minecraft:geometry'][0]['description']['texture_width'] = $manager->getTextureWidth();
		}
		if($this->getTextureHeight() < $manager->getTextureHeight()){
			$model['minecraft:geometry'][0]['description']['texture_height'] = $manager->getTextureHeight();
		}
		$resultBones = [];
		$tbones = $this->getBones();
		$mbones = $manager->getBones();
		foreach($mbones as $name => $bone){
			if(isset($tbones[$name])){
				$tbone = $this->getBone($name);
				if(isset($tbone['bone']) && isset($bone['parent']) && ($tbone['bone'] === $bone['parent']) && ($tbone['parent'] === $bone['parent'])){
					if(isset($bone['cubes']) && isset($tbone['cubes'])){
						foreach($tbone['cubes'] as $index => $cube){
							$bone['cubes'][] = $cube;
						}
						$resultBones[] = $bone;
						unset($tbones[$name]);
						continue;
					}elseif(!isset($bone['cubes']) && !isset($tbone['cubes'])){
						$resultBones[] = $bone;
						unset($tbones[$name]);
						continue;
					}
				}
				$count = 1;
				do{
					$editname = $name .$count;
					$count++;
				}while(isset($tbones[$editname]));
				$bone['name'] = $editname;
				$resultBones[] = $bone;
				$resultBones[] = $tbones[$name];
				unset($tbones[$name]);
				continue;
			}
			$resultBones[] = $bone;
		}
		foreach($tbones as $name  => $bone){
			$resultBones[] = $bone;
		}
		$model['minecraft:geometry'][0]['bones'] = $resultBones;
		return json_encode($model);
	}
	
}