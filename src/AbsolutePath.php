<?php

declare(strict_types=1);

namespace Crell\Path;

class AbsolutePath extends Path
{
    public const string StreamSeparator = '://';

    /**
     * The stream this path uses, like "ftp", "vfs", "aws", etc.
     *
     * If the is just a `/` rooted path with no stream, this property is null.
     */
    public readonly ?string $stream;

    /**
     * Whether or not this path actually exists on disk.
     */
    public bool $exists {
        get => file_exists($this->path);
    }

    /**
     * The SPL file object of the file this path represents.
     */
    public private(set) \SplFileInfo $fileInfo {
        get => $this->fileInfo ??= new \SplFileInfo($this->path);
    }

    /**
     * Returns the contents of the file this path represents, or null if the file does not actually exist.
     */
    public function contents(): ?string
    {
        return file_get_contents($this->path) ?: null;
    }

    protected static function createFromString(string $path): static
    {
        $new = new static();
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
    protected static function createFromSegments(array $segments, ?string $stream = null): static
    {
        $new = new static();

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
