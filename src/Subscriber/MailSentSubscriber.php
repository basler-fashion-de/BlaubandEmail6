<?php declare(strict_types=1);

namespace Blauband\EmailBase\Subscriber;

use Shopware\Core\Content\MailTemplate\Service\Event\MailSentEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Content\MailTemplate\Api\MailActionController;

class MailSentSubscriber implements EventSubscriberInterface
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
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        EntityRepositoryInterface $loggedMailRepository,
        EntityRepositoryInterface $customerRepository,
        SystemConfigService $systemConfigService,
        RequestStack $requestStack
    ) {
        $this->loggedMailRepository = $loggedMailRepository;
        $this->customerRepository   = $customerRepository;
        $this->systemConfigService = $systemConfigService;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Shopware\Core\Content\MailTemplate\Service\Event\MailSentEvent' => 'onSaveLetter',
            'kernel.controller' => 'onChangeConfig',
        ];
    }

    public function onSaveLetter(MailSentEvent $event)
    {
        /** @var EntityCollection $entities */
        $customers = $this->customerRepository->search(
            (new Criteria())->addFilter(new EqualsAnyFilter('email', $event->getRecipients())),
            Context::createDefaultContext()
        );

        $request = $this->requestStack->getCurrentRequest();
        $this->loggedMailRepository->create(
            [
                [
                    'fromMail' => $request->request->get('from'),
                    'toMail' => implode(', ', $event->getRecipients()),
                    'bccMail' => $request->request->get('bcc'),
                    'subject' => $event->getSubject(),
                    'bodyHtml' => $event->getContents()['text/html'],
                    'bodyPlain' => $event->getContents()['text/plain'],
                    'customer' => $customers->first()->getId(),

                ],
            ],
            Context::createDefaultContext()
        );
    }

    public function onChangeConfig(ControllerEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $requestController = $request->attributes->get('_controller');
        $controller = sprintf('%s::%s', MailActionController::class, 'send');

        if ($controller === $requestController) {
            $this->systemConfigService->set('core.basicInformation.email', $request->request->get('from'));
            $this->systemConfigService->set('core.mailerSettings.deliveryAddress', $request->request->get('bcc'));
        }
    }
}
