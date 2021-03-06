<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2015-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @copyright Copyright (c) 2015 Apiary Ltd. <support@apiary.io>
 */

namespace ZF\Apigility\Documentation\ApiBlueprint;

use ZF\Apigility\Documentation\Service as BaseService;

class ResourceGroup
{
    /**
     * @var BaseService
     */
    protected $service;

    /**
     * @var Resource[]
     */
    private $resources = [];

    /**
     * @param BaseService $service
     */
    public function __construct(BaseService $service)
    {
        $this->service = $service;
        $this->createResources();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->service->getName();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->service->getDescription();
    }

    /**
     * @return Resource[]
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * @return string
     */
    private function getCollectionUri()
    {
        return substr($this->service->getRoute(), 0, strpos($this->service->getRoute(), '['));
    }

    /**
     * @return string
     */
    private function getEntityUri()
    {
        return str_replace(['[', ']', '{/', '{:'], ['{', '}', '/{', '{'], $this->service->getRoute());
    }

    /**
     * Create resources from service.
     *
     * If service has a route identifier, creates both entity and collection
     * resources; otherwise, creates an RPC resource.
     *
     * @return void
     */
    private function createResources()
    {
        // If there is routeIdentifierName, it is REST service and we need to
        // handle both collection and entities
        if ($this->service->getRouteIdentifierName()) {
            $this->resources[] = new Resource(
                $this->service,
                $this->service->getOperations(),
                $this->getCollectionUri(),
                Resource::RESOURCE_TYPE_COLLECTION
            );
            $this->resources[] = new Resource(
                $this->service,
                $this->service->getEntityOperations(),
                $this->getEntityUri(),
                Resource::RESOURCE_TYPE_ENTITY
            );
            return;
        }

        $this->resources[] = new Resource(
            $this->service,
            $this->service->getOperations(),
            $this->getEntityUri(),
            Resource::RESOURCE_TYPE_RPC
        );
    }
}
