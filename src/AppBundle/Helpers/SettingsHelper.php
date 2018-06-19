<?php

namespace AppBundle\Helpers;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Settings;
use AppBundle\Entity\Clients;
use AppBundle\Entity\Users;
use AppBundle\Utils\Utils;

class SettingsHelper{
    private $em;
    private $repository;
    // private $entityHelper;
    private $cached=[];
    private $clientCached=[];
    private $userCached=[];


    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
        $this->repository=$this->em->getRepository( Settings::class );
    }

 //<editor-fold defaultstate="collapsed" desc="basic functions">
    public function getSettingValue(string $name, bool $asJSON= false){
        $value = Utils::deep_array_value($name, $this->cached);
        if(is_null($value)){
            $value=$this->cached[$name]=$this->repository->getSettingValue($name);
        }
        return $asJSON && ($value) ? json_encode($value) : $value;
    }

    public function getSettingsValues(string $namePrefix, bool $asJSON= false){
        if(!array_key_exists($namePrefix, $this->cached)){
            $this->cached[$namePrefix]=$this->repository->getSettingsValue($namePrefix);
        }
        return $asJSON ? json_encode($this->cached[$namePrefix]) : $this->cached[$namePrefix];
    }

    public function getClientSettingValue(int $clientId, string $name, bool $asJSON= false){
        if(!array_key_exists($name, $this->clientCached)){
            $this->clientCached[$name]=$this->repository->getSettingValue($name, [ 'clients' => $clientId ]);
        }
        return $asJSON ? json_encode($this->clientCached[$name]) : $this->clientCached[$name];
    }


    public function getClientSettingsValues(int $clientId, string $namePrefix, bool $asJSON = false)
    {
        if(!array_key_exists($namePrefix, $this->clientCached)){
            $this->clientCached[$namePrefix]=$this->repository->getSettingsValue($namePrefix, [ 'clients' => $clientId ]);
        }
        return $asJSON ? json_encode($this->clientCached[$namePrefix]) : $this->clientCached[$namePrefix];
    }

    public function getUserSettingValue(int $userId, string $name, bool $asJSON= false){
        if(!array_key_exists($name, $this->userCached)){
            $this->userCached[$name]=$this->repository->getSettingValue($name, [ 'users' => $userId ]);
        }
        return $asJSON ? json_encode($this->userCached[$name]) : $this->userCached[$name];
    }

    public function getUserSettingsValues(int $userId, string $namePrefix, bool $asJSON = false)
    {
        if(!array_key_exists($namePrefix, $this->userCached)){
            $this->userCached[$namePrefix]=$this->repository->getSettingsValue($namePrefix, [ 'users' => $userId ]);
        }
        return $asJSON ? json_encode($this->userCached[$namePrefix]) : $this->userCached[$namePrefix];
    }

    public function saveSetting($name, $value)
    {
        $setting = $this->repository->getSetting($name);
        if (!isset($setting)) {
            $setting = new Settings();
            $setting->setName($name);
        }
        $this->cached[$name]=$value;
        $setting->setValue($value);
        $this->em->persist($setting);
        $this->em->flush();
    }
  
    public function saveSettingForClient($clientId, $name, $value)
    {
        $setting = $this->repository->getSetting($name, [ 'clients' => $clientId ] );
        if (!isset($setting)) {
            $setting = new Settings();
            $setting->setClient($this->em->getReference(Clients::class, $clientId));
            $setting->setName($name);
        }
        $setting->setValue($value);
        $this->em->persist($setting);
        $this->em->flush();
    }

    public function saveSettingForUser($userId, $name, $value)
    {
        $setting = $this->repository->getSetting($name, [ 'users' => $userId ] );
        if (!isset($setting)) {
            $setting = new Settings();
            $setting->setUser($this->em->getReference(Users::class, $userId));
            $setting->setName($name);
        }
        $setting->setValue($value);
        $this->em->persist($setting);
        $this->em->flush();
    }
 // </editor-fold>   

 //<editor-fold defaultstate="collapsed" desc="entities settings functions">

    // public function getEntitySettingsValue(string $entityClassName, string $name)
    // {
    //     return Utils::deep_array_value($name, $this->getEntitySettings($entityClassName));
    // }

    // public function getEntitySettings(string $entityClassName): array
    // {
    //     $en=strtolower($entityClassName);
    //     if(!array_key_exists($entityClassName, $this->entitiesSettings)){
    //         $this->genEntitySettings($entityClassName);
    //     }      
    //     return is_array($this->entitiesSettings[$entityClassName]) ? $this->entitiesSettings[$entityClassName] : [];
    // }

    // private function genEntitySettings(string $entityClassName):void
    // {
    //     $en=$this->entityHelper->getEntityName($entityClassName);
    //     $this->entitiesSettings[$entityClassName]=array_merge($this->getSettingsValues($en), [
    //        'en' => $en,
    //        'ecn' => $entityClassName,
    //        'fields' => $this->entityHelper->getEntityShortNames($entityClassName)
    //     ]);
    //     $genCustom='custom'.$entityClassName.'Settings';
    //     if(\method_exists($this, $genCustom)){
    //         $this->$genCustom();
    //     }            
    // }
    
    // private function genEntitiesSettings(array $entitiesClasses = null):void
    // {
    //     $this->entitiesSettings = [];
    //     if (is_null($entitiesClasses) || count($entitiesClasses)==0 ) {
    //         $entitiesClasses = $this->entityHelper->entitiesClasses;
    //     }
    //     foreach ($entitiesClasses as $ec) {
    //         $this->genEntitySettings($ec);
    //     }
    // }

    // public function getEntitiesSettings(array $entitiesClasses=null):array
    // {
    //     $entitiesSettings=[];
    //     if (is_null($entitiesClasses) || count($entitiesClasses)==0 ) {
    //         $entitiesClasses = $this->entityHelper->entitiesClasses;
    //     }
    //     foreach ($entitiesClasses as $ec) {
    //         $entitiesSettings[$ec]=$this->getEntitySettings($ec);
    //     }
    //     return $entitiesSettings;
    // }

    // public function getEntityNumerate(string $entityClassName, ?int $clientId=null):array
    // {
    //     $numerate=[
    //         'nr' => null,
    //         'numberGenerator' => null
    //     ];
    //     $en=strtolower($entityClassName);
    //     $nrName=$en.'-number';
    //     $nrGeneratorName=$en.'-numberGenerator';
    //     $es=$this->getEntitySettings($entityClassName);
    //     if(!is_null($clientId)){
    //         $numerate['nr']=$this->getClientSettingValue($clientId, $nrName);   
    //         $numerate['numberGenerator']=$this->getClientSettingValue($clientId, $nrGeneratorName);
    //     }
    //     if(is_null($numerate['nr'])){
    //         $nr=$this->getSettingValue($nrName);
    //     }      
    //     if(is_null($numerate['numberGenerator'])){
    //         $numerate['numberGenerator']=Utils::deep_array_value('numberGenerator', $es, []);
    //     }      
    //     return $numerate;

    // }

    // public function saveEntityNumber(string $entityClassName, int $nr, ?int $clientId=null){
    //     $nrName=strtolower($entityClassName).'-number';
    //     if (is_null($clientId) || is_null($this->getClientSettingValue($clientId, $nrName))) {
    //         $this->saveSetting( $nrName, $nr);
    //     }else{
    //         $this->saveSettingForClient($clientId, $nrName, $nr);
    //     }
    // }

