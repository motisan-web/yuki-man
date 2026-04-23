<?php
/**
 * SimpleMarkdown — lightweight Markdown to HTML for yuuki-man
 * Handles: headers, paragraphs, bold, italic, strikethrough, inline code,
 * fenced code, blockquote, ul/ol lists, images, links, hr, tables.
 */
class Parsedown
{
    private bool $safeMode = false;

    public function setSafeMode(bool $safe): static
    {
        $this->safeMode = $safe;
        return $this;
    }

    public function text(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $blocks = $this->splitBlocks(rtrim($text));
        $html = '';
        foreach ($blocks as $block) {
            $html .= $this->renderBlock($block);
        }
        return $html;
    }

    // ── Block-level splitting ──────────────────────────────────────────────

    private function splitBlocks(string $text): array
    {
        // Split on blank lines, but keep fenced code intact
        $lines   = explode("\n", $text);
        $blocks  = [];
        $current = [];
        $inFence = false;
        $fenceChar = '';

        foreach ($lines as $line) {
            // Detect fence open/close
            if (preg_match('/^(`{3,}|~{3,})/', $line, $m)) {
                if (!$inFence) {
                    $inFence   = true;
                    $fenceChar = $m[1][0];
                    $current[] = $line;
                } elseif ($line[0] === $fenceChar) {
                    $inFence   = false;
                    $current[] = $line;
                } else {
                    $current[] = $line;
                }
                continue;
            }

            if ($inFence) {
                $current[] = $line;
                continue;
            }

            if (trim($line) === '') {
                if (!empty($current)) {
                    $blocks[]  = $current;
                    $current   = [];
                }
            } else {
                $current[] = $line;
            }
        }
        if (!empty($current)) $blocks[] = $current;
        return $blocks;
    }

    // ── Block rendering ────────────────────────────────────────────────────

    private function renderBlock(array $lines): string
    {
        $first = $lines[0];
        $raw   = implode("\n", $lines);

        // Fenced code block
        if (preg_match('/^(`{3,}|~{3,})(\S*)/', $first, $m)) {
            return $this->renderFencedCode($lines, $m[2]);
        }

        // ATX Header
        if (preg_match('/^(#{1,6})\s+(.+)$/', $first, $m)) {
            $level = min(6, strlen($m[1]));
            return "<h{$level}>" . $this->inline($m[2]) . "</h{$level}>\n";
        }

        // Setext header (underline === or ---)
        if (count($lines) >= 2) {
            $under = $lines[1];
            if (preg_match('/^=+\s*$/', $under)) {
                return '<h1>' . $this->inline($lines[0]) . "</h1>\n";
            }
            if (preg_match('/^-+\s*$/', $under)) {
                return '<h2>' . $this->inline($lines[0]) . "</h2>\n";
            }
        }

        // Horizontal rule
        if (preg_match('/^(\*{3,}|-{3,}|_{3,})\s*$/', $first)) {
            return "<hr>\n";
        }

        // Blockquote
        if (str_starts_with($first, '>')) {
            return $this->renderBlockquote($lines);
        }

        // Unordered list
        if (preg_match('/^[*+\-]\s/', $first)) {
            return $this->renderList($lines, false);
        }

        // Ordered list
        if (preg_match('/^\d+\.\s/', $first)) {
            return $this->renderList($lines, true);
        }

        // Table (contains | and second line is separator)
        if (count($lines) >= 2 && str_contains($lines[0], '|') && preg_match('/^[\|:\- ]+$/', $lines[1])) {
            return $this->renderTable($lines);
        }

        // Paragraph (or single-item)
        return $this->renderParagraph($lines);
    }

    private function renderFencedCode(array $lines, string $lang): string
    {
        // Remove first and last fence lines
        array_shift($lines);
        if (preg_match('/^(`{3,}|~{3,})\s*$/', end($lines))) array_pop($lines);
        $code = htmlspecialchars(implode("\n", $lines), ENT_QUOTES, 'UTF-8');
        $attr = $lang !== '' ? ' class="language-' . htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') . '"' : '';
        return "<pre><code{$attr}>{$code}</code></pre>\n";
    }

    private function renderBlockquote(array $lines): string
    {
        $inner = array_map(fn($l) => preg_replace('/^>\s?/', '', $l), $lines);
        $inner = implode("\n", $inner);
        // Recurse for nested markdown
        $sub = new self();
        $sub->safeMode = $this->safeMode;
        return "<blockquote>\n" . $sub->text($inner) . "</blockquote>\n";
    }

    private function renderList(array $lines, bool $ordered): string
    {
        $tag      = $ordered ? 'ol' : 'ul';
        $pattern  = $ordered ? '/^\d+\.\s+/' : '/^[*+\-]\s+/';
        $items    = [];
        $current  = null;

        foreach ($lines as $line) {
            if (preg_match($pattern, $line)) {
                if ($current !== null) $items[] = $current;
                $current = [preg_replace($pattern, '', $line)];
            } elseif ($current !== null) {
                // continuation / sub-content
                $current[] = ltrim($line);
            }
        }
        if ($current !== null) $items[] = $current;

        $html = "<{$tag}>\n";
        foreach ($items as $item) {
            $content = implode("\n", $item);
            $html .= '<li>' . $this->inline($content) . "</li>\n";
        }
        $html .= "</{$tag}>\n";
        return $html;
    }

