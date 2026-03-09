<?php

namespace App\Support;

/**
 * Writes saved classes-type content overrides back into blade file default attributes
 * so Tailwind's CSS scanner can detect the current class values at build time.
 */
class BladeClassSyncer
{
    /**
     * Known secondary suffixes in a key like {prefix}_{suffix}_classes
     * that map to default-{suffix}-classes on the component with prefix={prefix}.
     *
     * @var array<string, string>
     */
    private const SECONDARY_SUFFIXES = [
        '_featured' => 'featured',
        '_wrapper' => 'wrapper',
        '_image' => 'image',
        '_grid' => 'grid',
        '_video' => 'video',
    ];

    public function sync(string $bladeFilePath, string $rowSlug, string $key, string $value): void
    {
        if (! file_exists($bladeFilePath)) {
            return;
        }

        $content = file_get_contents($bladeFilePath);
        $updated = $this->updateAttr($content, $rowSlug, $key, $value);

        if ($updated !== $content) {
            file_put_contents($bladeFilePath, $updated);
        }
    }

    private function updateAttr(string $content, string $rowSlug, string $key, string $value): string
    {
        [$attrName, $searchBy, $searchValue] = $this->resolveAttr($rowSlug, $key);

        $searchStr = $searchBy.'="'.$searchValue.'"';
        $pos = 0;

        while (($pos = strpos($content, $searchStr, $pos)) !== false) {
            $beforePos = substr($content, 0, $pos);
            $tagStart = strrpos($beforePos, '<x-dl.');

            if ($tagStart === false) {
                $pos++;

                continue;
            }

            // Find the closing > of the opening tag (handles both /> and >)
            $tagEnd = strpos($content, '>', $pos);

            if ($tagEnd === false) {
                $pos++;

                continue;
            }

            $tagEnd += 1;
            $tag = substr($content, $tagStart, $tagEnd - $tagStart);

            // For prefix-based searches, verify the slug also exists in the tag.
            if ($searchBy === 'prefix' && ! str_contains($tag, 'slug="'.$rowSlug.'"')) {
                $pos++;

                continue;
            }

            $escaped = preg_quote($attrName, '/');
            $newTag = preg_replace(
                '/('.$escaped.'=")[^"]*(")/s',
                '${1}'.addcslashes($value, '$\\').'${2}',
                $tag,
                1
            );

            if ($newTag !== $tag) {
                return substr($content, 0, $tagStart).$newTag.substr($content, $tagEnd);
            }

            $pos++;
        }

        return $content;
    }

    /**
     * Resolve which default attribute to update and what to search for in the blade file.
     *
     * @return array{0: string, 1: string, 2: string} [attrName, searchBy, searchValue]
     */
    private function resolveAttr(string $rowSlug, string $key): array
    {
        if ($key === 'section_classes') {
            return ['default-section-classes', 'slug', $rowSlug];
        }

        if ($key === 'section_container_classes') {
            return ['default-container-classes', 'slug', $rowSlug];
        }

        // Strip trailing _classes
        $base = substr($key, 0, -8);

        foreach (self::SECONDARY_SUFFIXES as $suffix => $attrPart) {
            if (str_ends_with($base, $suffix)) {
                $prefix = substr($base, 0, -strlen($suffix));

                return ['default-'.$attrPart.'-classes', 'prefix', $prefix];
            }
        }

        // Simple {prefix}_classes → default-classes on component with prefix={base}
        return ['default-classes', 'prefix', $base];
    }
}
