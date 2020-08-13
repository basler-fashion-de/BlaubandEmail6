<?php declare(strict_types=1);

namespace Blauband\EmailBase\Core\Content\LoggedMail;

use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class LoggedMailDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'blauband_email_logged_mail';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return LoggedMailCollection::class;
    }

    public function getEntityClass(): string
    {
        return LoggedMailEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection(
            [
                (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
                (new StringField('from_mail', 'fromMail'))->addFlags(new Required()),
                (new StringField('to_mail', 'toMail'))->addFlags(new Required()),
                (new StringField('subject', 'subject'))->addFlags(new Required()),
                (new LongTextField('body_html', 'bodyHtml'))->addFlags(new Required(), new AllowHtml()),
                (new LongTextField('body_plain', 'bodyPlain'))->addFlags(new Required()),
                (new ManyToOneAssociationField('customer', 'customer_id', CustomerDefinition::class, 'id', false)),
                (new StringField('bcc_mail', 'bccMail'))->addFlags(new Required()),

                (new FkField('customer_id', 'customer', CustomerDefinition::class)),

                (new CreatedAtField()),
                (new UpdatedAtField()),

            ]
        );
    }
}
