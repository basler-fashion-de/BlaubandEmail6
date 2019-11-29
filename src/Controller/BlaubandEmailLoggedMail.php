<?php declare(strict_types=1);

namespace Blauband\EmailBase\Controller;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @RouteScope(scopes={"api"})
 */
class BlaubandEmailLoggedMail extends AbstractController
{
    /**
     * @Route("/api/v{version}/search/blauband-email-logged-mail", name="api.action.search.blauband-email-logged-mail", methods={"GET"})
     */
    public function getLoggedMails(Request $request, Context $context): JsonResponse
    {
        /** @var EntityRepositoryInterface loggedMailRepository */
        $this->loggedMailRepository = $this->container->get('blauband_email_logged_mail.repository');

    }
}
