<?php
declare(strict_types = 1);

namespace skymin\skin;

use pocketmine\utils\SingletonTrait;

use function file_exists;
use function file_get_contents;
use function json_encode;
use function json_decode;
use function chr;
use function imagecreatefrompng;
use function imagecopy;
use function imagepng;
use function imagecolorat;

final class SkinTool{
	use SingletonTrait;
	
	public const SIZE64 = 64;
	public const SIZE128 = 128;
	
	public const PATH = 0;
	public const JSON = 1;
	
	public function getImg(string $path) :?\GdImage{
		if(!file_exists($path)) return null;
		return imagecreatefrompng($path);
	}
	
	public function saveImg(\GdImage $img, string $path) :void{
		imagepng($img, $path);
	}
	
	public function getSkinData(\GdImage $img, int $size = self::SIZE64) :string{
		$skindata = '';
		for($y = 0; $y < $size; $y++){
			for($x = 0; $x < $sizd; $x++){
				$colorat = imagecolorat($img, $x, $y);
				$a = ((~((int) ($colorat >> 24))) << 1) & 0xff;
				$r = ($colorat >> 16) & 0xff;
				$g = ($colorat >> 8) & 0xff;
				$b = $colorat & 0xff;
				$skindata .= chr($r) . chr($g) . chr($b) . chr($a);
			}
		}
		return $skindata;
	}
	
	public function mergeModel(string $model1, string $model2, int $mode = self::JSON) :?string{
		if($mode === self::PATH){
			if(!(file_exists($model1) && file_exists($model2))) return null;
			$model1 = file_get_contents($model1);
			$model2 = file_get_contents($model2);
		}
		$model1 = json_decode($model1);
		$model2 = json_decode($model2);
		//todo
		return json_encode($model1);
	}
	
	public function mergeImage(\GdImage $img1, \GdImage $img2, int $size = self::SIZE64) :?\GdImage{
		if(imagecopy($img1, $img2, 0, 0, 0, 0, $size, $size)) return $img1;
		return null;
	}
	
}