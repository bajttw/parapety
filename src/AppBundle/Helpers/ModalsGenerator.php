<?php
namespace AppBundle\Helpers;
use AppBundle\Utils\Utils;

class ModalsGenerator extends ElementsGenerator
{

    protected $tmplh;
    
    protected $genType='modal';
    protected $type='my';


    public function __construct( EntityHelper $entityHelper, SettingsHelper $settingsHelper, TransHelper $transHelper, TemplateHelper $templateHelper){
        $this->eh=$entityHelper;
        $this->sh=$settingsHelper;
        $this->th=$transHelper;
        $this->tmplh=$templateHelper;
    }


    protected function getContent(array $panelOptions):?array
    {
        $contentName = Utils::deep_array_value('contentName', $panelOptions);
        if($contentName){
            return $this->tmplh->getPath(
                $contentName,
                Utils::deep_array_value('contentGeneric', $panelOptions, false),
                $this->ecn,
                'm'
            );
        }
        return null;
    }
    
    protected function getData(array $modalOptions):?array
    {
        $data=[];
        $source=Utils::deep_array_value('dataSource', $modalOptions);
        if(is_array($source)){
            switch($source['type']){
                case 'settings':
                    $data=$this->sh->getSettingValue($source['query']);
                break;
                case 'entity':
                    $data = $this->eh->getRepository($source['query'])->getList(Utils::deep_array_value('options', $source, [])) ;
                break;
            }
        }
        return $data;
    }

    protected function getSettings(array $modalOptions):?array
    {
        $data=[];
        $source=Utils::deep_array_value('settingsSource', $modalOptions);
        if(is_array($source)){
            switch($source['type']){
                case 'settings':
                    $data=$this->sh->getSettingValue($source['query']);
                break;
                case 'entity':
                    $data = $this->eh->getRepository($source['query'])->getList(Utils::deep_array_value('options', $source, [])) ;
                break;
            }
        }
        return $data;
    }

    protected function generateElement(?array $elementOptions=null):array
    {
        $elementOptions= ($elementOptions) ?: $this->options;
        $name= $this->getName($elementOptions);
        return [  
            'name' => $name,         
            'title' => $this->th->modalTitle($name, $this->en),
            'attr' => $this->getAttr($elementOptions),
            'd' => $this->getD($elementOptions),
        ];
    }

    public function generate(?string $type=null,  ?string $entityClassName='', array $options=[]):array
    {
        $this->init($type, $entityClassName, $options);
        $modal=$this->generateElement();
        $this->setId($modal);
        $this->setData($modal);
        $this->setSettings($modal);
        $this->setContent();
        return $modal;
    }

    public function generateEntityModal(?string $type=null,  ?string $entityClassName=null, array $options=[]):array
    {
        $this->init($type, $entityClassName, $options);
        $modal=$this->generateElement();
        $modal['en'] = $this->en;
        $modal['ecn'] = $this->ecn;
        $this->setId($modal);
        $this->addClass($modal, 'ajax');
        $this->setData($modal);
        $this->setSettings($modal);
        return $modal;
    }

    public function generateFieldModal(string $fieldName, string $entityClassName='', array $options=[]):array
    {       
        $modal=$this->generate(null, $entityClassName, array_replace_recursive([
                'name' => $fieldName,
                'contentName' => 'field',
                'contentGeneric' => true,
                'attr' => [
                    'class' => 'modal-field',
                ],
                'd' => [
                    'set-focus' => '.field'
                ]
            ],
            $options
        ));
        $this->setElementProperty('fieldType', $options);
        $this->setElementProperty('fieldAttr', $options);
        return $modal;
    }

    public function generateExpModal(?string $entityClassName=null, array $options=[]):array
    {       
        $this->init('exp', $entityClassName, $options);
        $modal=$this->generate(null, $entityClassName, array_replace_recursive([
                'name' => $this->getName(null, 'exp'),
                'contentName' => 'field',
                'contentGeneric' => true,
                'attr' => [
                    'class' => 'modal-field',
                ],
                'd' => [
                    'set-focus' => ''
                ]
            ],
            $options
        ));     
        $this->setElementProperty('fieldType', $options);
        $this->setElementProperty('fieldAttr', $options);
        $modal['showSave']=false;   
        return $modal;
    }

    public function generateFieldModals(array $fields , array $options=[]):array
    {
        $modals = [];
        $this->init($type, $entityClassName, $options);
        foreach($fields as $name => $fmo){
            $modals[$name]=$this->generateFieldModal(
                $name,
                Utils::deep_array_value('ecn', $fmo, ''),
                array_replace_recursive(
                    $options,
                    $fmo
                )
            );
        }
        return $modals;
    }


}