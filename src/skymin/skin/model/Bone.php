<?php
declare(strict_types = 1);

namespace skymin\skin\model;

use function array_merge;

final class Bone{
	
	public function __construct(public ModelManager $manager, public array $bone){}
	
	public function getName() : string{
		return $this->bone['name'];
	}
	
	public function getParentName() : ?string{
		if(isset($this->bone['parent'])){
			return $this->bone['parent'];
		}
		return null;
	}
	
	public function getParent() : ?Bone{
		if(isset($this->bone['parent'])){
			return $this->manager->getBone($this->bone['parent']);
		}
		return null;
	}
	
	public function getPivot() : array{
		return $this->bone['pivot'];
	}
	
	public function getCubes() : ?array{
		if(isset($this->bone['cubes'])){
			return $this->bone['cubes'];
		}
		return null;
	}
	
	public function mergeCubes(array $cubes) : void{
		if(isset($this->bone['cubes'])){
			$this->bone['cubes'] = array_merge($this->bone['cubes'], $cubes)
		}
	}
	
}