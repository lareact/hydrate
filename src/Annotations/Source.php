<?php
declare(strict_types=1);

namespace Golly\Hydrate\Annotations;

/**
 * Class Source
 * @package Golly\Hydrate\Annotations
 * @Annotation
 */
final class Source
{
    /**
     * field name of source
     *
     * @var string
     */
    public string $field;
}
