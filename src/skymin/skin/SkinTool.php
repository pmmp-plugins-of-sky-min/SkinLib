<?php
declare(strict_types = 1);

namespace skymin\skin;

use pocketmine\utils\SingletonTrait;

use function file_exists;
use function file_get_contents;
use function rmdir;
use function json_encode;
use function json_decode;
use function strlen;
use function chr;
use function ord;
use function intdiv;
use function count;
use function array_keys;
use function in_array;

use GdImage;
use function imagecreatefrompng;
use function imagecopy;
use function imagepng;
use function imagecolorat;
use function imagesx;
use function imagesy;
use function imagecreatetruecolor;
use function imagecopyresampled;
use function imagecolorallocatealpha;
use function imagecolortransparent;
use function imagealphablending;
use function imagesavealpha;
use function imagefill;
use function imagesetpixel;

const SKIN = [
	64 * 32 * 4 ,
	64 * 64 * 4,
	128 * 128 * 4,
];

const SKIN_W = [
	64 * 32 * 4 => 64,
	64 * 64 * 4 => 64,
	128 * 128 * 4 => 128
];

const SKIN_H = [
	64 * 32 * 4 => 32,
	64 * 64 * 4 => 64,
	128 * 128 * 4 => 128
];

final class SkinTool{
	use SingletonTrait;
	
	public const PATH = 0;
	public const JSON = 1;
	
	public function getImg(string $path) :?GdImage{
		$img = imagecreatefrompng($path);
		if($img) return $img;
		return null;
	}
	
	public function saveImg(GdImage $img, string $path) :void{
		imagepng($img, $path);
	}
	
	public function getSkinData(GdImage $img) :?string{
		$h = imagesy($img);
		$w = imagesx($img);
		if(!in_array($w * $h * 4, SKIN)) return null;
		$skindata = '';
		for($y = 0; $y < $h; $y++){
			for($x = 0; $x < $w; $x++){
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
	
	public function dataToImage(string $skindata) :?GdImage{
		$size = strlen($skindata);
		if(!in_array($size, SKIN)) return null;
		$p = 0;
		$width = SKIN_W[$size];
		$height = SKIN_H[$size];
		$image = imagecreatetruecolor($width, $height);
		imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
		for ($y = 0; $y < $height; $y++) {
			for ($x = 0; $x < $width; $x++) {
				$r = ord($skindata[$p]);
				$p++;
				$g = ord($skindata[$p]);
				$p++;
				$b = ord($skindata[$p]);
				$p++;
				$a = 127 - intdiv(ord($skindata[$p]), 2);
				$p++;
				$col = imagecolorallocatealpha($image, $r, $g, $b, $a);
				imagesetpixel($image, $x, $y, $col);
			}
		}
		imagesavealpha($image, true);
		return $image;
	}
	
	public function mergeModel(string $model1, string $model2, int $mode = self::JSON) :?string{
		if($mode === self::PATH){
			if(!(file_exists($model1) && file_exists($model2))) return null;
			$model1 = file_get_contents($model1);
			$model2 = file_get_contents($model2);
		}
		$model1 = json_decode($model1, true);
		$model2 = json_decode($model2, true);
		$bones = array();
		$m1 = $model1['minecraft:geometry'][0]['bones'];
		$m2 = $model2['minecraft:geometry'][0]['bones'];
		for($i = 0; $i < count($m1); $i++) {
			$name = $m1[$i]['name'];
			$bones[$name]['pivot'] = $m1[$i]['pivot'];
			if(isset($m1[$i]['parent'])){
				$bones[$name]['parent'] = $m1[$i]['parent'];
			}
		}
		$changeparent = array();
		for($i = 0; $i < count($m2); $i++) {
			$name = $m2[$i]['name'];
			if(isset($bones[$name])){
				if(!isset($m2[$i]['cubes'])){
					if($bones[$name]['pivot'] === $m2[$i]['pivot']){
						if(isset($bones[$name]['parent']) && isset($m2[$i]['parent'])){
							if($bones[$name]['parent'] === $m2[$i]['parent']){
								continue;
							}
						}else{
							continue;
						}
					}else{
						$changename = $name . '1';
						$changeparent[$name] = $changename;
						$m2[$i]['name'] = $changename;
					}
				}else{
					$parent = $m2[$i]['parent'];
					$m2[$i]['name'] = $name . '1';
					if(in_array($parent,  array_keys($changeparent))){
						$m2[$i]['parent'] = $changeparent[$parent];
					}
				}
			}
			 $m1[] = $m2[$i];
		}
		$model1['minecraft:geometry'][0]['bones'] = $m1;
		return json_encode($model1);
	}
	
	public function mergeImage(GdImage $img1, GdImage $img2) :?GdImage{
		$img1_w = imagesx($img1);
		$img1_h = imagesy($img1);
		$img2_w = imagesx($img2);
		$img2_h = imagesy($img2);
		if(!in_array($img1_w * $img1_h * 4, SKIN) && !in_array($img2_w * $img2_h * 4, SKIN)) return null;
		if($img1_w > $img2_w){
			$size = $img1_w;
		}else{
			$size = $img2_w;
		}
		if(!($img1_h === $size && $img1_w === $size)){
			$img1 = $this->imgPix($img1, $size);
		}
		if(!($img2_h === $size && $img2_w === $size)){
			$img2 = $this->imgPix($img2, $size);
		}
		if(imagecopy($img1, $img2, 0, 0, 0, 0, $size, $size)) return $img1;
		return null;
	}
	
	public function imgPix(GdImage $img, int $size) :GdImage{
		$result = imagecreatetruecolor($size, $size);
		imagecolortransparent($result, imagecolorallocate($result, 0, 0, 0, 127));
		imagealphablending($result, false);
		imagesavealpha($result, true);
		imagecopyresampled($result, $img, 0, 0, 0, 0, $size, $size, imagesx($img), imagesy($img));
		return $result;
	}
	
}