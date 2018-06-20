<?php

namespace AppBundle\Helpers;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface ;
use AppBundle\Utils\Utils;

class EntityHelper{

    private $entitiesSettings=[];

    private $bundleName='AppBundle';
    private $entityClassName='';
    public $entitiesClasses = ['Clients', 'Settings', 'Uploads', 'Users', 'Notes', 'Colors', 'Models', 'Sizes', 'Trims', 'Orders', 'Positions', 'Products', 'Productions', 'Deliveries', 'Invoices', 'PriceLists', 'PriceListItems', 'Prices'];
   
    private $sc;
    private $em;
    private $sh;

    public function __construct(ContainerInterface $seviceContainer){
        $this->sh=$seviceContainer->get('helper.settings');
        $this->em=$seviceContainer->get('doctrine.orm.entity_manager');
    }
    // public function __construct(EntityManager $entityManager, SettingsHelper $settingsHelper){
    //     $this->sh=$settingsHelper;
    //     $this->em = $entityManager;
    // }

    public function setEntityClassName(string $entityClassName){
        $this->entityClassName= $entityClassName;

    }

    public function getEntityClassName(?string $entityClassName=null){
        return ($entityClassName) ?: $this->entityClassName;
    }

    public function getNamespace(string $name, ?string $entityClassName=null, $suffix = '', string $bundleName=null): string
    {
        $entityClassName= ($entityClassName) ?: $this->entityClassName;
        $str = explode(':', $entityClassName);
        if (count($str) > 1) {
            $bundleName = $str[0];
            $entityClassName = $str[1];
        }
        return (is_null($bundleName) ? $this->bundleName : $bundleName) . '\\' . ucfirst($name) . '\\' . $entityClassName . $suffix;
    }

    public function getRepositoryNamespace(?string $entityClassName=null, string $bundleName=null ):string
    {
        return $this->getNamespace('Repository', $entityClassName, 'Repository');
    }

    public function getControllerNamespace(?string $entityClassName=null, string $bundleName=null ):string
    {
        return $this->getNamespace('Controller', $entityClassName, 'Controller');
    }

    public function getEntityNamespace(?string $entityClassName=null, string $bundleName=null):string
    {
        return $this->getNamespace('Entity', $entityClassName);
    }

    public function getEntityName(?string $entityClassName=null, string $bundleName=null):string
    {
        return ($this->getEntityNamespace($entityClassName))::en;
    }

    public function getEntityShortNames(?string $entityClassName=null, string $bundleName=null):array
    {
        return ($this->getEntityNamespace($entityClassName))::$shortNames;
    }

    public function getFormNamespace(?string $entityClassName=null, string $bundleName=null):string
    {
        return $this->getNamespace('Form', $entityClassName, 'Type');
    }

    public function getDic(?string $entityClassName=null){
        $entityClassName= ($entityClassName) ?: $this->entityClassName;
        if(!Utils::deep_array_key_exists(['dictionaries', $entityClassName], $this->entitiesSettings)){
            $this->entitiesSettings['dictionaries'][$entityClassName]=$this->getRepository($entityClassName)->getDic();
        }
        return $this->entitiesSettings['dictionaries'][$entityClassName];
    }

    public function getRepository(?string $entityClassName=null)
    {
        return $this->em->getRepository($this->getEntityNamespace($entityClassName));
    }

    public function getCount(?string $entityClassName=null):int
    {
        return $this->getRepository($entityClassName)->getCount();
    }

    public function newEntity(?string $entityClassName=null, array $entityOptions=[]){
        $ens=$this->getEntityNamespace($entityClassName);
        return new $ens($entityOptions);
    }

    public function newEntityGenerator(){
        $entity=new \stdClass();
        $entity->items= new ArrayCollection();
        return $entity;
    }

    public function fromBase($condition, ?string $entityClassName=null, bool $exeption=false){
        $repository = $this->getRepository($entityClassName);
        $entity = is_array($condition) ? $repository->findOneBy($condition) : $repository->find($condition);
        if (!$entity && $exeption) {
            throw $this->createNotFoundException('NOT FOUND');
            
            // $this->getTransHelper()->messageText('notFound', $this->)
            // throw $this->createNotFoundException(
            //     $this->trans([
            //         $this->getTransHelper()->errorText('base', ''),
            //         $this->getTransHelper()->errorText('notFound', $en),
            //     ])
            //     .' - ' 
            //     . (is_array($condition) ? "Warunki wyszukiwania: " . json_encode($condition) : 'ID: ' . $condition)
            // );
        }
        return $entity;
    }

