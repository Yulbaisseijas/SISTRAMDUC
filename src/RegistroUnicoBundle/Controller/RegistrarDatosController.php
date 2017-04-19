<?php
 
namespace RegistroUnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use RegistroUnicoBundle\Entity\Estatus;
use RegistroUnicoBundle\Entity\Nivel;
//use RegistroUnicoBundle\Entity\Cargo;
use AppBundle\Entity\Usuario;
use AppBundle\Entity\Rol;

class RegistrarDatosController extends Controller
{
    public function registrarDatosUsuarioAction()
    {
        return $this->render('RegistroUnicoBundle:RegistrarDatos:registrar_datos.html.twig');
    }
    
    public function guardarDatosAjaxAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $this->registerUser($request->get('personalData'));
            $this->registerCargos($request->get('cargrgata') $,,request->get('personalData')[15]);
            ////
            /*$request->get('hijrgata') 
            $request->get('indHijoData') 
            $request->get('registrosData') 
            $request->get('indRegistrosData') 
            $request->get('participantesData') 
            $request->get('indParticipantesData') 
            $request->get('revistasData') 
            $request->get('indRevistasData')*/
            return new JsonResponse("if");
        }
        else
            return new JsonResponse("else");
    }
    
    public function enviarEmailsAjaxAction(Request $request)
    {
        return new JsonResponse($this->getEmails($this->getAll("AppBundle:","Usuario")));
    }   
    
    public function buscarEmailAjaxAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $encontrado = $this->getOneEmail("AppBundle:","Usuario",$request->get("Email"));

            if (!$encontrado) {
                return new JsonResponse(0);
            }else
                return new JsonResponse($encontrado->getActivo());
        }
        else
             throw $this->createNotFoundException('Error al solicitar datos');
    }
    
    public function enviarDataAjaxAction(Request $request)
    {
        $val[][] = "";
        if($request->isXmlHttpRequest())
        {
            $estatus = $this->getAll("RegistroUnicoBundle:","Estatus");
            $nivel = $this->getAll("RegistroUnicoBundle:","Nivel");
            $tipo_regitro = $this->getAll("RegistroUnicoBundle:","TipoRegistro");
            $cargo = $this->getAll("RegistroUnicoBundle:","Cargo");
            
            $rol = $this->getAll("AppBundle:","Rol");

            if (!$estatus || !$nivel || !$tipo_regitro || !$cargo || !$rol) {
                 throw $this->createNotFoundException('Error al obtener datos iniciales');
            }else
            {
                $val = $this->bdToArrayDescription($estatus,'estatus',$val);
                $val = $this->bdToArrayDescription($nivel,'nivel',$val);
                $val = $this->bdToArrayDescription($tipo_regitro,'tipo_registro',$val);
                $val = $this->bdToArrayDescription($cargo,'cargo',$val);
                $val = $this->bdToArrayNombre($rol,'rol',$val);
                return new JsonResponse($val);
            }
        }
        else
             throw $this->createNotFoundException('Error al solicitar datos');
    }

    public function obtenerIdAjaxAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $value = $this->getDoctrine()
                          ->getManager()
                          ->createQuery('SELECT MAX(r.id) AS lastId FROM RegistroUnicoBundle:Registro r')
                          ->getResult();
            return new JsonResponse($value);
        }
        else
             throw $this->createNotFoundException('Error al obtener los datos');
    }
    
    private function getEmails($object)
    {
        $i = 0;
        $datas=null;
        $data["Email"]="";
        $data["Estatus"]="";
        foreach($object as $value)
        {
           $data["Email"] = $value->getCorreo();
           if($value->getActivo())
               $data["Estatus"]="Activo";
           else
               $data["Estatus"]="Inactivo";
           $datas[$i] = $data;
           $i++;
        }
        
        return array(
            "draw"            => 1,
	        "recordsTotal"    => $i,
	        "recordsFiltered" => $i,
	        "data"            => $datas
        );
    }
    
    private function bdToArrayDescription($object,$entidad,$val)
    {
        $i = 0;
        foreach($object as $value)
        {
           $val[$entidad][$i] = $value->getDescription();
           $i++;
        }
        return $val;
    }
    
    private function bdToArrayNombre($object,$entidad,$val)
    {
        $i = 0;
        foreach($object as $value)
        {
           $val[$entidad][$i] = $value->getNombre();
           $i++;
        }
        return $val;
    }
    
    private function getOneEmail($bundle,$entidad,$email)
    {
        return $this->getDoctrine()
                    ->getManager()
                    ->getRepository($bundle.$entidad)
                    ->findOneByCorreo($email);
    }
    
    private function getAll($bundle,$entidad)
    {
        return $this->getDoctrine()
                    ->getManager()
                    ->getRepository($bundle.$entidad)
                    ->findAll();
    }
    
    private function getRolName($bundle,$entidad,$id)
    {
        return $this->getDoctrine()
                    ->getManager()
                    ->getRepository($bundle.$entidad)
                    ->findOneById($id);
    }
    
    private function registerUser($user)
    {
        $em = $this->getDoctrine()->getManager();
        $newUser = $em->getRepository('AppBundle:Usuario')
                      ->findOneByCorreo($user[15]);
        
        if (!$newUser) {
            throw $this->createNotFoundException(
                'Usuario no encontrado por el correo '.$user[15]
            );
        }
        
        $newUser->setPrimerNombre($user[0]);
        $newUser->setSegundoNombre($user[1]);
        $newUser->setPrimerApellido($user[2]);
        $newUser->setSegundoApellido($user[3]);
        $newUser->setNacionalidad($user[4]);
        $newUser->setFechaNacimiento(new \DateTime($user[5]));
        $newUser->setEdad($user[6]);
        $newUser->setSexo($user[7]);
        $newUser->setRif('J-'.$user[8]);
        $newUser->setTelefono($user[9].'-'.$user[10]);
        $newUser->setDireccion($user[14]);
        
        $em->flush();
    }
    
    private function registerCargos($cagos,$email)
    {
        $em = $this->getDoctrine()->getManager();
        $newUser = $em->getRepository('AppBundle:Usuario')
                      ->findOneByCorreo($email);
    
        if (!$newUser) {
            throw $this->createNotFoundException(
                'Usuario no encontrado por el correo '.$email
            );
        }
        
        $newUser->addCargos($cagos);
        $em->flush();
    }
}