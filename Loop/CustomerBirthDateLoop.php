<?php

namespace CustomerBirthDate\Loop;

use CustomerBirthDate\Model\CustomerBirthDate;
use CustomerBirthDate\Model\CustomerBirthDateQuery;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class CustomerBirthDateLoop
 * @package CustomerBirthDate\Loop
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerBirthDateLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('id')
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $query = CustomerBirthDateQuery::create();
        
        if (null !== $id = $this->getId()) {
            $query->filterById($id);
        }
        
        return $query;
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var CustomerBirthDate $customerBirthDate */
        foreach ($loopResult->getResultDataCollection() as $customerBirthDate) {

            $loopResultRow = new LoopResultRow($customerBirthDate);

            $loopResultRow->set("CUSTOMER_ID", $customerBirthDate->getId());
            $loopResultRow->set("BIRTHDATE", $customerBirthDate->getBirthDate('Y-m-d'));

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
