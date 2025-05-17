<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// clase
use App\Entity\Sorteo;
use App\Repository\SorteoRepository;
use Symfony\Component\Validator\Constraints\DateTime;



#[Route('/sorteo')]
class SorteoController extends AbstractController
{
    #[Route('/check', name: 'sorteo_check')]
    public function check(  SorteoRepository $em, Request $request )
    {
         
        $fecha = new \DateTime('now');
        $fechas = array();
        $fechas_key = array();  
        for($i = 0; $i < 30; $i++ )
        {
            $fechas[] = $fecha->format('d-m-Y');
            $key[] = $i;   
            $fecha->modify('-1 day');
        } 
        
        $choices = array_combine($fechas, $key);
        $choices_inverse = array_combine($key, $fechas);
        
        $form = $this->createFormBuilder()
        ->add('numero', TextType::class)
        // Validacion moneda
        ->add('fecha', ChoiceType::class, [ 'choices' => $choices, 'expanded' => false  ])
        ->add('Send', SubmitType::class)
        ->getForm();
         
         
              
        $form->handleRequest( $request );

         if ($form->isSubmitted() && $form->isValid()) {
            // array con valores form field => value
            $data = $form->getData();
            
            $fecha = \DateTime::createFromFormat('j-m-Y', $choices_inverse[$data['fecha']]);            
           
            
            $sorteo = $em->findOneByFecha( $fecha);
            
          

            if( ! isset( $sorteo ))
                return $this->render('sorteo/msg.html.twig', [
                    'msg' => "No se ha podido comprobar el resultado del sorteo",
                    'enlace' => 'sorteo_check',
                    
                ]);
            elseif(  $data['numero']  ==  $sorteo->getNumero() )
                $premiado = true;
            else 
                $premiado = false;
       
            
            return $this->render('sorteo/resultado.html.twig', [
                    'premiado' => $premiado,
                    'numero' => $data['numero'],
                    'premio' => $sorteo->getPremio(),
                    'numero_premiado' =>  $sorteo->getNumero(),
                    'fecha' =>  $sorteo->getFecha(),
                ]);
        }
        else
            return $this->render('sorteo/form.html.twig', array('form' => $form->createView(),));
    }
}
