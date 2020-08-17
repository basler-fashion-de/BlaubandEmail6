<?php declare(strict_types=1);

namespace Blauband\EmailBase;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class BlaubandEmail6 extends Plugin
{
    /**
     * @inheritDoc
     */
    public function uninstall(UninstallContext $context): void
    {
        if ($context->keepUserData()) {
            parent::uninstall($context);

            return;
        }

        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);
        $connection->executeQuery('DROP TABLE IF EXISTS `blauband_email_logged_mail`');
    }
}
