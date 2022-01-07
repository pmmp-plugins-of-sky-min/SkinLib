# SkinTool
pmmp virion

[English](https://github.com/sky-min/SkinTool#api--english)

[한국어](https://github.com/sky-min/SkinTool#api--korean)

# API -English
## Get ImageTool
```php
$imagetool = SkinTool::getImageTool(strig $input, $type);
```
For $type, SkinTool::IMAGE_TYPE_PATH or SkinTool::IMAGE_TYPE_DATA. SkinTool::IMAGE_TYPE_PATH is specified by default.
## Saving an Image
```php
$imagetool->saveImg($path);
```
Use to store acquired or modified images.
## Combining images
```php
$imagetool->mergeImage(ImageTool $image);
```
Combine the image specified in $imagetool with the image specified in $image.
## image conversion to skin data
```php
$imagetool->getSkinData();
```
## image resize
```php
$imagetool->resize(int $width, int $height);
```
## Combining Modeling
```php
$modelManager1 = SkinTool::getModelTool(string $model1, $mode);
$modelManager2 = SkinTool::getModelTool(string $model2, $mode);
$result = $modelManager1->mergeModel($modelManager2);
$json = $result->getJson();
```
For $type, SkinTool::MODEL_TYPE_PATH or SkinTool:MODEL_TYPE_JSON. SkinTool::MODEL_TYPE_JSON is specified by default.

<br/>

# API -Korean
## ImageTool 불러오기
```php
$imagetool = SkinTool::getImageTool(string $input, $type);
```
$type에는 SkinTool::IMAGE_TYPE_PATH 또는 SkinTool::IMAGE_TYPE_DATA를 사용하시면 됩니다. 기본값은 SkinTool::IMAGE_TYPE_PATH입니다.
## 이미지 저장하기
```php
$skintool->saveImg($path);
```
얻은 이미지나 수정한 이미지를 저장할때 사용합니다. 동일명의 파일이 있을 경우 오류가 발생 할 수 있습니다. 주의하여 사용해 주세요.
## 이미지 합치기
```php
$imagetool->mergeImage(ImageTool $image);
```
2개의 이미지를 합칩니다.
## 이미지 스킨데이터로 변환
```php
$imagetool->getSkinData();
```
## 이미지 크기 조절
```php
$skintool->resize(int $width, int $height);
```
## 모델링 합성하기
```php
$modelManager1 = SkinTool::getModelTool(string $model1, $mode);
$modelManager2 = SkinTool::getModelTool(string $model2, $mode);
$result = $modelManager1->mergeModel($modelManager2);
$json = $result->getJson();
```
$type에는 SkinTool::MODEL_TYPE_PATH 와 SkinTool::MODEL_TYPE_JSON 중 상황에 맞게 입력하세요. SkinTool::MODEL_TYPE_JSON이 기본값으로 지정되어 있습니다.