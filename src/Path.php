<?php

declare(strict_types=1);

namespace Crell\Path;

abstract class Path implements \Stringable
{
    /**
     * A list of path segments in this path.
     *
     * Each path segment is what would be separated by a / if the path were stringified.
     *
     * @var array<string>
     */
    protected protected(set) array $segments = [];

    /**
     * The file extension of this path's file, or null if not a file.
     */
    public protected(set) readonly ?string $ext;

    /**
     * Whether this path represents a file or not.
     */
    public protected(set) bool $isFile {
        get => $this->isFile ??= $this->deriveIsFile();
    }

    /**
     * The path as a normalized string.
     */
    protected protected(set) readonly string $path;

    /**
     * The last segment of the path.
     *
     * If the path is a file, this property is how you access it.
     */
    public protected(set) string $end {
        get => $this->end ??= $this->segments[array_key_last($this->segments)] ?? '';
    }

    /**
     * Whether it is safe to concatenate a path fragment to this path.
     */
    protected bool $concatable = true;

    /**
     * Path itself cannot be constructed externally. Only via the static method.
     */
    protected function __construct() {}

    /**
     * Creates a new Path object from a string.
     *
     * Child classes should return their own type only.
     */
    abstract protected static function createFromString(string $path): Path;

    /**
     * Creates a new Path object from an array of path segments.
     *
     * Child classes should return their own type only.
     *
     * @param string[] $segments
     *   An array of path segments
     */
    abstract protected static function createFromSegments(array $segments): Path;

    /**
     * Computes the path object representing the parent of the current path.
     *
     * Child classes should take care of the root case.
     */
    abstract protected function deriveParent(): Path;

    /**
     * Creates a new path object.
     *
     * This method will handle determining if the value to return should be
     * an absolute path or path fragment.
     */
    public static function create(string $path): Path
    {
        if (str_contains($path, '..')) {
            throw new \InvalidArgumentException('Paths containing ".." are not allowed');
        }
        // @todo Probably more security filtering here.

        $class = static::getClass($path);
        return $class::createFromString($path);
    }

    /**
     * Returns a new path object, with the provided path concatenated at the end.
     *
     * If this path already ends in a file, then concatenation is not allowed and an exception will be thrown.
     */
    public function concat(string|Path $fragment): Path
    {
        if ($this->isFile) {
            throw new \InvalidArgumentException('Cannot append a path fragment onto a path to a file.');
        }

        if (is_string($fragment)) {
            return $this->concat(self::create($fragment));
        }

        if (! $fragment->concatable) {
            throw new \InvalidArgumentException('Stream-based paths may not be used to append to an existing path');
        }

        $combinedSegments = [...$this->segments, ...$fragment->segments];

        if ($this instanceof AbsolutePath) {
            /* @phpstan-ignore arguments.count (Because PHPStan just hates statics.) */
            return static::createFromSegments($combinedSegments, $this->stream);
        }

        return static::createFromSegments($combinedSegments);
    }

    /**
     * Returns the parent path as an object.
     *
     * This would ideally be a virtual property, but then it wouldn't allow
     * for a "static" value. We want child classes to always return their own type.
     */
    public function parent(): static
    {
        return $this->deriveParent();
    }

    public function __toString(): string
    {
        return $this->path;
    }

    /**
     * Determines if this path specifies a file rather than a directory or abstract path.
     */
    protected function deriveIsFile(): bool
    {
        return str_contains($this->end, '.');
    }

    /**
     * Derives which path type a given string represents.
     *
     * @param string $path
     * @phpstan-return class-string
     */
    protected static function getClass(string $path): string
    {
        return match (true) {
            str_starts_with($path, '/'), str_contains($path, AbsolutePath::StreamSeparator) => AbsolutePath::class,
            default => PathFragment::class,
        };
    }
}
