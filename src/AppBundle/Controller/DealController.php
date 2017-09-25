<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Deal;
use AppBundle\Entity\SeedData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Deal controller.
 *
 * @Route("deal")
 */
class DealController extends Controller
{
    /**
     * Lists all deal entities.
     *
     * @Route("/", name="deal_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $data = $this->getSeedData();

        $form = $this->createForm(
            'AppBundle\Form\DealType'
            ,null
            ,[
            'action' => $this->generateUrl('deal_new')
            ,'method' => 'POST'
        ]);

        $omegaNumerator = $this->calcRevenue($data['seedData']);

        return $this->render('deal/new.html.twig', [
            'form' => $form->createView(),
            'dealNumber' => $data['dealNumber'],
            'seedData' => $data['seedData'],
            'alphaNumerator' => $this->calcAlphaNumerator($data['seedData']),
            'omegaNumeratorOil' => $omegaNumerator['oilPrice$'],
            'omegaNumeratorOilMeal' => $omegaNumerator['oilMealPrice$']
        ]);
    }

    /**
     * Creates a new deal entity.
     *
     * @Route("/new", name="deal_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $deal = new Deal();
        $form = $this->createForm('AppBundle\Form\DealType', $deal);
        $form->handleRequest($request);

        if(
               $form->isSubmitted()
            && $form->isValid()
        ){
            $em = $this->getDoctrine()->getManager();

            $data = $request->request->get('appbundle_deal');

            /* @var $deal \AppBundle\Entity\Deal */
            $deal->setUid( $this->getUser() );
            $deal->setUpdatedAt( new \DateTime() );
            $deal->setSeedPrice( $data['seed_price'] );
            $deal->setDeliveryPrice( $data['delivery_price'] );
            $deal->setShipmentPrice( $data['shipment_price'] );
            $deal->setStoragePrice( $data['storage_price'] );
            $deal->setOilContent( $data['oil_content'] );
            $deal->setDealDone( true );

            $em->persist($deal);
            $em->flush();

            return $this->redirectToRoute('mainpage');
        }

        return $this->render('deal/new.html.twig', array(
            'deal' => $deal,
            'form' => $form->createView(),
        ));
    }

    /**
     * Show users closed deals.
     *
     * @Route("/list", name="deals")
     * @Method("GET")
     */
    public function listDealsAction()
    {
        $em = $this->getDoctrine()->getManager();

        //if user is admin get data about all deals stored in db
        if( $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') ){
            $deals = $em
                ->getRepository('AppBundle:Deal')
                ->createQueryBuilder('d')
                ->leftJoin('AppBundle:User', 'u', 'WITH', 'u.id = d.uid')
                ->orderBy('d.updated_at', 'DESC')
                ->getQuery()
                ->getResult();
        }
        //if user is manager get data only about his deals
        elseif( $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') ){
            $deals = $em
                ->getRepository('AppBundle:Deal')
                ->createQueryBuilder('d')
                ->where('d.uid = :uid')
                ->setParameter('uid', $this->getUser()->getId())
                ->orderBy('d.updated_at', 'DESC')
                ->getQuery()
                ->getResult();
        }

        return $this->render('deal/index.html.twig', array(
            'deals' => $deals
        ));
    }

    /**
     * Finds and displays a deal entity.
     *
     * @Route("/{id}", name="deal_show")
     * @Method("GET")
     */
    public function showAction(Deal $deal)
    {
        return $this->redirectToRoute('deals');
//        $deleteForm = $this->createDeleteForm($deal);
//
//        return $this->render('deal/show.html.twig', array(
//            'deal' => $deal,
//            'delete_form' => $deleteForm->createView(),
//        ));
    }

    /**
     * Displays a form to edit an existing deal entity.
     *
     * @Route("/{id}/edit", name="deal_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Deal $deal)
    {
        $editForm = $this->createForm('AppBundle\Form\DealType', $deal);
        $editForm->handleRequest($request);

        if(
               $editForm->isSubmitted()
            && $editForm->isValid()
        ){
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('deals');
        }

        $data = $this->getSeedData();

        $omegaNumerator = $this->calcRevenue($data['seedData']);

        return $this->render('deal/new.html.twig', array(
            'deal' => $deal,
            'form' => $editForm->createView(),
            'dealNumber' => $deal->getId(),
            'seedData' => $data['seedData'],
            'alphaNumerator' => $this->calcAlphaNumerator($data['seedData']),
            'omegaNumeratorOil' => $omegaNumerator['oilPrice$'],
            'omegaNumeratorOilMeal' => $omegaNumerator['oilMealPrice$']
        ));
    }

    /**
     * Deletes a deal entity.
     *
     * @Route("/{id}", name="deal_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Deal $deal)
    {
        return $this->redirectToRoute('deals');
//        $form = $this->createDeleteForm($deal);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($deal);
//            $em->flush();
//        }
//
//        return $this->redirectToRoute('deal_index');
    }

    /**
     * Creates a form to delete a deal entity.
     *
     * @param Deal $deal The deal entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Deal $deal)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('deal_delete', array('id' => $deal->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function calcAlphaNumerator($seed_data){
        /** @var $seed_data \AppBundle\Entity\SeedData */
        return (intval($seed_data->getOilPrice()) - 15)*intval($seed_data->getUsdrub()) - 2000 + intval($seed_data->getOilmealPrice())*intval($seed_data->getUsdrub()) - 2000;
        //((B12-15)*$B$16-2000)+(B13*$B$16-2000)
    }

    private function calcRevenue($seed_data){
        /** @var $seed_data \AppBundle\Entity\SeedData */
        return ['oilPrice$'=>((intval($seed_data->getOilPrice()) - 15)*intval($seed_data->getUsdrub()) - 2000), 'oilMealPrice$'=>(intval($seed_data->getOilmealPrice())*intval($seed_data->getUsdrub()) - 2000)];
    }

    private function getSeedData(){
        $em = $this->getDoctrine()->getManager();

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

        if( empty($seedData) ){
            $seedData = [ new SeedData() ];
        }

        return ['dealNumber'=>$dealNumber, 'seedData'=>$seedData[0]];
    }
}
