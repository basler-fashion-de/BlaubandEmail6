<?php declare(strict_types=1);

namespace Blauband\EmailBase\Subscriber;

use Shopware\Core\Content\MailTemplate\Service\Event\MailSentEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
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

    public function __construct(
        EntityRepositoryInterface $loggedMailRepository,
        EntityRepositoryInterface $customerRepository
    ) {
        $this->loggedMailRepository = $loggedMailRepository;
        $this->customerRepository   = $customerRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Shopware\Core\Content\MailTemplate\Service\Event\MailSentEvent' => 'onMailSend',
        ];
    }


    public function onMailSend(MailSentEvent $event)
    {
        $fromMail = '';
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
            \Shopware\Core\Framework\Context::createDefaultContext()
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
                    'customer' => $customers->first()->getId(),

                ],
            ],
            \Shopware\Core\Framework\Context::createDefaultContext()
        );
    }
}
