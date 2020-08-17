<?php declare(strict_types=1);

namespace Blauband\EmailBase\Subscriber;

use Shopware\Core\Content\MailTemplate\Service\Event\MailSentEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class Mail implements EventSubscriberInterface
{
    /**
     * @var EntityRepositoryInterface
     */
    private $loggedMailRepository;
    /**
     * @var EntityRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    public function __construct(
        EntityRepositoryInterface $loggedMailRepository,
        EntityRepositoryInterface $customerRepository,
        SystemConfigService $systemConfigService
    ) {
        $this->loggedMailRepository = $loggedMailRepository;
        $this->customerRepository   = $customerRepository;
        $this->systemConfigService = $systemConfigService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Shopware\Core\Content\MailTemplate\Service\Event\MailSentEvent' => 'onMailSend',
        ];
    }


    public function onMailSend(MailSentEvent $event)
    {
        $fromMail = $this->systemConfigService->get('core.basicInformation.email');
        $bccMail  = '';

        /** @var ParameterBag $request */
        $request = $GLOBALS['request']->request;

        //Eigene Sende Workflow
        if ($request->get('from')) {
            $fromMail = $request->get('from');
        }

        if ($request->get('bcc')) {
            $bccMail = $request->get('bcc');
        }

        /** @var EntityCollection $entities */
        $customers = $this->customerRepository->search(
            (new Criteria())->addFilter(new EqualsAnyFilter('email', $event->getRecipients())),
            $event->getContext()
        );

        $this->loggedMailRepository->create(
            [
                [
                    'fromMail' => $fromMail,
                    'toMail' => implode(', ', $event->getRecipients()),
                    'bccMail' => $bccMail,
                    'subject' => $event->getSubject(),
                    'bodyHtml' => $event->getContents()['text/html'],
                    'bodyPlain' => $event->getContents()['text/plain'],
//                    'order' => null,
                    'customer' => $customers->first() ? $customers->first()->getId() : null,

                ],
            ],
            $event->getContext()
        );
    }
}
