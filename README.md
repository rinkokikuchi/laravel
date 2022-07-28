## udemy Laravel口座



##ダウンロード方法
git clone
git clone https//github.com/aokitashipro/laravel_umarche.git

git crone branchを指定してダウンロードする場合
git clone -b ブランチ名 https://github.com/aokitashipro/laravel_umarche.git

もしくはzipファイルでダウンロードしてください。

## インストール方法
cd laravel_umarche
composer install
npm install
npm run dev

.env.exampleをコピーして.envファイルを作成

.envファイルの中の下記をご利用の環境に合わせて変更してください。

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_umarche
DB_USERNAME=umarche
DB_PASSWPRD=password123

XAMPP /MAMP またはほかの開発環境でDBを起動した後に

php artisan migrate:fresh --seed

と実行してください。(データベーステーブルとダミーデータがつかされればok)

最後に
php artisan key:generate
と入力してキーを生成後、

php artisan serve
で簡易サーバーを立ち上げ、表示確認してください。





## インストール後の実施事項

画像のダミーデータは
public/image フォルダ内に
sample1.jpg ~ sample6.jpg として
保存しています。

php artisan storage:link で
storageフォルダにリンク後、

storage/app/public/productsフォルダ内に
保存すると表示されます。
(products フォルダがない場合は作成してください。)

ショップの画像も表示する場合は、
storage/app/public/shopsフォルダを作成し
画像を保存してください。
