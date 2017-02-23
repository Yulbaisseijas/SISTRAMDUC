<?php

namespace Tests\PlanSeptenal\Entity;

use PlanSeptenalBundle\Entity\PlanSeptenalColectivo;
use PlanSeptenalBundle\Entity\PlanSeptenalIndividual;
use PlanSeptenalBundle\Entity\TramitePlanSeptenal;

class PlanSeptenalColectivoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException     Exception
     * @expectedExceptioncode 10
     */
    public function testPlanSeptenalColectivoMustBeSeptennial()
    {
        $planColectivo = new PlanSeptenalIndividual(2010, 2017);
    }

    public function testPlanSeptenalColectivoMustContainPlanesSeptenalesIndividualesAfterAdditions()
    {
        $beca = (new TramitePlanSeptenal)
            ->setTipo('beca')
            ->setMesInicial('01/2016')
            ->setMesFinal('06/2016');

        $sabatico = (new TramitePlanSeptenal)
            ->setTipo('sabatico')
            ->setMesInicial('01/2014')
            ->setMesFinal('12/2014');

        $planSeptenalIndividualUno = new PlanSeptenalIndividual(2010, 2016);
        $planSeptenalIndividualUno->addTramite($beca);
        $planSeptenalIndividualUno->addTramite($sabatico);

        $postgrado = (new TramitePlanSeptenal)
            ->setTipo('postgrado')
            ->setMesInicial('01/2016')
            ->setMesFinal('06/2016');

        $licencia = (new TramitePlanSeptenal)
            ->setTipo('licencia')
            ->setMesInicial('01/2014')
            ->setMesFinal('12/2014');

        $planSeptenalIndividualDos = new PlanSeptenalIndividual(2010, 2016);
        $planSeptenalIndividualDos->addTramite($postgrado);
        $planSeptenalIndividualDos->addTramite($licencia);

        $planSeptenalColectivo = new PlanSeptenalColectivo(2010, 2016);
        $planSeptenalColectivo->addPlanSeptenalIndividual($planSeptenalIndividualUno);
        $planSeptenalColectivo->addPlanSeptenalIndividual($planSeptenalIndividualDos);

        $planes = $planSeptenalColectivo->getPlanesSeptenalesIndividuales();
        $this->assertContains($planSeptenalIndividualUno, $planes);
        $this->assertContains($planSeptenalIndividualDos, $planes);
    }

    /**
     * @expectedException     Exception
     * @expectedExceptioncode 10
     */
    public function testPlanesIndividualesDateRangeMustCoincideWithPlanColectivo()
    {
        $planSeptenalIndividual = new PlanSeptenalIndividual(2010, 2016);

        $planSeptenalColectivo = new PlanSeptenalColectivo(2011, 2017);

        $planSeptenalColectivo->addPlanSeptenalIndividual($planSeptenalIndividual);
    }
}