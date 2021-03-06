<?php 

namespace ConcursoOposicionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ConcursosBundle\Entity\Concurso;
use ConcursosBundle\Entity\Jurado;
use ConcursoOposicionBundle\Entity\Recusacion;
use AppBundle\Entity\Usuario;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\User\UserInterface;

class ListadosOposicionController extends Controller
{
     /**
     * @Route("/concursoOposicion/listadoConcursosAjax", name="listadoConcursosAjax")
     * @Method("POST")
     */
    public function listadoConcursosAjaxAction(Request $request){

        $val[][] = "";

        if($request->isXmlHttpRequest())
        {
            $repository = $this->getDoctrine()
                ->getRepository('ConcursosBundle:Concurso');
             
            $query = $repository->createQueryBuilder('p')
                ->where('p.tipo = :cadena')
                ->setParameter('cadena', 'Oposicion')
                ->orderBy('p.id', 'DESC')
                ->getQuery();
             
            $concurso = $query->getResult();

            if (!$concurso) {
                 return new JsonResponse("N");
            }else
            {
                $val = $this->asignarFilaConcursos($concurso,'id',$val, 0);
                $val = $this->asignarFilaConcursos($concurso,'usuario',$val, 1);
                $val = $this->asignarFilaConcursos($concurso,'observacion',$val, 2);
                $val = $this->asignarFilaConcursos($concurso,'vacantes',$val, 3);
                $val = $this->asignarFilaConcursos($concurso,'area',$val, 4);
                $val = $this->asignarFilaConcursos($concurso,'inicio',$val, 5);
                $val = $this->asignarFilaFechaRecepcion($concurso,'recepcion',$val);
                $val = $this->asignarFilaFechaPresentacion($concurso,'presentacion',$val);
            }
            return new JsonResponse($val);
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    private function asignarFilaFechaPresentacion($object,$entidad,$val)
    {
        $i = 0;
        foreach($object as $value)
        {
            if ($value->getFechaPresentacion() != null)
                $val[$entidad][$i] = date_format($value->getFechaPresentacion(), 'd-m-Y');
            else
                $val[$entidad][$i] = $value->getFechaPresentacion();

           $i++;
        }
        return $val;
    }

    private function asignarFilaFechaRecepcion($object,$entidad,$val)
    {
        $i = 0;
        foreach($object as $value)
        {
            if ($value->getFechaRecepDoc() != null)
                $val[$entidad][$i] = date_format($value->getFechaRecepDoc(), 'd-m-Y');
           else
                $val[$entidad][$i] = $value->getFechaRecepDoc();
           $i++;
        }
        return $val;
    }

    private function asignarFilaConcursos($object,$entidad,$val, $pos)
    {
        $i = 0;
        foreach($object as $value)
        {
           switch ($pos) {
               case 1: $val[$entidad][$i] = $this->usuarioAsigna($value->getIdUsuario()); break;
               case 2: $val[$entidad][$i] = $value->getObservaciones(); break;
               case 3: $val[$entidad][$i] = $value->getNroVacantes();break;
               case 4: $val[$entidad][$i] = $value->getAreaPostulacion(); break;
               case 5: $val[$entidad][$i] = date_format($value->getFechaInicio(), 'd-m-Y'); break;

               default: $val[$entidad][$i] = $value->getId(); break;
           }
           $i++;
        }
        return $val;
    }

     /**
     * @Route("/concursoOposicion/listadoJuradosAjax", name="listadoJuradosAjax")
     * @Method("POST")
     */
    public function listadoJuradosAjaxAction(Request $request){

        $val[][] = "";

        if($request->isXmlHttpRequest())
        {
        	if ($request->get("id") != "NO"){
        		
        		$query = $this->getDoctrine()
		        		->getManager()
		        		->createQuery("SELECT u
				         FROM ConcursosBundle:Jurado u, ConcursosBundle:Concurso c
				         INNER JOIN  c.jurado r
				         WHERE c.id = :concurso and r = u.id and u.tipo = 'Oposicion'")
		        		         ->setParameter(':concurso', $request->get("id"));
        		
        	} else {
        	
	            $repository = $this->getDoctrine()
	                ->getRepository('ConcursosBundle:Jurado');
	             
	            $query = $repository->createQueryBuilder('p')
	                ->where('p.tipo = :cadena')
	                ->setParameter('cadena', 'Oposicion')
	                ->orderBy('p.id', 'DESC')
	                ->getQuery();
            
        	}
        	
            $jurados = $query->getResult();

            if (!$jurados) {
                 return new JsonResponse("N");
            }else
            {
                $val = $this->asignarFilaJurado($jurados,'id',$val, 0);
                $val = $this->asignarFilaJurado($jurados,'nombre',$val, 1);
                $val = $this->asignarFilaJurado($jurados,'apellido',$val, 2);
                $val = $this->asignarFilaJurado($jurados,'areainvestigacion',$val, 3);
                $val = $this->asignarFilaJurado($jurados,'facultad',$val, 4);
                $val = $this->asignarFilaJurado($jurados,'universidad',$val, 5);
                $val = $this->asignarFilaJurado($jurados,'idusuarioasigna',$val, 6);
                $val = $this->asignarFilaJurado($jurados,'cedula',$val, 7);
            }
            return new JsonResponse($val);
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    private function asignarFilaJurado($object,$entidad,$val, $pos)
    {
        $i = 0;
        foreach($object as $value)
        {
           switch ($pos) {
               case 1: $val[$entidad][$i] = $value->getNombre(); break;
               case 2: $val[$entidad][$i] = $value->getApellido(); break;
               case 3: $val[$entidad][$i] = $value->getAreaInvestigacion(); break;
               case 4: $val[$entidad][$i] = $value->getFacultad(); break;
               case 5: $val[$entidad][$i] = $value->getUniversidad(); break;
               case 6: $val[$entidad][$i] = $this->usuarioAsigna($value->getIdUsuarioAsigna()); break;
               case 7: $val[$entidad][$i] = $value->getCedula(); break;

               default: $val[$entidad][$i] = $value->getId(); break;
           }
           $i++;
        }
        return $val;
    }

    private function usuarioAsigna($id){

        if ($id == null)
            return "";
        else {

            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:Usuario');
             
            $query = $repository->createQueryBuilder('p')
                ->where('p.id = :cadena')
                ->setParameter('cadena', $id)
                ->getQuery();
             
            $usuario = $query->getResult();

            if ($usuario == null) return "";
            else return $usuario[0]->getNombreCorto();
        }
    }

     /**
     * @Route("/concursoOposicion/listadoSuplentesAjax", name="listadoSuplentesAjax")
     * @Method("POST")
     */
    public function listadoSuplentesAjaxAction(Request $request){

        $val[][] = "";

        if($request->isXmlHttpRequest())
        {
        	if ($request->get("id") != "NO"){
        	
        		$query = $this->getDoctrine()
        		->getManager()
        		->createQuery("SELECT u
				         FROM ConcursosBundle:Jurado u, ConcursosBundle:Concurso c
				         INNER JOIN  c.jurado r
				         WHERE c.id = :concurso and r = u.id and u.tipo = 'OposicionSuplentes'")
        					         ->setParameter(':concurso', $request->get("id"));
        	
        	} else {
        		
        		$repository = $this->getDoctrine()
        		->getRepository('ConcursosBundle:Jurado');
        		 
        		$query = $repository->createQueryBuilder('p')
        		->where('p.tipo = :cadena')
        		->setParameter('cadena', 'OposicionSuplentes')
        		->orderBy('p.id', 'DESC')
        		->getQuery();
        	}
    
            $jurados = $query->getResult();

            if (!$jurados) {
                 return new JsonResponse("N");
            }else
            {
                $val = $this->asignarFilaJurado($jurados,'id',$val, 0);
                $val = $this->asignarFilaJurado($jurados,'nombre',$val, 1);
                $val = $this->asignarFilaJurado($jurados,'apellido',$val, 2);
                $val = $this->asignarFilaJurado($jurados,'areainvestigacion',$val, 3);
                $val = $this->asignarFilaJurado($jurados,'facultad',$val, 4);
                $val = $this->asignarFilaJurado($jurados,'universidad',$val, 5);
                $val = $this->asignarFilaJurado($jurados,'idusuarioasigna',$val, 6);
                $val = $this->asignarFilaJurado($jurados,'cedula',$val, 7);
            }
            return new JsonResponse($val);
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

     /**
     * @Route("/concursoOposicion/listadoCPECAjax", name="listadoCPECAjax")
     * @Method("POST")
     */
    public function listadoCPECAjaxAction(Request $request){

        $val[][] = "";

        if($request->isXmlHttpRequest())
        {
        	if ($request->get("id") != "NO"){
        		 
        		$query = $this->getDoctrine()
        		->getManager()
        		->createQuery("SELECT u
				         FROM ConcursosBundle:Jurado u, ConcursosBundle:Concurso c
				         INNER JOIN  c.jurado r
				         WHERE c.id = :concurso and r = u.id and u.tipo = 'OposicionCpec'")
        					         ->setParameter(':concurso', $request->get("id"));
        					          
        	} else {
        	
            $repository = $this->getDoctrine()
                ->getRepository('ConcursosBundle:Jurado');
             
            $query = $repository->createQueryBuilder('p')
                ->where('p.tipo = :cadena')
                ->setParameter('cadena', 'OposicionCpec')
                ->orderBy('p.id', 'DESC')
                ->getQuery();
        	} 
                
            $jurados = $query->getResult();

            if (!$jurados) {
                 return new JsonResponse("N");
            }else
            {
                $val = $this->asignarFilaJurado($jurados,'id',$val, 0);
                $val = $this->asignarFilaJurado($jurados,'nombre',$val, 1);
                $val = $this->asignarFilaJurado($jurados,'apellido',$val, 2);
                $val = $this->asignarFilaJurado($jurados,'areainvestigacion',$val, 3);
                $val = $this->asignarFilaJurado($jurados,'facultad',$val, 4);
                $val = $this->asignarFilaJurado($jurados,'universidad',$val, 5);
                $val = $this->asignarFilaJurado($jurados,'idusuarioasigna',$val, 6);
                $val = $this->asignarFilaJurado($jurados,'cedula',$val, 7);
            }
            return new JsonResponse($val);
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

     /**
     * @Route("/concursoOposicion/listadoSuplenteCPECAjax", name="listadoSuplenteCPECAjax")
     * @Method("POST")
     */
    public function listadoSuplenteCPECAjaxAction(Request $request){

        $val[][] = "";

        if($request->isXmlHttpRequest())
        {
        	if ($request->get("id") != "NO"){
        		 
        		$query = $this->getDoctrine()
        		->getManager()
        		->createQuery("SELECT u
				         FROM ConcursosBundle:Jurado u, ConcursosBundle:Concurso c
				         INNER JOIN  c.jurado r
				         WHERE c.id = :concurso and r = u.id and u.tipo = 'OposicionSuplenteCpec'")
        					         ->setParameter(':concurso', $request->get("id"));
        					         	
        	} else {
        		
        		$repository = $this->getDoctrine()
        		->getRepository('ConcursosBundle:Jurado');
        		 
        		$query = $repository->createQueryBuilder('p')
        		->where('p.tipo = :cadena')
        		->setParameter('cadena', 'OposicionSuplenteCpec')
        		->orderBy('p.id', 'DESC')
        		->getQuery();
        	}           
             
            $jurados = $query->getResult();

            if (!$jurados) {
                 return new JsonResponse("N");
            }else
            {
                $val = $this->asignarFilaJurado($jurados,'id',$val, 0);
                $val = $this->asignarFilaJurado($jurados,'nombre',$val, 1);
                $val = $this->asignarFilaJurado($jurados,'apellido',$val, 2);
                $val = $this->asignarFilaJurado($jurados,'areainvestigacion',$val, 3);
                $val = $this->asignarFilaJurado($jurados,'facultad',$val, 4);
                $val = $this->asignarFilaJurado($jurados,'universidad',$val, 5);
                $val = $this->asignarFilaJurado($jurados,'idusuarioasigna',$val, 6);
                $val = $this->asignarFilaJurado($jurados,'cedula',$val, 7);
            }
            return new JsonResponse($val);
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    /**
     * @Route("/concursoOposicion/buscarConcursoAjax", name="buscarConcursoAjax")
     * @Method("POST")
     */
    public function buscarConcursoAjaxAction(Request $request){

        $val[][] = "";

        if($request->isXmlHttpRequest())
        {
            $repository = $this->getDoctrine()
                ->getRepository('ConcursosBundle:Concurso');
             
            $query = $repository->createQueryBuilder('p')
                ->where('p.id = :id')
                ->setParameter('id', $request->get("id"))
                ->getQuery();
             
            $concurso = $query->getResult();

            if (!$concurso) {
                 return new JsonResponse("N");
            }else
            {
                $val = $this->asignarFilaConcursos($concurso,'usuario',$val, 1);
                $val = $this->asignarFilaConcursos($concurso,'observacion',$val, 2);
                $val = $this->asignarFilaConcursos($concurso,'vacantes',$val, 3);
                $val = $this->asignarFilaConcursos($concurso,'area',$val, 4);
                $val = $this->asignarFilaConcursos($concurso,'inicio',$val, 5);
                $val = $this->asignarFilaFechaRecepcion($concurso,'recepcion',$val);
                $val = $this->asignarFilaFechaPresentacion($concurso,'presentacion',$val);
            }
            return new JsonResponse($val);
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    /**
     * @Route("/concursoOposicion/borrarConcursoAjax", name="borrarConcursoAjax")
     * @Method("POST")
     */
    public function borrarConcursoAjaxAction(Request $request){

        if($request->isXmlHttpRequest())
        {
            $encontrado = false;

            foreach ($this->getUser()->getRoles() as $rol => $valor) {
                
                if ($valor == "Asuntos Profesorales")
                    $encontrado = true;
            }

            if ($encontrado){

                $em = $this->getDoctrine()->getManager();

                $concurso = $em->getRepository('ConcursosBundle:Concurso')->find($request->get("id"));

                $em->remove($concurso);

                $em->flush();
                
                return new JsonResponse("S");

            } else return new JsonResponse("N");             
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    /**
     * @Route("/concursoOposicion/modificarConcursoAjax", name="modificarConcursoAjax")
     * @Method("POST")
     */
    public function modificarConcursoAjaxAction(Request $request){

        if($request->isXmlHttpRequest())
        {
            $encontrado = false;

            foreach ($this->getUser()->getRoles() as $rol => $valor) {
                
                if ($valor == "Asuntos Profesorales")
                    $encontrado = true;
            }

            if ($encontrado){

                $em = $this->getDoctrine()->getManager();

                $concurso = $em->getRepository('ConcursosBundle:Concurso')->find(intval($request->get("id")));

                if ($request->get("Vacantes") != null || $request->get("Vacantes") != "")
                	$concurso->setNroVacantes(intval($request->get("Vacantes")));
                
                if ($request->get("Area") != null || $request->get("Area") != "")
                	$concurso->setAreaPostulacion($request->get("Area"));
                
                $concurso->setIdUsuario($this->getUser()->getId());
                
                $concurso->setObservaciones($request->get("observacion"));
                               
                if ($request->get("Inicio") != null)
                {
                    $fecha = $this->cambiarFormatoFecha($request->get("Inicio"));
                    $concurso->setFechaInicio(date_create($fecha));
                }               
               
                if ($request->get("fechaDoc") != null)
                {
                    $fecha = $this->cambiarFormatoFecha($request->get("fechaDoc"));
                    $concurso->setFechaRecepDoc(date_create($fecha));
                }
                else $concurso->setFechaRecepDoc(null);
           
                if ($request->get("fechaPre") != null)
                {
                    $fecha = $this->cambiarFormatoFecha($request->get("fechaPre"));
                    $concurso->setFechaPresentacion(date_create($fecha));
                }
                else $concurso->setFechaPresentacion(null);
                
                $em->flush();
                
                return new JsonResponse("S");

            } else return new JsonResponse("N");             
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    public function cambiarFormatoFecha($fecha){

        $dia = substr($fecha, 0, 2);
        $mes = substr($fecha, 3, 2);
        $ano = substr($fecha, 6, 4);

        return $mes."/".$dia."/".$ano;
    }

    /**
     * @Route("/concursoOposicion/buscarJuradoAjax", name="buscarJuradoAjax")
     * @Method("POST")
     */
    public function buscarJuradoAjaxAction(Request $request){

        $val[][] = "";

        if($request->isXmlHttpRequest())
        {
            $repository = $this->getDoctrine()
                ->getRepository('ConcursosBundle:Jurado');
             
            $query = $repository->createQueryBuilder('p')
                ->where('p.id = :id and p.tipo = :cadena')
                ->setParameter('id', $request->get("id"))
                ->setParameter('cadena', 'Oposicion')
                ->getQuery();
             
            $jurados = $query->getResult();

            if (!$jurados) {
                 return new JsonResponse("N");
            }else
            {
                $val = $this->asignarFilaJurado($jurados,'nombre',$val, 1);
                $val = $this->asignarFilaJurado($jurados,'apellido',$val, 2);
                $val = $this->asignarFilaJurado($jurados,'areainvestigacion',$val, 3);
                $val = $this->asignarFilaJurado($jurados,'facultad',$val, 4);
                $val = $this->asignarFilaJurado($jurados,'universidad',$val, 5);
                $val = $this->asignarFilaJurado($jurados,'idusuarioasigna',$val, 6);
                $val = $this->asignarFilaJurado($jurados,'cedula',$val, 7);
            }
            return new JsonResponse($val);
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    /**
     * @Route("/concursoOposicion/buscarJuradoSuplenteAjax", name="buscarJuradoSuplenteAjax")
     * @Method("POST")
     */
    public function buscarJuradoSuplenteAjaxAction(Request $request){

        $val[][] = "";

        if($request->isXmlHttpRequest())
        {
            $repository = $this->getDoctrine()
                ->getRepository('ConcursosBundle:Jurado');
             
            $query = $repository->createQueryBuilder('p')
                ->where('p.id = :id and p.tipo = :cadena')
                ->setParameter('id', $request->get("id"))
                ->setParameter('cadena', 'OposicionSuplentes')
                ->getQuery();
             
            $jurados = $query->getResult();

            if (!$jurados) {
                 return new JsonResponse("N");
            }else
            {
                $val = $this->asignarFilaJurado($jurados,'nombre',$val, 1);
                $val = $this->asignarFilaJurado($jurados,'apellido',$val, 2);
                $val = $this->asignarFilaJurado($jurados,'areainvestigacion',$val, 3);
                $val = $this->asignarFilaJurado($jurados,'facultad',$val, 4);
                $val = $this->asignarFilaJurado($jurados,'universidad',$val, 5);
                $val = $this->asignarFilaJurado($jurados,'idusuarioasigna',$val, 6);
                $val = $this->asignarFilaJurado($jurados,'cedula',$val, 7);
            }
            return new JsonResponse($val);
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    /**
     * @Route("/concursoOposicion/borrarJuradoAjax", name="borrarJuradoAjax")
     * @Method("POST")
     */
    public function borrarJuradoAjaxAction(Request $request){

        if($request->isXmlHttpRequest())
        {
            $encontrado = false;

            foreach ($this->getUser()->getRoles() as $rol => $valor) {
                
                if ($valor == "Asuntos Profesorales")
                    $encontrado = true;
            }

            if ($encontrado){

                $em = $this->getDoctrine()->getManager();

                $jurado = $em->getRepository('ConcursosBundle:Jurado')->find($request->get("id"));

                $em->remove($jurado);

                $em->flush();
                
                return new JsonResponse("S");

            } else return new JsonResponse("N");             
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }


    /**
     * @Route("/concursoOposicion/modificarJuradoAjax", name="modificarJuradoAjax")
     * @Method("POST")
     */
    public function modificarJuradoAjaxAction(Request $request){

        if($request->isXmlHttpRequest())
        {
            $encontrado = false;

            foreach ($this->getUser()->getRoles() as $rol => $valor) {
                
                if ($valor == "Asuntos Profesorales")
                    $encontrado = true;
            }

            if ($encontrado){

                $em = $this->getDoctrine()->getManager();

                $jurado = $em->getRepository('ConcursosBundle:Jurado')->find($request->get("id"));

                if ($request->get("cedula") != null || $request->get("cedula") != "")
                    $jurado->setCedula($request->get("cedula"));

                if ($request->get("nombre") != null || $request->get("nombre") != "")
                    $jurado->setNombre($request->get("nombre"));

                if ($request->get("apellido") != null || $request->get("apellido") != "")
                    $jurado->setApellido($request->get("apellido"));

                if ($request->get("facultad") != null || $request->get("facultad") != "")
                    $jurado->setFacultad($request->get("facultad"));

                if ($request->get("universidad") != null || $request->get("universidad") != "")
                    $jurado->setUniversidad($request->get("universidad"));

                if ($request->get("area") != null || $request->get("area") != "")
                    $jurado->setAreaInvestigacion($request->get("area"));

                $jurado->setIdUsuarioAsigna($this->getUser()->getId());

                $em->flush();
                
                return new JsonResponse("S");

            } else return new JsonResponse("N");             
        }
        else
            throw $this->createNotFoundException('Error al devolver datos');
    }

    /**
     * @Route("/concursoOposicion/buscarCpecAjax", name="buscarCpecAjax")
     * @Method("POST")
     */
    public function buscarCpecAjaxAction(Request $request){

        $val[][] = "";

        if($request->isXmlHttpRequest())
        {
            $repository = $this->getDoctrine()
                ->getRepository('ConcursosBundle:Jurado');
             
            $query = $repository->createQueryBuilder('p')
                ->where('p.id = :id and p.tipo = :cadena')
                ->setParameter('id', $request->get("id"))
                ->setParameter('cadena', 'OposicionCpec')
                ->getQuery();
             
            $jurados = $query->getResult();

            if (!$jurados) {
                 return new JsonResponse("N");
            }else
            {
                $val = $this->asignarFilaJurado($jurados,'nombre',$val, 1);
                $val = $this->asignarFilaJurado($jurados,'apellido',$val, 2);
                $val = $this->asignarFilaJurado($jurados,'areainvestigacion',$val, 3);
                $val = $this->asignarFilaJurado($jurados,'facultad',$val, 4);
                $val = $this->asignarFilaJurado($jurados,'universidad',$val, 5);
                $val = $this->asignarFilaJurado($jurados,'idusuarioasigna',$val, 6);
                $val = $this->asignarFilaJurado($jurados,'cedula',$val, 7);
            }
            return new JsonResponse($val);
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    /**
     * @Route("/concursoOposicion/buscarCpecSuplenteAjax", name="buscarCpecSuplenteAjax")
     * @Method("POST")
     */
    public function buscarCpecSuplenteAjaxAction(Request $request){

        $val[][] = "";

        if($request->isXmlHttpRequest())
        {
            $repository = $this->getDoctrine()
                ->getRepository('ConcursosBundle:Jurado');
             
            $query = $repository->createQueryBuilder('p')
                ->where('p.id = :id and p.tipo = :cadena')
                ->setParameter('id', $request->get("id"))
                ->setParameter('cadena', 'OposicionSuplenteCpec')
                ->getQuery();
             
            $jurados = $query->getResult();

            if (!$jurados) {
                 return new JsonResponse("N");
            }else
            {
                $val = $this->asignarFilaJurado($jurados,'nombre',$val, 1);
                $val = $this->asignarFilaJurado($jurados,'apellido',$val, 2);
                $val = $this->asignarFilaJurado($jurados,'areainvestigacion',$val, 3);
                $val = $this->asignarFilaJurado($jurados,'facultad',$val, 4);
                $val = $this->asignarFilaJurado($jurados,'universidad',$val, 5);
                $val = $this->asignarFilaJurado($jurados,'idusuarioasigna',$val, 6);
                $val = $this->asignarFilaJurado($jurados,'cedula',$val, 7);
            }
            return new JsonResponse($val);
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    private function asignarFilaAspirante($object,$entidad,$val, $pos)
    {
        $i = 0;
        foreach($object as $value)
        {
           switch ($pos) {
               case 1: $val[$entidad][$i] = $value->getPrimerNombre(); break;
               case 2: $val[$entidad][$i] = $value->getSegundoNombre(); break;
               case 3: $val[$entidad][$i] = $value->getPrimerApellido(); break;
               case 4: $val[$entidad][$i] = $value->getSegundoApellido(); break;
               case 5: $val[$entidad][$i] = $value->getTelefono(); break;
               case 6: $val[$entidad][$i] = $value->getCorreo(); break;
               case 7: $val[$entidad][$i] = $value->getCedula(); break;
               case 8: $val[$entidad][$i] = $value->getUniversidadEgresado(); break;
               case 9: $val[$entidad][$i] = $value->getObservaciones(); break;

               default: $val[$entidad][$i] = $value->getId(); break;
           }
           $i++;
        }
        return $val;
    }

    /**
     * @Route("/concursoOposicion/listadoRecusacionAjax", name="listadoRecusacionAjax")
     * @Method("POST")
     */
    public function listadoRecusacionAjaxAction(Request $request){

        $val[][] = "";

        if($request->isXmlHttpRequest())
        {
            $repository = $this->getDoctrine()
                ->getRepository('ConcursoOposicionBundle:Recusacion');
             
            $query = $repository->createQueryBuilder('p')
                ->orderBy('p.id', 'DESC')
                ->getQuery();
             
            $recusacion = $query->getResult();

            if (!$recusacion) {
                 return new JsonResponse("N");
            }else
            {
                $val = $this->asignarFilaRec($recusacion,'id',$val, 0);
                $val = $this->asignarFilaRec($recusacion,'jurado',$val, 1);
                $val = $this->asignarFilaRec($recusacion,'aspirante',$val, 2);
                $val = $this->asignarFilaRec($recusacion,'fecha',$val, 3);
                $val = $this->asignarFilaRec($recusacion,'usuario',$val, 4);

                return new JsonResponse($val);
            }
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    private function asignarFilaRec($object,$entidad,$val, $pos)
    {
        $i = 0;
        foreach($object as $value)
        {
           switch ($pos) {
               case 1: $val[$entidad][$i] = $this->juradoAsigna($value->getJuradoId()); break;
               case 2: $val[$entidad][$i] = $this->aspiranteAsigna($value->getAspiranteId()); break;
               case 3: $val[$entidad][$i] = date_format($value->getFecha(), 'd-m-Y'); break;
               case 4: $val[$entidad][$i] = $this->usuarioAsigna($value->getUsuario()); break;

               default: $val[$entidad][$i] = $value->getId(); break;
           }
           $i++;
        }
        return $val;
    }

    private function juradoAsigna($id){

        if ($id == null)
            return "";
        else {

            $repository = $this->getDoctrine()
                ->getRepository('ConcursosBundle:Jurado');
             
            $query = $repository->createQueryBuilder('p')
                ->where('p.id = :cadena')
                ->setParameter('cadena', $id)
                ->getQuery();
             
            $usuario = $query->getResult();

            if ($usuario == null) return "";
            else return $usuario[0]->getNombre()." ".$usuario[0]->getApellido();
        }
    }

    private function aspiranteAsigna($id){

        if ($id == null)
            return "";
        else {

            $repository = $this->getDoctrine()
                ->getRepository('ConcursosBundle:Aspirante');
             
            $query = $repository->createQueryBuilder('p')
                ->where('p.id = :cadena')
                ->setParameter('cadena', $id)
                ->getQuery();
             
            $usuario = $query->getResult();

            if ($usuario == null) return "";
            else return $usuario[0]->getPrimerNombre()." ".$usuario[0]->getPrimerApellido();
        }
    }


    /**
     * @Route("/concursoOposicion/buscarRecusadoAjax", name="buscarRecusadoAjax")
     * @Method("POST")
     */
    public function buscarRecusadoAjaxAction(Request $request){

        $val[][] = "";

        if($request->isXmlHttpRequest())
        {
            $repository = $this->getDoctrine()
                ->getRepository('ConcursoOposicionBundle:Recusacion');
             
            $query = $repository->createQueryBuilder('p')
                ->where('p.id = :id')
                ->setParameter('id', $request->get("id"))
                ->getQuery();
             
            $recusacion = $query->getResult();

            if (!$recusacion) {
                 return new JsonResponse("N");
            }else
            {
                $val = $this->asignarFilaRec($recusacion,'id',$val, 0);
                $val = $this->asignarFilaRec($recusacion,'jurado',$val, 1);
                $val = $this->asignarFilaRec($recusacion,'aspirante',$val, 2);
                $val = $this->asignarFilaRec($recusacion,'fecha',$val, 3);
                $val = $this->asignarFilaRec($recusacion,'usuario',$val, 4);

                return new JsonResponse($val);
            }
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    /**
     * @Route("/concursoOposicion/borrarRecusacionAjax", name="borrarRecusacionAjax")
     * @Method("POST")
     */
    public function borrarRecusacionAjaxAction(Request $request){

        if($request->isXmlHttpRequest())
        {
            $encontrado = false;

            foreach ($this->getUser()->getRoles() as $rol => $valor) {
                
                if ($valor == "Asuntos Profesorales")
                    $encontrado = true;
            }

            if ($encontrado){

                $em = $this->getDoctrine()->getManager();

                $recusacion = $em->getRepository('ConcursoOposicionBundle:Recusacion')->find($request->get("id"));

                $em->remove($recusacion);

                $em->flush();
                
                return new JsonResponse("S");

            } else return new JsonResponse("N");             
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    /**
     * @Route("/concursoOposicion/listadoAspiranteAjax", name="listadoAspiranteAjax")
     * @Method("POST")
     */
    public function listadoAspiranteAjaxAction(Request $request){

        $val[][] = "";

        if($request->isXmlHttpRequest())
        {        
            if ($request->get("resul") == "si"){

            	$query = $this->getDoctrine()
            	->getManager()
            	->createQuery("SELECT u
                                     FROM ConcursosBundle:Aspirante u, ConcursosBundle:Concurso c
                                        INNER JOIN  c.aspirantes r
                                     WHERE c.tipo = 'Oposicion' and c.id = :concurso and r = u.id")
                                     ->setParameter(':concurso', $request->get("concurso"));
            	
            	$recusacion = $query->getResult();
            	
            } else {
            	
            	$query = $this->getDoctrine()
            	->getManager()
            	->createQuery("SELECT u
                                     FROM ConcursosBundle:Aspirante u, ConcursosBundle:Concurso c
                                        INNER JOIN  c.aspirantes r
                                     WHERE c.tipo = 'Oposicion' and r = u.id");
            	 
            	$recusacion = $query->getResult();
            }

            if (!$recusacion) {
                 return new JsonResponse("N");
            }else
            {
                $val = $this->asignarFilaAsp($recusacion,'id',$val, 0);
                $val = $this->asignarFilaAsp($recusacion,'nombre1',$val, 1);
                $val = $this->asignarFilaAsp($recusacion,'nombre2',$val, 2);
                $val = $this->asignarFilaAsp($recusacion,'apellido1',$val, 3);
                $val = $this->asignarFilaAsp($recusacion,'apellido2',$val, 4);
                $val = $this->asignarFilaAsp($recusacion,'cedula',$val, 5);
                $val = $this->asignarFilaAsp($recusacion,'tlf1',$val, 6);
                $val = $this->asignarFilaAsp($recusacion,'tlf2',$val, 7);
                $val = $this->asignarFilaAsp($recusacion,'correo',$val, 8);
                $val = $this->asignarFilaAsp($recusacion,'universidad',$val, 9);
                $val = $this->asignarFilaAsp($recusacion,'titulo',$val, 10);
                $val = $this->asignarFilaAsp($recusacion,'anho',$val, 11);
                $val = $this->asignarFilaAsp($recusacion,'observacion',$val, 12);

                return new JsonResponse($val);
            }
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    private function asignarFilaAsp($object,$entidad,$val, $pos)
    {
        $i = 0;
        foreach($object as $value)
        {
           switch ($pos) {
               case 1: $val[$entidad][$i] = $value->getPrimerNombre(); break;
               case 2: $val[$entidad][$i] = $value->getSegundoNombre(); break;
               case 3: $val[$entidad][$i] = $value->getPrimerApellido(); break;
               case 4: $val[$entidad][$i] = $value->getSegundoApellido(); break;
               case 5: $val[$entidad][$i] = $value->getCedula(); break;
               case 6: $val[$entidad][$i] = $value->getTelefono(); break;
               case 7: $val[$entidad][$i] = $value->getTelefonoSecundario(); break;
               case 8: $val[$entidad][$i] = $value->getCorreo(); break;
               case 9: $val[$entidad][$i] = $value->getUniversidadEgresado(); break;
               case 10: $val[$entidad][$i] = $value->getDescripcionTituloUniv(); break;
               case 11: $val[$entidad][$i] = $value->getAnyoGraduacion(); break;
               case 12: $val[$entidad][$i] = $value->getObservaciones(); break;

               default: $val[$entidad][$i] = $value->getId(); break;
           }
           $i++;
        }
        return $val;
    }

    /**
     * @Route("/concursoOposicion/buscarAspiranteAjax", name="buscarAspiranteAjax")
     * @Method("POST")
     */
    public function buscarAspiranteAjaxAction(Request $request){

        $val[][] = "";

        if($request->isXmlHttpRequest())
        {
            $repository = $this->getDoctrine()
                ->getRepository('ConcursosBundle:Aspirante');
             
            $query = $repository->createQueryBuilder('p')
                ->where('p.id = :id')
                ->setParameter('id', $request->get("id"))
                ->getQuery();
             
            $jurados = $query->getResult();

            if (!$jurados) {
                 return new JsonResponse("N");
            }else
            {
                $val = $this->asignarFilaAsp($jurados,'id',$val, 0);
                $val = $this->asignarFilaAsp($jurados,'nombre1',$val, 1);
                $val = $this->asignarFilaAsp($jurados,'nombre2',$val, 2);
                $val = $this->asignarFilaAsp($jurados,'apellido1',$val, 3);
                $val = $this->asignarFilaAsp($jurados,'apellido2',$val, 4);
                $val = $this->asignarFilaAsp($jurados,'cedula',$val, 5);
                $val = $this->asignarFilaAsp($jurados,'tlf1',$val, 6);
                $val = $this->asignarFilaAsp($jurados,'tlf2',$val, 7);
                $val = $this->asignarFilaAsp($jurados,'correo',$val, 8);
                $val = $this->asignarFilaAsp($jurados,'universidad',$val, 9);
                $val = $this->asignarFilaAsp($jurados,'titulo',$val, 10);
                $val = $this->asignarFilaAsp($jurados,'anho',$val, 11);
                $val = $this->asignarFilaAsp($jurados,'observacion',$val, 12);
            }
            return new JsonResponse($val);
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    /**
     * @Route("/concursoOposicion/borrarAspiranteAjax", name="borrarAspiranteAjax")
     * @Method("POST")
     */
    public function borrarAspiranteAjaxAction(Request $request){

        if($request->isXmlHttpRequest())
        {
            $encontrado = false;

            foreach ($this->getUser()->getRoles() as $rol => $valor) {
                
                if ($valor == "Asuntos Profesorales")
                    $encontrado = true;
            }

            if ($encontrado){

                $em = $this->getDoctrine()->getManager();

                $concurso = $em->getRepository('ConcursosBundle:Aspirante')->find($request->get("id"));

                $em->remove($concurso);

                $em->flush();
                
                return new JsonResponse("S");

            } else return new JsonResponse("N");             
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }

    /**
     * @Route("/concursoOposicion/modificarAspiranteAjax", name="modificarAspiranteAjax")
     * @Method("POST")
     */
    public function modificarAspiranteAjaxAction(Request $request){

        if($request->isXmlHttpRequest())
        {
            $encontrado = false;

            foreach ($this->getUser()->getRoles() as $rol => $valor) {
                
                if ($valor == "Asuntos Profesorales")
                    $encontrado = true;
            }

            if ($encontrado){

                $em = $this->getDoctrine()->getManager();

                $aspirante = $em->getRepository('ConcursosBundle:Aspirante')->find($request->get("id"));

                $aspirante->setTelefono($request->get("tlf"));
                $aspirante->setCorreo($request->get("correo"));

                $em->flush();
                
                return new JsonResponse("S");

            } else return new JsonResponse("N");             
        }
        else
             throw $this->createNotFoundException('Error al devolver datos');
    }
    
    /**
     * @Route("/concursoOposicion/listadoResultadoAjax", name="listadoResultadoAjax")
     * @Method("POST")
     */
    public function listadoResultadoAjaxAction(Request $request){
    
    	$val[][] = "";
    
    	if($request->isXmlHttpRequest())
    	{
    		    			 
    		$query = $this->getDoctrine()
    		->getManager()
    		->createQuery("SELECT u
                                     FROM ConcursosBundle:Resultado u
                                     WHERE u.cedulaAspirante = :cedula and u.idConcurso = :concurso")
    		->setParameter(':cedula', $request->get("cedula"))
    		->setParameter(':concurso', $request->get("concurso"));
    
    		$recusacion = $query->getResult();
    
    		if (!$recusacion) {
    			return new JsonResponse("N");
    		}else
    		{
    			$val = $this->asignarFilaResult($recusacion,'id',$val, 0);
    			$val = $this->asignarFilaResult($recusacion,'cedulaAspirante',$val, 1);
    			$val = $this->asignarFilaResult($recusacion,'idConcurso',$val, 2);
    			$val = $this->asignarFilaResult($recusacion,'nroPrueba',$val, 3);
    			$val = $this->asignarFilaResult($recusacion,'nota',$val, 4);
    			$val = $this->asignarFilaResult($recusacion,'notaEscrito',$val, 5);
    			$val = $this->asignarFilaResult($recusacion,'aptitud',$val, 6);
    			$val = $this->asignarFilaResult($recusacion,'psicologica',$val, 7);
    			$val = $this->asignarFilaResult($recusacion,'responsable',$val, 8);
    			$val = $this->asignarFilaResult($recusacion,'resultado',$val, 9);
    			$val = $this->asignarFilaResult($recusacion,'notaOral',$val, 10);
    
    			return new JsonResponse($val);
    		}
    	}
    	else
    		throw $this->createNotFoundException('Error al devolver datos');
    }
    
    private function asignarFilaResult($object,$entidad,$val, $pos)
    {
    	$i = 0;
    	foreach($object as $value)
    	{
    		switch ($pos) {
    			case 1: $val[$entidad][$i] = $value->getCedulaAspirante(); break;
    			case 2: $val[$entidad][$i] = $value->getIdConcurso(); break;
    			case 3: $val[$entidad][$i] = $value->getNroPrueba(); break;
    			case 4: $val[$entidad][$i] = $value->getNota(); break;
    			case 5: $val[$entidad][$i] = $value->getNotaEscrito(); break;
    			case 6: $val[$entidad][$i] = $value->getAptitud(); break;
    			case 7: $val[$entidad][$i] = $value->getPsicologica(); break;
    			case 8: $val[$entidad][$i] = $value->getResponsable(); break;
    			case 9: $val[$entidad][$i] = $value->getResultado(); break;
    			case 10: $val[$entidad][$i] = $value->getNotaOral(); break;
    
    			default: $val[$entidad][$i] = $value->getId(); break;
    		}
    		$i++;
    	}
    	return $val;
    }
    
    /**
     * @Route("/concursoOposicion/buscarUsuarioAjax", name="buscarUsuarioAjax")
     * @Method("POST")
     */
    public function buscarUsuarioAjaxAction(Request $request){
    
    	$val[][] = "";
    
    	if($request->isXmlHttpRequest())
    	{
    
    		$query = $this->getDoctrine()
    		->getManager()
    		->createQuery("SELECT u 
                   FROM AppBundle:Usuario u
                   WHERE u.id = :id")
                    ->setParameter(':id', $request->get("id"));
    
            $recusacion = $query->getResult();
    
            if (!$recusacion) {
               return new JsonResponse("No Data");
            } else {                                  
    
               return new JsonResponse($recusacion[0]->getPrimerNombre()." ".$recusacion[0]->getPrimerApellido());
            }
    	}
    	else
    		throw $this->createNotFoundException('Error al devolver datos');
    }
}
