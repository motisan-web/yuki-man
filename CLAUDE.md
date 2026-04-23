# yuuki-man

## 目的
「ゆうきまん」──日本版フロリダマン的概念のニュースアーカイブサイト。
毎日1〜4件のランダムなMDニュース記事を掲載し、全文検索・タグ検索・日付ブラウズができる。
Zenn風のシンプルさと日本のニュースサイトの情報密度を組み合わせたデザイン。

## 設計・環境

### 動作環境
| 環境 | ホスト | PHP |
|---|---|---|
| ローカル | git11.local (XAMPP) | 8.2+ |
| 本番 | Xserver | 8.2+ |

### 技術スタック
- **バックエンド**: PHP 8.2+（フレームワークなし、素のPHP）
- **フロントエンド**: HTML + CSS（Tailwind CDN）+ vanilla JS
- **記事データ**: Markdown ファイル（`articles/YYYY/MM/DD/slug.md`）+ JSONインデックス（`data/index.json`）
- **検索**: JSONインデックスを使ったサーバーサイド全文検索（PHP）
- **管理**: Web管理画面（`/admin/`）+ Claude直接記事追加（MDファイル + JSON更新）

### ディレクトリ構成（予定）
```
/
├── index.php              # トップページ（最新記事一覧）
├── article.php            # 記事詳細
├── search.php             # 検索結果
├── admin/
│   ├── index.php          # 管理トップ
│   ├── edit.php           # 記事作成・編集
│   └── delete.php         # 記事削除
├── api/
│   └── search.php         # 検索API（JSON返却）
├── articles/              # MDファイル格納
│   └── YYYY/MM/DD/slug.md
├── data/
│   └── index.json         # 検索・一覧用インデックス
├── thumbnails/            # サムネイル画像
├── assets/
│   ├── css/style.css
│   └── js/main.js
└── includes/
    ├── config.php
    ├── article.php        # 記事読み書きクラス
    └── search.php         # 検索クラス
```

### 記事MDフロントマター仕様
```yaml
---
title: タイトル
date: YYYY-MM-DD
slug: unique-slug
tags: [タグ1, タグ2]
thumbnail: thumbnails/xxx.jpg  # 省略可
summary: 要約文（検索・OGP用）
published: true
---
本文（Markdown）
```

### JSONインデックス仕様（data/index.json）
```json
[
  {
    "slug": "unique-slug",
    "title": "タイトル",
    "date": "YYYY-MM-DD",
    "tags": ["タグ1"],
    "thumbnail": "thumbnails/xxx.jpg",
    "summary": "要約",
    "path": "articles/2024/01/15/unique-slug.md",
    "published": true
  }
]
```

---

## ドキュメント管理ルール

### ファイルの役割分担

| ファイル | 役割 | 読むタイミング |
|---|---|---|
| `CLAUDE.md` | プロジェクトルール＋アクティブな todo/issue のみ | 毎チャット必読 |
| `.claude-codex/CURRENT.md` | 直近の完了内容・次にやること・注意事項 | 毎チャット必読 |
| `.claude-codex/change/*.md` | 完了した作業の詳細ログ | 必要なときだけ |

### todo のルール
- `[ ]` のみここに記載する
- `[x]` にしたら**その行を削除**する（記録は change/ の変更ログが担う）
- 変更ログファイル名を `（ファイル名.md）` の形で末尾に添えてチェックする

### issue のルール
- バグ・不具合・改善点が見つかったら、**まずここに `[ ]` で登録してから対処**する
- 対処完了後は `[x]` にしてから**その行を削除**する

### 変更ログのルール
- 作業の変更内容は `.claude-codex/change/作業内容.md` に記録する
- 記録フォーマット：タイムスタンプ・変更内容・意図を羅列する
- 記録タイミング：todo/issue を消化したとき・変更に一区切りがついたとき

### セッション引き継ぎのルール
- チャット終了時に `.claude-codex/CURRENT.md` を最新状態に更新する
- 新チャット開始時は **CLAUDE.md → CURRENT.md** の順に読めば文脈が揃う状態を保つ

### Claude直接記事追加のルール
1. `articles/YYYY/MM/DD/slug.md` にMDファイルを作成
2. `data/index.json` にエントリを追加（先頭に追加、date降順を維持）
3. slugはケバブケース英数字（例: `woman-burns-gyoza-after-groping-incident`）

---

## issue
（現在アクティブな issue なし）

## todo
（現在アクティブな todo なし）
