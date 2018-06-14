<?php

namespace AppBundle\Repository;
use AppBundle\Utils\Utils;

/**
 * PriceListsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PriceListsRepository extends AppRepository
{

    protected function addFilters(){
        foreach ($this->filters as $name => $filter) {
            $filterName= isset($filter['name']) ? $filter['name'] : $name;
            if($filterName == 'client'){
                $this->filters[$name]['name'] = 'clients.id';
            }
        }
        // if($this->filters)
        return parent::addFilters();
    }
    
}
