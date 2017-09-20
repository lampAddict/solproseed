<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{
    /**
     * @Route("/", name="mainpage")
     */
    public function indexAction(Request $request)
    {

        //Check if user authenticated
        if( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        //admin stuff
        if( $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') ){

            $cDate = new \DateTime();
            $seedData = $em
                ->getRepository('AppBundle:SeedData')
                ->createQueryBuilder('sd')
                ->where('sd.updated_at <= :cdata')
                ->setParameter('cdata', $cDate, \Doctrine\DBAL\Types\Type::DATETIME)
                ->orderBy('sd.updated_at', 'DESC')
                ->getQuery()
                ->setMaxResults( 1 )
                ->getResult();

            $seed_data = null;
            $revenue = 0;
            if( !empty($seedData) ){
                /** @var $seed_data \AppBundle\Entity\SeedData */
                $seed_data = $seedData[0];
                $revenue = (($seed_data->getOilPrice() - 15)*$seed_data->getUsdrub() - 2000)*$seed_data->getOilYield() + ($seed_data->getOilmealPrice()*$seed_data->getUsdrub() - 2000)*$seed_data->getOilmealYield();
            }

            $form = $this->createForm(
                'AppBundle\Form\SeedDataType'
                ,$seed_data
                ,[
                    'action' => $this->generateUrl('seeddata_new'),
                    'method' => 'POST',
            ]);

            return $this->render('seeddata/new.html.twig', [
                'form' => $form->createView(),
                'revenue' => $revenue
            ]);

        }
        //manager
        else if( $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') ){
            $form = $this->createForm('AppBundle\Form\DealType');
            return $this->render('deal/new.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        // replace this example code with whatever you need
        return $this->render('index/index.html.twig', [
            'param' => 'param',
        ]);
    }
}
