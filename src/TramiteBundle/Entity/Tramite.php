<?php

namespace TramiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

use TramiteBundle\Entity\Recaudo;
use ComisionRemuneradaBundle\Entity\SolicitudComisionServicio;
use TramiteBundle\Entity\Transicion;
use AppBundle\Entity\Usuario;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"tramite" = "Tramite", "comision" = "ComisionRemuneradaBundle\Entity\SolicitudComisionServicio"})
 */
class Tramite
{
    protected  $type = "tramite";

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var array
     * @Assert\Count(
     *      min = "1",
     *      max = "10",
     *      minMessage = "Debe tener al menos 1 Archivo, en caso de ser el tomo completo",
     *      maxMessage = "Sólo puede tener como maximo {{ limit }} Archivos"
     * )
     * @ORM\OneToMany(targetEntity="Recaudo", mappedBy="tramite", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    protected $recaudos;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $observacion = "hola";

    /**
     * @ORM\ManyToOne(targetEntity="TipoTramite", inversedBy="tramites")
     * @ORM\JoinColumn(name="tipo_tramite_id", referencedColumnName="id")
     */
    protected $tipo_tramite;

    /**
     * @ORM\OneToOne(targetEntity="Transicion", mappedBy="tramite")
     */
    protected  $transicion;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario", inversedBy="tramite")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    protected $owner;

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     *
     * @return Tramite
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set tipo_tramite
     *
     * @param \TramiteBundle\Entity\TipoTramite $tipoTramite
     *
     * @return Tramite
     */
    public function setTipoTramite(\TramiteBundle\Entity\TipoTramite $tipo_tramite = null)
    {
        $this->tipo_tramite = $tipo_tramite;

        return $this;
    }

    /**
     * Get tipo_tramite
     *
     * @return \TramiteBundle\Entity\TipoTramite
     */
    public function getTipoTramite()
    {
        return $this->tipo_tramite;
    }

    /**
     * Get recaudos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRecaudos()
    {
        return $this->recaudos;
    }

    /**
     * Add recaudo
     *
     * @return tramite
     */
    public function addRecaudo(Recaudo $recaudo)
    {
        $this->recaudos[] = $recaudo;
        $recaudo->setTramite($this);

        return $this;
    }
    
    public function removeRecaudo(Recaudo $recaudo)
    {
        $this->recaudos->removeElement($recaudo);
        $recaudo->setTramite(null);
    }
    
    public function removeAllRecaudos()
    {
        $this->recaudos->clear();
    }

    public function assignTo(Usuario $owner)
    {
        $this->owner = $owner;
        $owner->ownTramite($this);

        return $this;
    }

    /**
     * Set solicitud comision servicio
     *
     * @return Tramite
     */
    /*public function setSolicitudComisionServicio(SolicitudComisionServicio $solicitud_comision_servicio)
    {
        $this->solicitud_comision_servicio = $solicitud_comision_servicio;
        $solicitud_comision_servicio->addTramite($this);
        return $this;
    }*/

    /**
     * Get solicitud comision servicio
     *
     * @return \ComisionRemuneradaBundle\Entity\SolicitudComisionServicio
     */
    /*public function getSolicitudComisionServicio()
    {
        return $this->solicitud_comision_servicio;
    }*/
}