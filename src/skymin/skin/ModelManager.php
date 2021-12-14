<?php
declare(strict_types = 1);

namespace skymin\skin;

use function array_map;
use function json_encode;
use function dirname;
use function is_dir;
use function mkdir;
use function file_put_contents;

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
	
	public function getJson() : string{
		return json_encode($this->model);
	}
	
	public function save(string $path) : void{
		$dir = dirname($path);
		if(!is_dir($dir)){
			mkdir($dir);
		}
		file_put_contents($path, json_encode($this->model));
	}
	
	public function mergeModel(ModelManager $manager) : ModelManager{
		$result = $this->model;
		$model = &$result['minecraft:geometry'][0];
		if(($offset1 = $this->getOffset()) !== ($offset2 = $manager->getOffset())){
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
		if($this->getTextureWidth() < $manager->getTextureWidth()){
			$model['description']['texture_width'] = $manager->getTextureWidth();
		}
		if($this->getTextureHeight() < $manager->getTextureHeight()){
			$model['description']['texture_height'] = $manager->getTextureHeight();
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
		$model['bones'] = $resultBones;
		return new self($result);
	}
	
}