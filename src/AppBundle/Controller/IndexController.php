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

                $revenue =  $this->calcRevenue($seed_data);
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

            $sql = 'SELECT COUNT(*) as num from deal';
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();
            $deals = $stmt->fetchAll();

            $dealNumber = 0;
            if( !empty($deals) ){
                $dealNumber = intval($deals[0]['num']) + 1;
            }

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

            $form = $this->createForm(
                     'AppBundle\Form\DealType'
                    ,null
                    ,[
                         'action' => $this->generateUrl('deal_new')
                        ,'method' => 'POST'
            ]);

            if( !empty($seedData) )
                return $this->render('deal/new.html.twig', [
                    'form' => $form->createView(),
                    'dealNumber' => $dealNumber,
                    'seedData' => $seedData[0],
                    'alphaNumerator' => $this->calcAlphaNumerator($seedData[0]),
                    'omegaNumerator' => $this->calcRevenue($seedData[0])
                ]);
        }

        // replace this example code with whatever you need
        return $this->render('index/index.html.twig', [
        ]);
    }

    private function calcAlphaNumerator($seed_data){
        /** @var $seed_data \AppBundle\Entity\SeedData */
        return (intval($seed_data->getOilPrice()) - 15)*intval($seed_data->getUsdrub()) - 2000 + intval($seed_data->getOilmealPrice())*intval($seed_data->getUsdrub()) - 2000;
        //((B12-15)*$B$16-2000)+(B13*$B$16-2000)
    }

    private function calcRevenue($seed_data){
        /** @var $seed_data \AppBundle\Entity\SeedData */
        return (((intval($seed_data->getOilPrice()) - 15)*intval($seed_data->getUsdrub()) - 2000)*floatval($seed_data->getOilYield()) + (intval($seed_data->getOilmealPrice())*intval($seed_data->getUsdrub()) - 2000)*floatval($seed_data->getOilmealYield()))/100;
    }
}
