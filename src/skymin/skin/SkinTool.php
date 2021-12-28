<?php
/**
 *      _                    _       
 *  ___| | ___   _ _ __ ___ (_)_ __  
 * / __| |/ / | | | '_ ` _ \| | '_ \ 
 * \__ \   <| |_| | | | | | | | | | |
 * |___/_|\_\\__, |_| |_| |_|_|_| |_|
 *           |___/ 
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the MIT License. see <https://opensource.org/licenses/MIT>.
 * 
 * @author skymin
 * @link   https://github.com/sky-min
 * @license https://opensource.org/licenses/MIT MIT License
 * 
 *   /\___/\
 * 　(∩`・ω・)
 * ＿/_ミつ/￣￣￣/
 * 　　＼/＿＿＿/
 *
 */

declare(strict_types = 1);

namespace skymin\skin;

use pocketmine\utils\SingletonTrait;

use function file_exists;
use function file_get_contents;
use function json_decode;
use function strlen;
use function chr;
use function intdiv;
use function in_array;

use function imagecreatefrompng;
use function imagecolorallocatealpha;
use function imagesetpixel;
use function imagefill;

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
	
	public const IMAGE_TYPE_PATH = 0;
	public const IMAGE_TYPE_DATA = 1;
	
	public function getImageTool(string $input, int $type = self::IMAGE_TYPE_PATH) : ImageTool{
		$img = null;
		if($type === self::IMAGE_TYPE_PATH){
			$img = imagecreatefrompng($path);
		}elseif($type === self::IMAGE_TYPE_PATH){
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
			$img = $image;
		}
		return $img === null ? $img : new ImageTool($img);
	}
	
	public const MODEL_TYPE_PATH = 0;
	public const MODEL_TYPE_JSON = 1;
	
	public function getModelTool(string $model, int $type = self::MODEL_TYPE_JSON){
		if($mode === self::MODE_PATH){
			if(!file_exists($model)) return null;
			$model = file_get_contents($model);
		}
		$model = new ModelTool(json_decode($model, true));
		return $model;
	}
	
}