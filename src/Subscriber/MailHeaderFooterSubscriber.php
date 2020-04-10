<?php declare(strict_types=1);

namespace Blauband\EmailBase\Subscriber;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PostWriteValidationEvent;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MailHeaderFooterSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityRepositoryInterface
     */
    private $salesChannelRepository;

    public function __construct(EntityRepositoryInterface $salesChannelRepository)
    {
        $this->salesChannelRepository = $salesChannelRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PostWriteValidationEvent' => 'onSave'
        ];
    }

    public function onSave(PostWriteValidationEvent $event): void
    {
        // todo check all variants
        foreach ($event->getCommands() as $command) {
            if (!$command->getPayload()) {
                return;
            }

            if (
                $command->getEntityExistence()->getEntityName() !== 'system_config' ||
                $command->getPayload()['configuration_key'] !== 'EmailBase.config.emailHeaderFooter'
            ) {
                continue;
            }

            $payload = $command->getPayload();
            $mailHeaderFooterId = json_decode($payload['configuration_value'], true)['_value'];
            $salesChannelBytesId = $payload['sales_channel_id'];

            if (null === $salesChannelBytesId) {
                continue;
            }

            $this->salesChannelRepository->update(
                [
                    ['id' => Uuid::fromBytesToHex($salesChannelBytesId), 'mailHeaderFooterId' => $mailHeaderFooterId],
                ],
                Context::createDefaultContext()
            );
        }
    }
}
