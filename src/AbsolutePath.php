<?php

declare(strict_types=1);

namespace Crell\Path;

class AbsolutePath extends Path
{
    public const string StreamSeparator = '://';

    public readonly ?string $stream;

    public bool $exists {
        get => file_exists($this->path);
    }

    public private(set) \SplFileInfo $fileInfo {
        get => $this->fileInfo ??= new \SplFileInfo($this->path);
    }

    public function contents(): ?string
    {
        return file_get_contents($this->path) ?: null;
    }

    protected static function createFromString(string $path): AbsolutePath
    {
        $new = new self();
        $new->path = $path;

        if (str_contains($path, self::StreamSeparator)) {
            [$new->stream, $pathPart] = explode(self::StreamSeparator, $path);
            $new->segments = explode('/', $pathPart);
            $new->concatable = false;
        } else {
            $new->segments = array_filter(explode('/', $path));
            $new->stream = null;
        }

        $new->ext = pathinfo($new->path, PATHINFO_EXTENSION);

        return $new;
    }

    /**
     * @param string|null $stream
     *   A stream name, like "ftp", "vfs", or "aws".
     */
    protected static function createFromSegments(array $segments, ?string $stream = null): AbsolutePath
    {
        $new = new self();

        $new->segments = $segments;

        $new->path = ($stream ? $stream . self::StreamSeparator : '/') . implode('/', $segments);
        $new->stream = $stream;
        $new->ext = pathinfo($new->path, PATHINFO_EXTENSION);

        return $new;
    }

    protected function deriveParent(): static
    {
        $segments = $this->segments;
        array_pop($segments);
        return static::createFromSegments($segments, $this->stream);
    }
}
