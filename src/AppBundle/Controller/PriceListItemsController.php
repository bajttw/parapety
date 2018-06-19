<?php

namespace AppBundle\Controller;

use AppBundle\Utils\Utils;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\PriceListItems;
use AppBundle\Controller\AppController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * PriceListItems controller.
 *
 */
class PriceListItemsController extends AppController
{
    const en = 'pricelistitems';
    const ec = 'PriceListItems';

    public static function getToolbarBtn($type = 'index', $options = [])
    {
        $cid = Utils::deep_array_value('cid', $options);
        $b = [
            'new' => [
                'action' => 'new',
                'attr' => [
                    'class' => 'btn-primary',
                    'target' => '_blank'
                ]
            ],
            'generate' => [
                'action' => 'generate',
                'attr' => [
                    'class' => 'btn-default',
                    'target' => '_blank'
                ],
                'routeParam' => ['type' => 'w']
            ]
        ];
        $btns = [];
        switch ($type) {
            case 'index':
                foreach (['new', 'generate'] as $n) {
                    $btns[] = $b[$n];
                }
            break;
            default:
                foreach (['new'] as $n) {
                    $btns[] = $b[$n];
                }
        }
        return $btns;
    }

    public static function getFilters($type = 'index', $options = [])
    {
        $id = Utils::deep_array_value('id', $options);
        $filters = [];
        $fs = [
            'active' => self::$activeFilter,
            'size' => [
                'name' => 'size.id',
                'label' => 'pricelistitems.label.filter.size',
                'source' => [
                    'type' => 'entity',
                    'query' => 'Sizes'
                ],
                'attr' => [
                    'multiple' => 'multiple'
                ],
                'd' => [
                    'widget' => 'multiselect'
                ]
            ],
            'color' => [
                'name' => 'color.id',
                'label' => 'pricelistitems.label.filter.color',
                'source' => [
                    'type' => 'entity',
                    'query' => 'Colors'
                ],
                'attr' => [
                    'multiple' => 'multiple'
                ],
                'd' => [
                    'widget' => 'multiselect'
                ]
            ]
        
        ];

        switch ($type) {
            case 'index':
                foreach (['active', 'size', 'color'] as $f) {
                    self::addFilter($filters, $fs[$f], $f);
                }
                break;
            case 'pricelists_form':
                foreach (['size', 'color'] as $f) {
                    self::addFilter($filters, $fs[$f], $f);
                }
                break;
            default :
                foreach (['size', 'color'] as $f) {
                    self::addFilter($filters, $fs[$f], $f);
                }

        }        
        return $filters;

    }    

    //  <editor-fold defaultstate="collapsed" desc="Custom functions">
    public static function updateActive($entityManager, $size = null, $color = null)
    {
        $filters = [];

        if ($color) {
            $filters['color'] = [
                'name' => 'color',
                'value' => $color->getId()
            ];
        }
        if ($size) {
            $filters['color'] = [
                'name' => 'size',
                'value' => $size->getId()
            ];
        }
        $entities = $entityManager->getRepository(self::getEntityPath())->getEntities(['filters' => $filters]);
        foreach ($entities as $entity) {
            $entity->updateActive();
            $entityManager->persist($entity);
        }
        $entityManager->flush();
    }

    protected function setCustomFormOptions()
    {
        $this->formOptions['attr']['data-admin'] = $this->isAdmin();
        $this->formOptions['attr']['data-form'] = static::en;
        return $this;
    }

    // public static function genCustomSettings($controller, &$entitySettings = [])
    // {
    //     foreach (['Colors', 'Sizes'] as $ecn) {
    //         $entitySettings['dictionaries'][$ecn] = $controller->getDic($ecn);
    //     }
    //     return $entitySettings;
    // }
   
    // </editor-fold>
    protected function newEntityGenerator()
    {
        parent::newEntityGenerator();
        $this->entity->size = [];
        $this->entity->color = [];
    }

    // protected function createGenerateForm($options = [])
    // {
    //     $this->entity=new \stdClass();
    //     $this->entity->size=[];
    //     $this->entity->color=[];
    //     $this->entity->items= new ArrayCollection();
    //     $this->setFormOptions('add', $options);
    //     $this->formOptions['attr']['data-form'] = self::en . 'generate';
    //     $this->formOptions['attr']['data-uniques'] =  json_encode($this->getEntityManager()->getRepository($this->entityNameSpaces['path'])->getUniques());
    //     $this->setRenderOptions([
    //         'title' => $this->titleText('generate'),
    //         'template_body' => $this->tmplPath( 'generate_body', static::ec),
    //         'form_options' => [
    //             'submit' => self::genSubmitBtn('save')
    //         ]
    //     ]);
    //     $this->formSystem = $this->createForm(self::getNameSpace('Form', "PriceListItemsGenerate", 'Type'), $this->entity, $this->formOptions);

    //     return $this;
    // }

 //  <editor-fold defaultstate="collapsed" desc="Actions">
    // public function generateAction(Request $request, $cid=0){
    //     if (!$this->preAction($request, $cid, ['checkPrivilages' => 1, 'entitySettings' => false])) {
    //         return $this->responseAccessDenied(true);
    //     }
    //     $this->setFormTemplate(static::ec, 'generate');
    //     $this->createGenerateForm();
    //     return $this->renderSystem(true);
    // }

 // </editor-fold>

    public function ajaxListToPriceListAction(Request $request, $cid = 0){
        if (!$this->preAction($request, $cid, ['entitySettings' => false, 'checkPrivilages' => 1 ])) {
            return $this->responseAccessDenied(true);
        }
        // $this->setEntityQuery();
        return new JsonResponse($this->getEntiesFromBase($request, 'getListToPriceList'));
    }
}
