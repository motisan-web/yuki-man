# Parsedown スパンバグ修正 2026-04-24

## 問題
自前の Parsedown.php が壊れており、記事本文がすべて `<span>text</span>` で出力されていた。

## 原因
ポートした実装の `plainTextElement()` が `name: 'span'` を返し、`element()` がそれをそのまま `<span>` タグとして出力していた。element の handler 解決も正しく動いていなかった。

## 対処
Parsedown の移植を捨て、シンプルで確実な Markdown パーサーをゼロから書き直し。
対応構文：見出し(ATX/Setext)・段落・太字・斜体・打ち消し・インラインコード・フェンスコード・引用・ul/ol・画像・リンク・水平線・テーブル。

## 変更ファイル
- `includes/Parsedown.php` — 全面書き直し（`Parsedown` クラス名は維持）
