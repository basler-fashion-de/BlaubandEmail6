<?php declare(strict_types=1);

namespace Blauband\EmailBase\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1563869805CreateLoggedMail extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1563869805;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery(
            '
            CREATE TABLE IF NOT EXISTS `blauband_email_logged_mail` (
                `id` binary(16) NOT NULL,
                `from_mail` varchar(255),
                `to_mail` varchar(255) NOT NULL,
                `bcc_mail` varchar(255),
                `subject` varchar(255),
                `body_html` longtext,
                `body_plain` longtext,
                `order_id` binary(16) DEFAULT NULL,
                `customer_id` binary(16) DEFAULT NULL,
                `created_at` datetime(3),
                `updated_at` datetime(3),
                PRIMARY KEY (`id`),
                KEY `blauband_email_logged_mail_customer_id_fk` (`customer_id`),
                KEY `blauband_email_logged_mail_order_id_fk` (`order_id`),
                CONSTRAINT `blauband_email_logged_mail_customer_id_fk` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`),
                CONSTRAINT `blauband_email_logged_mail_order_id_fk` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`)

            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        '
        );
    }

    public function updateDestructive(Connection $connection): void
    {
        $connection->executeQuery('DROP TABLE IF EXISTS `blauband_email_logged_mail`');
    }
}
