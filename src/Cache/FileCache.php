<?php

declare(strict_types=1);

namespace Mrcl\SlimRoutes\Cache;

use Mrcl\SlimRoutes\Routing\SlimRoute;

use function file_get_contents;
use function file_put_contents;
use function serialize;
use function unserialize;

final class FileCache
{
    public function __construct(
        private string $file
    ) {
    }

    public function write(array $data): bool
    {
        return file_put_contents($this->file, serialize($data)) > 0;
    }

    public function read(): mixed
    {
        return unserialize(file_get_contents($this->file), ['allowed_classes' => [SlimRoute::class]]);
    }
}
