<?php

namespace Kaliop\EzRemoteIdBundle\EventListener;

use eZ\Publish\API\Repository\Exceptions\BadStateException;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\PermissionResolver;
use EzSystems\EzPlatformAdminUi\Tab\Event\TabEvents;
use EzSystems\EzPlatformAdminUi\Tab\Event\TabGroupEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RemoteIdTabSubscriber implements EventSubscriberInterface
{
    /**
     * @var PermissionResolver
     */
    private $permissionResolver;

    /**
     * RemoteIdTabSubscriber constructor.
     * @param PermissionResolver $permissionResolver
     */
    public function __construct(PermissionResolver $permissionResolver)
    {
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            TabEvents::TAB_GROUP_PRE_RENDER => 'checkPermissions'
        ];
    }

    /**
     * @param TabGroupEvent $event
     */
    public function checkPermissions(TabGroupEvent $event)
    {
        $params = $event->getParameters();
        $content = $params['content'] ?? null;

        $hasAccess = false;
        if($content) {
            try {
                $hasAccess = $this->permissionResolver->canUser('kaliop_ez_remote_id', 'view', $content);
            } catch (BadStateException $e) {
                $hasAccess = false;
            } catch (InvalidArgumentException $e) {
                $hasAccess = false;
            }
        }

        if (!$hasAccess) {
            $data = $event->getData();
            $tabs = $data->getTabs();
            if (key_exists('reference-tab', $tabs)) {
                $event->getData()->removeTab('reference-tab');
            }
        }
    }
}
