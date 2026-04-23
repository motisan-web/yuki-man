# Claude Design System 適用 2026-04-24

## 概要
デザインを DESIGN-claude.md（Anthropic Claude のデザインシステム）ベースに全面刷新。

## 変更内容

### カラー変更（赤系 → テラコッタ・パーチメント系）
| 旧 | 新 | 用途 |
|---|---|---|
| bg-gray-50 | bg-parchment (#f5f4ed) | ページ背景 |
| bg-white | bg-ivory (#faf9f5) | カード背景 |
| red-600 | terra (#c96442) | CTA・アクセント |
| gray-400/500 | olive/stone (#5e5d59 / #87867f) | テキスト |
| border-gray-200 | border-cream (#f0eee6) | ボーダー |

### タイポグラフィ
- 見出し：Noto Serif JP weight 500（Georgia フォールバック）
- 本文：Noto Sans JP

### シャドウ
- drop-shadow 廃止 → ring shadow `0 0 0 1px #d1cfc5`
- カードホバー：whisper shadow `rgba(0,0,0,0.05) 0 4px 24px` + ring

### コンポーネント
- .btn-terra / .btn-sand / .btn-dark — CSS クラス化
- .card — ivory 背景 + cream ボーダー + ring hover
- .tag — warm sand タグ pill
- .input-warm — ivory 背景、focus は focus-blue (#3898ec) のみ
- .label-warm — olive テキストラベル

### フッター
- 旧：白背景 → 新：nearblack (#141413) ダーク背景でセクション変化演出

## 変更ファイル
- `includes/header.php` — Tailwind config に Claude カラー追加、Noto フォント読み込み
- `includes/footer.php` — ダーク背景に変更
- `assets/css/style.css` — 全面書き直し（設計トークン + prose）
- `index.php` — hero をダーク、カードを ivory
- `article.php` — serif 見出し、editorial レイアウト
- `search.php` — warm palette 適用
- `admin/login.php` — nearblack 背景 + parchment カード
- `admin/index.php` — warm table スタイル
- `admin/edit.php` — input-warm / btn-terra 適用
