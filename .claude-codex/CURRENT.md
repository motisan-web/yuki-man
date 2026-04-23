# 現在の作業状態

> 最終更新: 2026-04-24

---

## 直近で完了したこと
- システム本体の実装（全ページ・管理画面・APIを含む）
- サンプル記事6件作成（2026-04-20〜24）
- `data/index.json` 初期データ投入済み
- Parsedown の `<span>` バグ修正（自前パーサー書き直し）
- Claude Design System 全面適用（パーチメント背景・テラコッタ・セリフ見出し・ring shadow）

## 完成したファイル一覧
```
index.php               # トップページ（記事一覧・ページネーション）
article.php             # 記事詳細（Markdown→HTML, タグ, サムネイル）
search.php              # 全文検索・タグ検索結果
includes/
  config.php            # 定数・設定（管理パスワードもここ）
  Parsedown.php         # Markdownパーサー（single-file）
  Article.php           # 記事CRUD + JSONインデックス管理
  Search.php            # 検索クラス
  header.php / footer.php
admin/
  login.php / logout.php / index.php / edit.php / delete.php
api/search.php          # JSON検索API
assets/css/style.css    # カスタムCSS（prose, article-body等）
data/index.json         # 検索インデックス（記事追加時に自動更新）
articles/               # MDファイル格納（YYYY/MM/DD/slug.md）
.htaccess               # .json/.md への直アクセスを禁止
```

## 管理画面アクセス
- URL: `http://git11.local/admin/`
- ユーザー名: `admin`
- パスワード: `yuukiman2026`
- **本番前に config.php の ADMIN_PASS_HASH を変更すること**

## Claudeが直接記事を追加する手順
1. `articles/YYYY/MM/DD/slug.md` を作成（フロントマター付き）
2. `data/index.json` の先頭にエントリを追加（date降順を維持）

## 次にやること
- サムネイル画像の作成・設定（任意）
- 本番Xserverへのデプロイ
- config.php の管理パスワード変更

## 注意事項・既知の仕様
- Parsedown は自前実装（simplified）。複雑なネスト等は非対応
- 検索はJSONの title + summary + tags のみ（本文全文検索はなし）
- adminパスワードは config.php の ADMIN_PASS_HASH をpassword_hash()で再生成して変更
- `.htaccess` で .md/.json への直アクセスは禁止済み
