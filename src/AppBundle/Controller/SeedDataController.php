<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SeedData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Seeddatum controller.
 *
 * @Route("seeddata")
 */
class SeedDataController extends Controller
{
    /**
     * Lists all seedDatum entities.
     *
     * @Route("/", name="seeddata_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $seedDatas = $em->getRepository('AppBundle\Entity\SeedData')->findAll();

        return $this->render('seeddata/index.html.twig', array(
            'seedDatas' => $seedDatas,
        ));
    }

    /**
     * Creates a new seedDatum entity.
     *
     * @Route("/new", name="seeddata_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $seedDatum = new SeedData();
        $form = $this->createForm('AppBundle\Form\SeedDataType', $seedDatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($seedDatum);
            $em->flush();

            return $this->redirectToRoute('seeddata_show', array('id' => $seedDatum->getId()));
        }

        return $this->render('seeddata/new.html.twig', array(
            'seedDatum' => $seedDatum,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a seedDatum entity.
     *
     * @Route("/{id}", name="seeddata_show")
     * @Method("GET")
     */
    public function showAction(SeedData $seedDatum)
    {
        $deleteForm = $this->createDeleteForm($seedDatum);

        return $this->render('seeddata/show.html.twig', array(
            'seedDatum' => $seedDatum,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing seedDatum entity.
     *
     * @Route("/{id}/edit", name="seeddata_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, SeedData $seedDatum)
    {
        $deleteForm = $this->createDeleteForm($seedDatum);
        $editForm = $this->createForm('AppBundle\Form\SeedDataType', $seedDatum);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('seeddata_edit', array('id' => $seedDatum->getId()));
        }

        return $this->render('seeddata/edit.html.twig', array(
            'seedDatum' => $seedDatum,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a seedDatum entity.
     *
     * @Route("/{id}", name="seeddata_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, SeedData $seedDatum)
    {
        $form = $this->createDeleteForm($seedDatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($seedDatum);
            $em->flush();
        }

        return $this->redirectToRoute('seeddata_index');
    }

    /**
     * Creates a form to delete a seedDatum entity.
     *
     * @param SeedData $seedDatum The seedDatum entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SeedData $seedDatum)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('seeddata_delete', array('id' => $seedDatum->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
