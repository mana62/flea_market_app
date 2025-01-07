# flea_market_app
- coachtechフリマ<br>

# 作成した目的
- アイテムの出品と購入を行うためのフリマアプリを開発するため<br>
<br>

# アプリケーションURL
<開発環境><br>
- phpmyadmin: [http://localhost:8080](http://localhost:8080)<br>
- アプリURL: [http://localhost/register](http://localhost/)
<br>

# 他のリポジトリ
<開発環境><br>
- https://github.com/mana62/flea_market_app<br>
<br>

# 機能一覧
- 登録認証機能
- 入力フォーム
- バリデーション
- エラーメッセージ表示
- ユーザー認証動線
- 初回ログイン時ユーザー設定
- ログイン認証機能
- 入力フォーム
- バリデーション
- エラーメッセージ表示
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
<br>

# 使用技術
- nginx: latest<br>
- php: 8.1-fpm<br>
- mysql: 8.0.26<br>
- Laravel: 8<br>
<br>

# テーブル設計

<br>

# ER図
<img width="1030" alt="coachtechフリマ" src="https://github.com/user-attachments/assetsaeee877a-52dc-4c90-86c9-e1b34d7daf73" />
<br>

# 環境構築
1. リモートリポジトリを作成<br>
2. ローカルリポジトリの作成<br>
3. リモートリポジトリをローカルリポジトリに追加<br>
4. docker-compose.yml の作成<br>
5. Nginx の設定<br>
6. PHP の設定<br>
7. MySQL の設定<br>
8. phpMyAdmin の設定<br>
9. docker-compose up -d --build<br>
10. docker-compose exec php bash<br>
11. composer create-project "laravel/laravel=8.*" . --prefer-dist<br>
12. app.php の timezone を修正<br>
13. .env ファイルの環境変数を変更<br>
14. php artisan key:generate
15. php artisan migrate<br>
16. php artisan db:seed<br>
<br>

# クローンの流れ
1. Git リポジトリのクローン<br>
(git clone git@github.com:mana62/flea_market_app.git)<br>

2. .env ファイルの作成<br>
(cp .env.example .env)<br>
3. .env ファイルの編集<br>
<br>
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

4. Dockerの設定<br>
(docker compose up -d --build)<br>
5. PHPコンテナにアクセス<br>
(docker exec -it flea_market_php bash)<br>
6. Laravelパッケージのインストール<br>
(composer install)<br>
7. アプリケーションキーの生成<br>
(php artisan key:generate)<br>
8. マイグレーション<br>
(php artisan migrate)<br>
9. シーディング<br>
(php artisan db:seed)<br>

# 補足