bitbank.cc 仮想通貨注文プログラム
===

bitbank.cc の API を使用して仮想通貨の注文を出すプログラムです。

PHPが動くLinuxサーバであればある程度動くと思います。composerも必須です。

## 設置方法
### 1. php-bitbankcc を適当なディレクトリに設置する
環境変数の設定ファイル(.env)が外部に暴露される危険性があるので、レンタルサーバの非公開領域に設置が望ましいです。

### 2. .env を編集する
bitbank.cc のAPIキーを取得して .env ファイルを編集します。

```
BITBANK_API_KEY="APIキーを記入"
BITBANK_API_SECRET="APIシークレットを記入"
```

### 3. composer で必要なライブラリを取得する
php-bitbankcc ディレクトリ内で composer install を実行。

```
cd php-bitbankcc
composer install
```

これにてかんりょうです。

## 使用方法
コマンドから実行できます。

1万円分のビットコインを買板の5番目に注文を入れたい場合は以下のコマンドで注文ができます。

```
php bbtrade.php --pair="btc_jpy" --amount_price="10000" --side="buy" --n="5"
```

3000円分のリップルを売板の20番目に注文を入れたい場合は以下のコマンドで注文ができます。

```
php bbtrade.php --pair="xrp_jpy" --amount_price="3000" --side="sell" --n="20"
```

注文する際に既に注文している売注文を全部キャンセルした上で新しく3000円分のリップルを売板の20番目に注文を入れたい場合は以下のコマンドで注文ができます。

```
php bbtrade.php --cancel="1" --pair="xrp_jpy" --amount_price="3000" --side="sell" --n="20"
```

## 応用: 自動買付
cron を使用すればドルコスト平均法的な買付を行うことができます。
cron の使用方法については割愛しますが、サンプルとしては以下のとおりです。

1万円分のビットコインを毎日0時5分に買板の20番目に指値する

```
5 0 * * * cd 【設置場所】/php-bitbankcc; php bbtrade.php --pair="btc_jpy" --amount_price="10000" --side="buy" --n="20"
```

さくらの場合

```
5 0 * * * cd 【設置場所】/php-bitbankcc; /usr/local/bin/php bbtrade.php --pair="btc_jpy" --amount_price="10000" --side="buy" --n="20"
```
