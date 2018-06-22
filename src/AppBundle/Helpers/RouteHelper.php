<?php

namespace AppBundle\Helpers;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
class RouteHelper
{

    private $ac;// AuthorizationCheckerInterface
    private $eh;// EntityHelper

    public function __construct(AuthorizationCheckerInterface $autorizationChecker, EntityHelper $entityHelper){
        $this->ac=$autorizationChecker;
        $this->eh=$entityHelper;
    }

    private function getUserPrefix():string
    {
        if($this->ac->isGranted('ROLE_SUPER_ADMIN')){
            return 'app_admin_';
        }else if($this->ac->isGranted('ROLE_ADMIN')){
            return 'app_employee_';
        }else if($this->ac->isGranted('ROLE_USER')){
            return 'app_client_';
        }
        return '';
    }


    public function getRoute(?string $routeSuffix = null, ?string $entityClassName = null):string
    {
        return $this->getUserPrefix().$this->eh->getEntityName($entityClassName).(isset($routeSuffix) ? '_' . $routeSuffix : '');
    }

    public function getClientRoute(?string $routeSuffix = null, ?string $entityClassName = null):string
    {
        return $this->getUserPrefix()
            .$this->eh->getEntityName($entityClassName)
            .($this->ac->isGranted('ROLE_ADMIN') ? '_client' : '')
            .(isset($routeSuffix) ? '_' . $routeSuffix : '');
    }
   
    
 
}