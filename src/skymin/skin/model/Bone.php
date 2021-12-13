<?php
declare(strict_types = 1);

namespace skymin\skin\model;

final class Bone{
	
	public function __construct(public ModelManager $manager, private array $bone){}
	
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
	
}