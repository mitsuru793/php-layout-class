<?php
declare(strict_types=1);

namespace PhpLayoutClass;

class PageInfo
{
    /** @var string */
    private $baseTitle = 'My Site';

    /** @var string */
    private $pageTitle;

    public function title(): string
    {
       return "{$this->pageTitle} - {$this->baseTitle}";
    }

    public function pageTitle(): string
    {
        return $this->pageTitle;
    }

    public function setPageTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
}
