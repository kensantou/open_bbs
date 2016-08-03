BBSシステム
==========

## 概要
1.システム構成  
2.ソースファイル構成  
3.機能概要  
5.テーブル定義
6.利用リンク集

## 1.システム構成
- PHP version : 5.6
- フレームワーク : Slim3
- レスポンシブ : bootstrap3
- DB : MySQL
- DB利用 : PDO

## 2.ソースファイル構成
public/  : 直下がドキュメントルートとなっている。  
├ /css/origin.css :カスタム用CSS  
├ /img/ : 画像用ディレクトリ   
├ /uploads/tmp/ : 確認画面での画像表示用  
├ /uploads/ : ユーザーが投稿した画像  
├ index.php : 設定ファイルの読み込み、アプリ開始  

src/  
├ routes.php : HTTPリクエストのハンドリング  
├ settings.php : 設定ファイル   
├ dependencies.php : DIコンテナへの登録  

src/app/usecase  
それぞれの機能がまとめてある。   
ロジックはroutes.phpから切り離され、  
ここにまとめてある。


## 3.機能概要
- トピック作成
 - 確認画面  
 ImageUsecaseを使って画像を/uploads/tmp/に移動  
 hiddenを使ってユーザーの入力値を完了に送信
 - 完了画面  
 ImageUsecaseを使って画像を/uploads/に移動  
 TopicUsecaseを使ってトピック作成  
 同時にCommentUsecaseを使ってコメント作成  
 ※　画像名はtopicsとcommentsテーブル両方に登録  

- コメント作成
 - 確認画面  
ImageUsecaseを使って画像を/uploads/tmp/に移動  
hiddenを使ってユーザーの入力値を完了に送信
 - 完了画面  
ImageUsecaseを使って画像を/uploads/に移動  
TopicUsecaseを使ってトピック作成  
同時にCommentUsecaseを使ってコメント作成  
※　画像名はtopicsとcommentsテーブル両方に登録  
※　comment_cntをインクリメントしている
- トピック新着一覧  
topicsのcreatedでソートして表示  
表示画像はtopicsテーブルのもの  
- トピックコメント数ランキング  
topicsのcomment_cntでソートして表示  
表示画像はtopicsテーブルのもの  

## 4.テーブル定義
#### topicsテーブル
| カラム | 定義 | 概要 |
|:-----------|------------:|:------------:|
| topic_id | int NOT NULL AUTO_INCREMENT | トピックのID |
| title     | varchar(100) |    タイトル    |
| comment_cnt |  int  |     コメント数ソート用     |
| image_name  | varchar(256) | 画像ファイル名。トピック一覧用で、コメントと同じ名前 |
| created  | timestamp DEFAULT CURRENT_TIMESTAMP | 新着ソート用    |

#### commentsテーブル
| カラム | 定義 | 概要 |
|:-----------|------------:|:------------:|
| id | int NOT NULL AUTO_INCREMENT | 一意ID |
| topic_id     | int | トピックID    |
| comment_id |  int  | コメント番号     |
| message  | text NOT NULL | コメント |
| image_name  | varchar(30) | 画像ファイル名    |
| created  | timestamp DEFAULT CURRENT_TIMESTAMP | 作成時刻    |

## 利用リンク集
- FrameWork  
<http://www.slimframework.com/>  
- bootstrap3  
<http://getbootstrap.com/components/>  
- DB  
<http://phpocean.com/tutorials/back-end/workouts-with-slim-3-database-with-pdo-and-eloquent/51>  
- VALIDATION  
<http://respect.github.io/Validation/docs/install.html>  
- CSRF  
<http://log.deprode.net/logs/2015-10-01/>
