<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

class Article
{
    private static function indexPath(): string
    {
        return INDEX_FILE;
    }

    public static function loadIndex(): array
    {
        $path = self::indexPath();
        if (!file_exists($path)) return [];
        $json = file_get_contents($path);
        return json_decode($json, true) ?? [];
    }

    public static function saveIndex(array $index): void
    {
        $path = self::indexPath();
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        file_put_contents($path, json_encode($index, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public static function parseFrontmatter(string $raw): array
    {
        $frontmatter = [];
        $body = $raw;
        if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)/s', $raw, $m)) {
            $body = $m[2];
            foreach (explode("\n", $m[1]) as $line) {
                if (preg_match('/^(\w+):\s*(.*)$/', trim($line), $lm)) {
                    $key = $lm[1];
                    $val = trim($lm[2]);
                    if ($key === 'tags') {
                        $val = preg_replace('/^\[|\]$/', '', $val);
                        $tags = array_map('trim', explode(',', $val));
                        $frontmatter[$key] = array_filter($tags, fn($t) => $t !== '');
                    } elseif ($key === 'published') {
                        $frontmatter[$key] = $val === 'true';
                    } else {
                        $frontmatter[$key] = $val;
                    }
                }
            }
        }
        return ['meta' => $frontmatter, 'body' => $body];
    }

    public static function buildFrontmatter(array $meta, string $body): string
    {
        $tags = implode(', ', $meta['tags'] ?? []);
        $published = ($meta['published'] ?? true) ? 'true' : 'false';
        $thumbnail = $meta['thumbnail'] ?? '';
        $summary = $meta['summary'] ?? '';
        return "---\ntitle: {$meta['title']}\ndate: {$meta['date']}\nslug: {$meta['slug']}\ntags: [{$tags}]\nthumbnail: {$thumbnail}\nsummary: {$summary}\npublished: {$published}\n---\n{$body}";
    }

    public static function slugToPath(string $slug, string $date): string
    {
        [$y, $m, $d] = explode('-', $date);
        return ARTICLES_DIR . "/{$y}/{$m}/{$d}/{$slug}.md";
    }

    public static function readBySlug(string $slug): ?array
    {
        $index = self::loadIndex();
        foreach ($index as $entry) {
            if ($entry['slug'] === $slug) {
                $path = ROOT_DIR . '/' . $entry['path'];
                if (!file_exists($path)) return null;
                $raw = file_get_contents($path);
                $parsed = self::parseFrontmatter($raw);
                return array_merge($entry, ['body' => $parsed['body'], 'raw' => $raw]);
            }
        }
        return null;
    }

    public static function save(array $meta, string $body): bool
    {
        $slug = $meta['slug'];
        $date = $meta['date'];
        $path = self::slugToPath($slug, $date);
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $raw = self::buildFrontmatter($meta, $body);
        file_put_contents($path, $raw);

        $index = self::loadIndex();
        $relativePath = 'articles/' . implode('/', explode('-', $date, 3)) . "/{$slug}.md";

        $entry = [
            'slug'      => $slug,
            'title'     => $meta['title'],
            'date'      => $date,
            'tags'      => $meta['tags'] ?? [],
            'thumbnail' => $meta['thumbnail'] ?? '',
            'summary'   => $meta['summary'] ?? '',
            'path'      => $relativePath,
            'published' => $meta['published'] ?? true,
        ];

        // update or prepend
        $found = false;
        foreach ($index as &$e) {
            if ($e['slug'] === $slug) { $e = $entry; $found = true; break; }
        }
        unset($e);
        if (!$found) array_unshift($index, $entry);

        // sort by date desc
        usort($index, fn($a, $b) => strcmp($b['date'], $a['date']));
        self::saveIndex($index);
        return true;
    }

    public static function delete(string $slug): bool
    {
        $index = self::loadIndex();
        foreach ($index as $entry) {
            if ($entry['slug'] === $slug) {
                $path = ROOT_DIR . '/' . $entry['path'];
                if (file_exists($path)) unlink($path);
                break;
            }
        }
        $index = array_values(array_filter($index, fn($e) => $e['slug'] !== $slug));
        self::saveIndex($index);
        return true;
    }

    public static function listPublished(int $page = 1, int $perPage = ARTICLES_PER_PAGE): array
    {
        $index = array_values(array_filter(self::loadIndex(), fn($e) => $e['published'] ?? true));
        $total = count($index);
        $offset = ($page - 1) * $perPage;
        return ['items' => array_slice($index, $offset, $perPage), 'total' => $total, 'pages' => (int) ceil($total / $perPage), 'page' => $page];
    }

    public static function listAll(): array
    {
        return self::loadIndex();
    }

    public static function getByDate(string $date): array
    {
        return array_values(array_filter(self::loadIndex(), fn($e) => $e['date'] === $date && ($e['published'] ?? true)));
    }

    public static function sanitizeSlug(string $slug): string
    {
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
}
