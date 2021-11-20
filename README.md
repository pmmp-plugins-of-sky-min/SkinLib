# SkinTool
pmmp virion

# API
## 이미지 불러오기
```php
SkinTool::getInstance()->getImg($path);
```
## 이미지 저장하기
```php
SkinTool::getInstance()->saveImg($image, $path);
```
## 이미지 합치기
```php
SkinTool::getInstance()->mergeImage($image1, $image2);
```
## 이미지 스킨데이터로 변환
```php
SkinTool::getInstance()->getSkinData($image);
```
## 스킨데이터로 이미지 불러오기
```php
SkinTool::getInstance()->dataToImage($skinData);
```
## 모댈링 합성
```php
SkinTool::getInstance()->mergeModel($model1, $model2, $type);
```
$type에는 SkinTool::PATH와 SkinTool::JSON 중 상황에 맞게 입력하세요. SkinTool::JSON은 기본값으로 지정되어 있습니다.

$type에 따라 $model1과 $model2에 넣어야 할 값이 다름니다.

SkinTool::JSON 일 경우 $model1과 $model2에 모델링 json 내용을 넣어줘야합니다

SkinTool::PATH일 경우 $model1과 $model2에 파일경로를 넣어주면 됩니다.