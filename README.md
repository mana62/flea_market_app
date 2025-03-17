# flea_market_app

- coachtech フリマ

# 作成した目的

- アイテムの出品と購入を行うためのフリマアプリを開発するため

# アプリケーション URL

<開発環境><br>

- phpMyAdmin: <br>
  http://localhost:8080
- アプリ URL:<br>
  http://localhost/register<br>
  <br>

<テスト環境><br>

- phpMyAdmin: <br>
  http://localhost:8081
- アプリ URL:<br>
  http://localhost:8082<br>

# 他のリポジトリ

<開発環境><br>

- https://github.com/mana62flea_market_app<br>

# 機能一覧

- 登録認証機能
- 入力フォーム
- バリデーション
- エラーメッセージ表示
- 初回ログイン時ユーザー設定
- ログイン認証機能
- ユーザー認証動線
- メールを用いた認証機能
- ログアウト機能
- 商品一覧取得
- マイリスト一覧取得
- 商品検索機能
- 商品詳細情報取得
- いいね機能
- 購入手続き動線
- コメント送信機能
- 購入前商品情報取得機能
- 商品購入機能
- 支払い方法選択機能
- 配送先変更機能
- ユーザー情報取得
- プロフィール編集動線
- ユーザー情報変更機能
- 出品商品情報登録機能
- 出品商品画像アップロード機能
- 取引中商品確認機能
- 取引チャット遷移機能
- 別取引遷移機能
- 取引自動ソート機能
- 取引商品新規通知確認機能
- 評価平均確認機能
- 取引チャット機能
- 入力情報保持機能
- メッセージ編集機能
- メッセージ削除機能
- 取引後評価機能（購入者）
- 取引後評価機能（出品者）
- 取引後画面遷移
- メール送信
- メール送信機能

# 使用技術

- nginx: latest
- php: 8.1-fpm
- mysql: 8.0.26
- Laravel: 8

# テーブル設計

<img width="220" alt="Image" src="https://github.com/user-attachments/assets/90ab5c60-5bfb-47f0-a1df-0e3b6d12efff" />

# ER 図

<img width="857" alt="Image" src="https://github.com/user-attachments/assets/acc38789-5218-4d80-b075-84465cedb846" />

# 環境構築

1. リモートリポジトリを作成
2. ローカルリポジトリの作成
3. リモートリポジトリをローカルリポジトリに追加
4. docker-compose.yml の作成
5. Nginx / PHP / MySQL / phpMyAdmin の設定
6. Docker コンテナを起動:<br>
   docker compose up -d --build
7. PHP コンテナに入る:<br>
   docker exec -it flea_market_php bash
8. Laravel のインストール:<br>
   composer create-project "laravel/laravel=8.\*" . --prefer-dist
9. タイムゾーン設定:<br>
   'timezone' => 'Asia/Tokyo'
10. .env の作成 & 設定:<br>
    cp .env.example .env
11. アプリケーションキーの生成:<br>
    php artisan key:generate
12. マイグレーション:<br>
    php artisan migrate
13. シーディング:<br>
    php artisan db:seed

# test 環境構築

1. テスト用のコンテナを起動:<br>
   docker compose -f docker-compose.testing.yml up -d
2. .env.testing の作成 & 設定:<br>
   cp src/.env.example src/.env.testing
3. PHP コンテナに入る:<br>
   docker exec -it flea_market_php_test bash
4. テスト実行:<br>
   php artisan test

# クローンして環境構築する手順

1. Git リポジトリのクローン:<br>
   git clone git@github.com:mana62/flea_market_app.git<br>
   cd flea_market_app
2. .env ファイルの作成 & 設定:<br>
   cp src/.env.example src/.env<br>

.env の設定例:<br>
DB_CONNECTION=mysql<br>
DB_HOST=mysql<br>
DB_PORT=3306<br>
DB_DATABASE=flea_market_db<br>
DB_USERNAME=user<br>
DB_PASSWORD=pass<br>
<br>
MAIL_MAILER=smtp<br>
MAIL_HOST=mailhog<br>
MAIL_PORT=1025<br>
MAIL_USERNAME=null<br>
MAIL_PASSWORD=null<br>
MAIL_ENCRYPTION=null<br>
MAIL_FROM_ADDRESS=test@example.com<br>
MAIL_FROM_NAME="flea_market_app"<br>
<br>

※ https://stripe.com/jp から下記2つのキーを取得して記載する ※<br>
STRIPE_KEY=<br>
STRIPE_SECRET=<br>

3. .env.testing ファイルの作成 & 設定:<br>
   cp src/.env.example src/.env.testing<br>

.env.testing の設定例:<br>
APP_ENV=testing<br>
<br>
DB_CONNECTION=mysql<br>
DB_HOST=mysql<br>
DB_PORT=3306<br>
DB_DATABASE=test_db<br>
DB_USERNAME=test_user<br>
DB_PASSWORD=test<br>
<br>

※ https://stripe.com/jpから 下記2つのキーを取得して記載する ※<br>
STRIPE_KEY=<br>
STRIPE_SECRET=<br>
<br>

4. Docker コンテナの起動:<br>
   docker compose up -d --build
5. PHP コンテナに入る:<br>
   docker exec -it flea_market_php bash
6. Laravel パッケージのインストール:<br>
   composer install
7. .env アプリケーションキーの生成:<br>
   php artisan key:generate
8. .env.testing アプリケーションキーの生成:<br>
   php artisan key:generate --env=testing
9. マイグレーションとシーディング:<br>
   php artisan migrate --seed
10. シンボリックリンクを設定:<br>
    php artisan storage:link
11. exit
12. 環境変数を反映するために再起動:<br>
    docker compose down<br>
    docker compose up -d --build
    <br>
    ＜テスト環境＞

13. テスト環境の起動:<br>
    docker compose down<br>
    docker compose -f docker-compose.testing.yml up -d
14. PHP コンテナに入る:<br>
    docker exec -it flea_market_php_test bash
15. マイグレーション:<br>
    php artisan migrate --env=testing
16. テスト実行:<br>
    php artisan test

# 補足

- メール認証が完了していないとログインできない
- 未承認の場合はコメント入力欄が非表示
- 未承認の場合はいいねができず、マイリストには「いいねした商品はありません」と表示される
- メール認証は MailHog を使用
- タグ部分の通知 -> 取引されているアイテムの数を表示
- 画像部分の通知 -> メッセージの未読件数を表示
  <br>

# ユーザーのダミーデータ<br>

- $user1 [item1~5を出品]<br>
  名前 : 山田太郎<br>
  メールアドレス : taro@example.com<br>
  パスワード : password123<br>

- user2 [item6~10を出品]<br>
  名前 : 山田二郎<br>
  メールアドレス : jiro@example.com<br>
  パスワード : password456<br>

- user3 [何も出品しない]<br>
  名前 : 山田三郎<br>
  メールアドレス : saburo@example.com<br>
  パスワード : password789<br>
