<?php

namespace Tests\PlanSeptenal\Entity;

use PlanSeptenalBundle\Entity\PlanSeptenalIndividual;
use PlanSeptenalBundle\Entity\TramitePlanSeptenal;

class PlanSeptenalIndividualTest extends \PHPUnit_Framework_TestCase
{
    /**
      * @expectedException     Exception
      * @expectedExceptionCode 10
      */
    public function testPlanSeptenalMustHaveASevenYearsRange()
    {
        $planSeptenalIndividual = new PlanSeptenalIndividual(2010, 2015);
    }

    public function testPlanSeptenalIndividualTramiteCount()
    {
        $beca = (new TramitePlanSeptenal)
            ->setTipo('beca')
            ->setMesInicial('01/2016')
            ->setMesFinal('06/2016');

        $sabatico = (new TramitePlanSeptenal)
            ->setTipo('sabatico')
            ->setMesInicial('01/2014')
            ->setMesFinal('12/2014');

        $planSeptenalIndividual = new PlanSeptenalIndividual(2010, 2016);
        $planSeptenalIndividual->addTramite($beca);
        $planSeptenalIndividual->addTramite($sabatico);

        $tramites = $planSeptenalIndividual->getTramites();
        $this->assertCount(2, $tramites);
    }

    /**
      * @expectedException     Exception
      * @expectedExceptionCode 20
      */
    public function testTramiteMustBeWithinPlanSeptenalRange()
    {
        $beca = (new TramitePlanSeptenal)
            ->setTipo('beca')
            ->setMesInicial('01/2009')
            ->setMesFinal('06/2009');

        $planSeptenalIndividual = new PlanSeptenalIndividual(2010, 2016);

        $planSeptenalIndividual->addTramite($beca);
    }

    /**
      * @expectedException     Exception
      * @expectedExceptionCode 30
      */
    public function testTramiteRangesMustBeDisjoint()
    {
        $beca = (new TramitePlanSeptenal)
            ->setTipo('beca')
            ->setMesInicial('01/2016')
            ->setMesFinal('06/2016');

        $sabatico = (new TramitePlanSeptenal)
            ->setTipo('sabatico')
            ->setMesInicial('04/2016')
            ->setMesFinal('08/2016');

        $planSeptenalIndividual = new PlanSeptenalIndividual(2010, 2016);

        $planSeptenalIndividual->addTramite($beca);
        $planSeptenalIndividual->addTramite($sabatico);
    }
}
