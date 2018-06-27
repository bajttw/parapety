<?php
namespace AppBundle\Helpers;


use AppBundle\Utils\Utils;

class ClientElementsGenerator extends ElementsGenerator{

    protected $rh;// RouteHelper
    protected $clientId;

    protected function init(?string $type=null,  ?string $entityClassName=null, array $options=[]):void
    {
        parent::init($type, $entityClassName, $options);
        $this->clientId=Utils::deep_array_value('clientId', $options);
    }

    protected function getUrl(string $type, array $parameters):string
    {
        return $this->clientId ? $this->rh->getClientUrl($type, $this->ecn, array_replace($parameters, [ 'cid' => $this->clientId])) 
                : $this->rh->getEmployeeUrl($type, $this->ecn, $parameters);
    }

}