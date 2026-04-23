<?php
declare(strict_types=1);

require_once __DIR__ . '/Article.php';

class Search
{
    public static function query(string $q, int $limit = 30): array
    {
        if (trim($q) === '') return [];
        $q = mb_strtolower($q, 'UTF-8');
        $index = Article::loadIndex();
        $results = [];
        foreach ($index as $entry) {
            if (!($entry['published'] ?? true)) continue;
            $haystack = mb_strtolower(
                $entry['title'] . ' ' . $entry['summary'] . ' ' . implode(' ', $entry['tags'] ?? []),
                'UTF-8'
            );
            if (mb_strpos($haystack, $q) !== false) {
                $results[] = $entry;
            }
            if (count($results) >= $limit) break;
        }
        return $results;
    }

    public static function byTag(string $tag): array
    {
        $tag = mb_strtolower(trim($tag), 'UTF-8');
        $index = Article::loadIndex();
        return array_values(array_filter($index, function ($e) use ($tag) {
            if (!($e['published'] ?? true)) return false;
            $tags = array_map(fn($t) => mb_strtolower($t, 'UTF-8'), $e['tags'] ?? []);
            return in_array($tag, $tags);
        }));
    }
}
