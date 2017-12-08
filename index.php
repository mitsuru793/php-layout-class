<?php
declare(strict_types=1);

use PhpLayoutClass\Model\User;
use PhpLayoutClass\Page\UserPage;

require_once __DIR__ . '/vendor/autoload.php';

class MyController
{
    public function actionMethod()
    {
        $user = new User('mike');
        echo new UserPage($user);
    }
}

$controller = new MyController();
$controller->actionMethod();