    private function renderTable(array $lines): string
    {
        $headers  = $this->splitTableRow($lines[0]);
        // Parse alignment from separator row
        $sepCells = $this->splitTableRow($lines[1]);
        $aligns   = array_map(function ($c) {
            $c = trim($c, ' ');
            if (str_starts_with($c, ':') && str_ends_with($c, ':')) return ' style="text-align:center"';
            if (str_ends_with($c, ':')) return ' style="text-align:right"';
            if (str_starts_with($c, ':')) return ' style="text-align:left"';
            return '';
        }, $sepCells);

        $html = "<table>\n<thead>\n<tr>\n";
        foreach ($headers as $i => $h) {
            $a = $aligns[$i] ?? '';
            $html .= "<th{$a}>" . $this->inline(trim($h)) . "</th>\n";
        }
        $html .= "</tr>\n</thead>\n<tbody>\n";

        for ($r = 2; $r < count($lines); $r++) {
            $cells = $this->splitTableRow($lines[$r]);
            $html .= "<tr>\n";
            foreach ($cells as $i => $c) {
                $a = $aligns[$i] ?? '';
                $html .= "<td{$a}>" . $this->inline(trim($c)) . "</td>\n";
            }
            $html .= "</tr>\n";
        }
        return $html . "</tbody>\n</table>\n";
    }

    private function splitTableRow(string $line): array
    {
        $line = preg_replace('/^\||\|$/', '', trim($line));
        return explode('|', $line);
    }

    private function renderParagraph(array $lines): string
    {
        $text = implode("\n", $lines);
        // Hard line breaks: two trailing spaces → <br>
        $text = preg_replace('/  \n/', "<br>\n", $text);
        return '<p>' . $this->inline($text) . "</p>\n";
    }

    // ── Inline rendering ───────────────────────────────────────────────────

    private function inline(string $text): string
    {
        // Escape HTML (safe mode or always for security)
        if ($this->safeMode) {
            $text = htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');
            // Re-allow safe constructs by working on escaped text
            $text = $this->applyInline($text, escaped: true);
        } else {
            $text = $this->applyInline($text, escaped: false);
        }
        return $text;
    }

    private function applyInline(string $text, bool $escaped): string
    {
        if (!$escaped) {
            // Temporarily protect HTML entities
            $text = htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');
        }

        // Inline code (must go first to protect contents)
        $codeMap = [];
        $text = preg_replace_callback('/`([^`]+)`/', function ($m) use (&$codeMap) {
            $key = "\x00CODE" . count($codeMap) . "\x00";
            $codeMap[$key] = '<code>' . htmlspecialchars($m[1], ENT_QUOTES, 'UTF-8') . '</code>';
            return $key;
        }, $text);

        // Images (before links)
        $text = preg_replace_callback(
            '/!\[([^\]]*)\]\(([^)]+)\)/',
            function ($m) {
                $alt = htmlspecialchars($m[1], ENT_QUOTES, 'UTF-8');
                $src = $this->safeUrl($m[2]);
                return "<img src=\"{$src}\" alt=\"{$alt}\">";
            },
            $text
        );

        // Links
        $text = preg_replace_callback(
            '/\[([^\]]+)\]\(([^)]+)\)/',
            function ($m) {
                $href  = $this->safeUrl($m[2]);
                $label = $m[1]; // already escaped
                return "<a href=\"{$href}\">{$label}</a>";
            },
            $text
        );

        // Bold+Italic ***
        $text = preg_replace('/\*\*\*(.+?)\*\*\*/s', '<strong><em>$1</em></strong>', $text);
        $text = preg_replace('/___(.+?)___/s',        '<strong><em>$1</em></strong>', $text);

        // Bold
        $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
        $text = preg_replace('/__(.+?)__/s',      '<strong>$1</strong>', $text);

        // Italic
        $text = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $text);
        $text = preg_replace('/_(.+?)_/s',   '<em>$1</em>', $text);

        // Strikethrough
        $text = preg_replace('/~~(.+?)~~/s', '<s>$1</s>', $text);

        // Restore inline code
        foreach ($codeMap as $key => $val) {
            $text = str_replace($key, $val, $text);
        }

        return $text;
    }

    private function safeUrl(string $url): string
    {
        $url = trim($url);
        // Strip title from link syntax: (url "title")
        if (preg_match('/^(\S+)\s+"[^"]*"$/', $url, $m)) $url = $m[1];
        if ($this->safeMode) {
            $lower = strtolower($url);
            foreach (['javascript:', 'vbscript:', 'data:'] as $bad) {
                if (str_starts_with($lower, $bad)) return '#';
            }
        }
        return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    }
}
