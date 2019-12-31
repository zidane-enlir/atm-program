# ATM Program

#### オリジナルの作成者
<a href="https://github.com/IVER9">IVER9</a>

<a href="https://github.com/IVER9/ATMprogram-update">クローン元のリポジトリ</a>

## 動作イメージ画像

<img src="https://github.com/zidane-enlir/atm-program/raw/master/img/readme1.png">

## PSR-4規約に沿ったオートローディングの導入方法
  
1. Composerをプロジェクトルートにcurl。
```
curl -s https://getcomposer.org/installer | php
```
  
2. 名前空間&クラス名とディレクトリ構造を一致させた形でcomposer.jsonを記入。
```
touch composer.json
```
  
3. オートローディングの構築
```
php composer.phar dump-autoload
```
