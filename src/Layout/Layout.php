<?php
declare(strict_types=1);

namespace PhpLayoutClass\Layout;

use PhpLayoutClass\PageInfo;

abstract class Layout
{
    /** @var PageInfo */
    protected $page;

    abstract public function __toString(): string;

    abstract protected function content(): void;
}
