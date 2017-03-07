<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Usuario;
use AppBundle\Entity\Rol;

class UsuarioTest extends \PHPUnit_Framework_TestCase
{
    protected $tony;

    protected function setUp()
    {
        $this->tony = new Usuario();
        $this->tony->setCedula('1234');
        $this->tony->setPrimerNombre('Anthony');
        $this->tony->setSegundoNombre('Edward');
        $this->tony->setPrimerApellido('Stark');
        $this->tony->setSegundoApellido('');
        $this->tony->setNacionalidad('_');
        $this->tony->setCorreo('tony@stark.com');
        $this->tony->setTelefono(0);
        $this->tony->setRif(0);
        $this->tony->setActivo(true);
        $this->tony->setDireccion('Malibu');
        $this->tony->setEstatusId(1);
    }

    public function testGetNombreCorto()
    {
        $this->assertEquals('Anthony Stark', $this->tony->getNombreCorto());
    }

    public function testGetNombreCompleto()
    {
        $this->assertEquals('Anthony Edward Stark ', $this->tony->getNombreCompleto());
    }

    public function testAddRol()
    {
        $superhero = (new Rol())->setNombre('superhero');

        $this->tony->addRol($superhero);

        $this->assertContains('superhero', $this->tony->getRoles());
    }
}