<?php declare(strict_types=1);

namespace Blauband\EmailBase\Core\Content\LoggedMail;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class LoggedMailCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return LoggedMailEntity::class;
    }
}