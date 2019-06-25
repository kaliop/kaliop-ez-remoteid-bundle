<?php

namespace Kaliop\EzRemoteIdBundle\Security;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;
use eZ\Publish\API\Repository\Values\User\Limitation;

class RemoteIdPolicyProvider implements PolicyProviderInterface
{
    /**
     * @param ConfigBuilderInterface $configBuilder
     * @return array|void
     */
    public function addPolicies(ConfigBuilderInterface $configBuilder)
    {
        $configBuilder->addConfig([
            'kaliop_ez_remote_id' => [
                'view' => [
                    Limitation::CONTENTTYPE,
                    Limitation::LOCATION,
                    Limitation::SUBTREE
                ],
                'edit' => [
                    Limitation::CONTENTTYPE,
                    Limitation::LOCATION,
                    Limitation::SUBTREE
                ]
            ]
        ]);
    }
}
