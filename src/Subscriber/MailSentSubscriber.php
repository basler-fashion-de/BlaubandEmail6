<?php declare(strict_types=1);

namespace Blauband\EmailBase\Subscriber;

use Shopware\Core\Content\MailTemplate\Service\Event\MailSentEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Storefront\Controller\CheckoutController;
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
    private $orderRepository;

    private $systemConfigService;
    /**
     * @var RequestStack
     */
    private $requestStack;

    private $defaultEmail;

    private $defaultBcc;

    public function __construct(
        EntityRepositoryInterface $loggedMailRepository,
        EntityRepositoryInterface $customerRepository,
        EntityRepositoryInterface $orderRepository,
        SystemConfigService $systemConfigService,
        RequestStack $requestStack
    )
    {
        $this->loggedMailRepository = $loggedMailRepository;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
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

    public function onSaveLetter(MailSentEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $requestController = $request->attributes->get('_controller');
        $mailController = sprintf('%s::%s', MailActionController::class, 'send');
        $orderController = sprintf('%s::%s', CheckoutController::class, 'order');

        if ($mailController !== $requestController && $orderController !== $requestController) {
            return;
        }

        /** @var EntityCollection $entities */
        $customer = $this->customerRepository->search(
            (new Criteria())->addFilter(new EqualsFilter('email', array_key_first($event->getRecipients()))),
            Context::createDefaultContext()
        );

        if (!$customer->getTotal()) {
            return;
        }

        if ($mailController === $requestController) {
            $this->systemConfigService->set('core.mailerSettings.senderAddress', $this->defaultEmail);
            $this->systemConfigService->set('core.mailerSettings.deliveryAddress', $this->defaultBcc);
        }

        $this->loggedMailRepository->create(
            [
                [
                    'fromMail' => $request->request->get('from') ?? $this->systemConfigService->get('core.mailerSettings.senderAddress'),
                    'toMail' => array_key_first($event->getRecipients()),
                    'bccMail' => $request->request->get('bcc') ?? $this->systemConfigService->get('core.mailerSettings.deliveryAddress'),
                    'subject' => $event->getSubject(),
                    'bodyHtml' => $event->getContents()['text/html'],
                    'bodyPlain' => $event->getContents()['text/plain'],
                    'customer' => $customer->first()->getId(),
                ],
            ],
            Context::createDefaultContext()
        );
    }

    public function onChangeConfig(ControllerEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $requestController = $request->attributes->get('_controller');
        $controller = sprintf('%s::%s', MailActionController::class, 'send');

        if ($controller === $requestController) {
            $this->defaultEmail = $this->systemConfigService->get('core.mailerSettings.senderAddress');
            $this->defaultBcc = $this->systemConfigService->get('core.mailerSettings.deliveryAddress');

            $this->systemConfigService->set('core.mailerSettings.senderAddress', $request->request->get('from'));
            $this->systemConfigService->set('core.mailerSettings.deliveryAddress', $request->request->get('bcc'));
        }
    }
}
