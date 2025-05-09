<?php

declare(strict_types=1);

namespace Crell\Path;

class PathFragment extends Path
{
    protected static function createFromString(string $path): static
    {
        $new = new static();
        $new->path = $path;
        $new->ext = pathinfo($new->path, PATHINFO_EXTENSION);

        $new->segments = array_filter(explode('/', $path));

        return $new;
    }

    protected static function createFromSegments(array $segments): static
    {
        $new = new static();

        $new->segments = $segments;

        $new->path = implode('/', $segments);

        $new->ext = pathinfo($new->path, PATHINFO_EXTENSION);

        return $new;
    }

    protected function deriveParent(): static
    {
        $segments = $this->segments;
        array_pop($segments);
        return self::createFromSegments($segments);
    }
}