//  //<editor-fold defaultstate="collapsed" desc="Custom functions">
//     private function customPriceListItemsSettings():void
//     {
//         $this->addDics($this->entitiesSettings['PriceListItems'], [ 'Colors', 'Sizes']);
//     }

//     private function customOrdersSettings():void
//     {
//         $this->addDics($this->entitiesSettings['Orders'], ['Models' , 'Colors', 'Trims', 'Sizes']);
//     }

//     private function customPriceListsSettings():void
//     {
//         $this->addDics($this->entitiesSettings['PriceLists'], ['PriceListItems' ]);
//     }

//     private function customPricesSettings():void
//     {
//         $this->addDics($this->entitiesSettings['Prices'], ['PriceListItems' ]);
//     }

//     private function addDics(&$settings, array $entitiesClasses):void
//     {
//         foreach ($entitiesClasses as $ecn){
//             $settings['dictionaries'][$ecn]=$this->getDic($ecn);            
//         }          
//     }

//     public function getDic(string $entityClassName):array
//     {
//         if(!Utils::deep_array_key_exists(['dictionaries', $entityClassName], $this->entitiesSettings)){
//             $this->entitiesSettings['dictionaries'][$entityClassName]=$this->entityHelper->getDic($entityClassName);
//         }
//         return $this->entitiesSettings['dictionaries'][$entityClassName];
//     }
//  // </editor-fold>   

    public function getTableSettings(string $entityName, $tableType = 'index')
    {
        $prefix = "tables-" . $entityName;
        $type = strtolower($tableType);
        $table = $this->getSettingValue($prefix . "-" . $type);
        if (!is_array($table)) {
            $tables = $this->getSettingValue( $prefix);
            $table = is_array($tables) && is_array($tables[$type]) ? $tables[$type] : [];
        }
        return $table;
    }


 
    //

}