 //<editor-fold defaultstate="collapsed" desc="EntitySettings">
    private function genSettings(string $entityClassName):void
    {
        $en=$this->getEntityName($entityClassName);
        $this->entitiesSettings[$entityClassName]=array_merge($this->sh->getSettingsValues($en), [
            'en' => $en,
            'ecn' => $entityClassName,
            'fields' => $this->getEntityShortNames($entityClassName)
        ]);
        $genCustom='custom'.$entityClassName.'Settings';
        if(\method_exists($this, $genCustom)){
            $this->$genCustom();
        }            
    }
 
    public function getSettingsValue(string $name, ?string $entityClassName=null)
    {
        return Utils::deep_array_value($name, $this->getSettings($entityClassName));
    }
 
    public function getSettings(?string $entityClassName=null): array
    {
        $entityClassName= ($entityClassName) ?: $this->entityClassName;
        if(!array_key_exists($entityClassName, $this->entitiesSettings)){
            $this->genSettings($entityClassName);
        }      
        return $this->entitiesSettings[$entityClassName];
    }

    private function genEntitiesSettings(array $entitiesClasses = null):void
    {
        $entitiesClasses= ($entitiesClasses) ?: $this->entitiesClasses;
        $this->entitiesSettings = [];
        foreach ($entitiesClasses as $ec) {
            $this->genEntitySettings($ec);
        }
    }

    public function getEntitiesSettings(array $entitiesClasses=null):array
    {
        if(is_null($entitiesClasses)){
            foreach ($this->entitiesClasses as $ec) {
                if(!array_key_exists($ec, $this->entitiesSettings)){
                    $this->genSettings($ec);
                }      
            }                
            return $this->entitiesSettings;
        }
        $entitiesSettings=[];
        foreach ($entitiesClasses as $ec) {
            $entitiesSettings[$ec]=$this->getEntitySettings($ec);
        }
        return $entitiesSettings;
    }

    public function getNumerate(?string $entityClassName=null, ?int $clientId=null):array
    {
        $numerate=[
            'nr' => null,
            'numberGenerator' => null
        ];
        $en=$this->getEntityName($entityClassName);
        $nrName=$en.'-number';
        $nrGeneratorName=$en.'-numberGenerator';
        $es=$this->getSettings($entityClassName);
        if(!is_null($clientId)){
            $numerate['nr']=$this->sh->getClientSettingValue($clientId, $nrName);   
            $numerate['numberGenerator']=$this->sh->getClientSettingValue($clientId, $nrGeneratorName);
        }
        if(is_null($numerate['nr'])){
            $nr=$this->sh->getSettingValue($nrName);
        }      
        if(is_null($numerate['numberGenerator'])){
            $numerate['numberGenerator']=Utils::deep_array_value('numberGenerator', $es, []);
        }      
        return $numerate;

    }

    public function saveEntityNumber(string $entityClassName, int $nr, ?int $clientId=null){
        $nrName=$this->getEntityName($entityClassName).'-number';
        if (is_null($clientId) || is_null($this->sh->getClientSettingValue($clientId, $nrName))) {
            $this->sh->saveSetting( $nrName, $nr);
        }else{
            $this->sh->saveSettingForClient($clientId, $nrName, $nr);
        }
    }


 //<editor-fold defaultstate="collapsed" desc="Custom functions">
    private function customPriceListItemsSettings():void
    {
        $this->addDics($this->entitiesSettings['PriceListItems'], [ 'Colors', 'Sizes']);
    }

    private function customOrdersSettings():void
    {
        $this->addDics($this->entitiesSettings['Orders'], ['Models' , 'Colors', 'Trims', 'Sizes']);
    }

    private function customPriceListsSettings():void
    {
        $this->addDics($this->entitiesSettings['PriceLists'], ['PriceListItems' ]);
    }

    private function customPricesSettings():void
    {
        $this->addDics($this->entitiesSettings['Prices'], ['PriceListItems' ]);
    }

    private function addDics(&$settings, array $entitiesClasses):void
    {
        foreach ($entitiesClasses as $ecn){
            $settings['dictionaries'][$ecn]=$this->getDic($ecn);            
        }          
    }

 // </editor-fold>  
}