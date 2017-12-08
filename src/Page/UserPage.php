<?php

namespace PhpLayoutClass\Page;

use PhpLayoutClass\Layout\DefaultLayout;
use PhpLayoutClass\Model\User;
use PhpLayoutClass\PageInfo;

final class UserPage extends DefaultLayout
{
    /** @var User */
    private $user;

    public function __construct(User $user)
    {
        $this->page = new PageInfo();
        $this->page->setPageTitle('user page');
        $this->user = $user;
    }

    protected function content(): void
    {
        ?>
        <p>Your name is <?= $this->user->name() ?></p>
        <?
    }

    protected function sidebar(): void
    {
        // If you want to write all content of sidebar, don't call parent method.
        parent::sidebar();
        ?>
        add user sidebar
        <?
    }
}