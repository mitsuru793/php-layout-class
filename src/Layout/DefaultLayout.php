<?php
declare(strict_types=1);

namespace PhpLayoutClass\Layout;

abstract class DefaultLayout extends Layout
{
    public function __toString(): string
    {
        ob_start();
        ?>
        <html>
        <head>
            <title><?= $this->page->title() ?></title>
        </head>
        <body>
        <div class="content">
            <? $this->content() ?>
        </div>
        <div class="sidebar">
            <? $this->sidebar() ?>
        </div>
        </body>
        </html>
        <?
        return ob_get_clean();
    }

    protected function sidebar(): void
    {
        ?>
        sidebar content
        <?
    }
}