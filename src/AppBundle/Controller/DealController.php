<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Deal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

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
        $em = $this->getDoctrine()->getManager();

        $deals = $em->getRepository('AppBundle:Deal')->findAll();

        return $this->render('deal/index.html.twig', array(
            'deals' => $deals,
        ));
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
     * Finds and displays a deal entity.
     *
     * @Route("/{id}", name="deal_show")
     * @Method("GET")
     */
    public function showAction(Deal $deal)
    {
        $deleteForm = $this->createDeleteForm($deal);

        return $this->render('deal/show.html.twig', array(
            'deal' => $deal,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing deal entity.
     *
     * @Route("/{id}/edit", name="deal_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Deal $deal)
    {
        $deleteForm = $this->createDeleteForm($deal);
        $editForm = $this->createForm('AppBundle\Form\DealType', $deal);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('deal_edit', array('id' => $deal->getId()));
        }

        return $this->render('deal/edit.html.twig', array(
            'deal' => $deal,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
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
        $form = $this->createDeleteForm($deal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($deal);
            $em->flush();
        }

        return $this->redirectToRoute('deal_index');
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
}
