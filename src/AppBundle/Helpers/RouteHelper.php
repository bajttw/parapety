<?php

namespace AppBundle\Helpers;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Routing\RouterInterface;
class RouteHelper
{

    private $ac;// AuthorizationCheckerInterface
    private $eh;// EntityHelper
    private $router;

    public function __construct(AuthorizationCheckerInterface $autorizationChecker, RouterInterface $routerInterface, EntityHelper $entityHelper){
        $this->ac=$autorizationChecker;
        $this->router=$routerInterface;
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
   
    public function getUlr(?string $routeSuffix = null, ?string $entityClassName = null):string
    {
        $this->generate($this->get())
    }
 
}