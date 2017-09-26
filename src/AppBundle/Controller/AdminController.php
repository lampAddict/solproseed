<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @Route("/reports", name="reports")
     */
    public function reportsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em
            ->getRepository('AppBundle:User')
            ->createQueryBuilder('u')
            ->getQuery()
            ->getResult();

        $_users = [];
        foreach ($users as $user){
            /* @var $user \AppBundle\Entity\User */
            $_users[] = [
                'id'=>$user->getId()
                ,'login'=>$user->getEmail()
                ,'name'=>$user->getUsername()
                ,'roles'=>$user->getRoles()
                ,'active'=>($user->isEnabled()?1:0)
            ];
        }

        return $this->render('admin/reports.html.twig', [
            'users'=>$_users
        ]);
    }

    /**
     * @Route("/prepareReports", name="prepareReports")
     */
    public function prepareReportsAction(Request $request)
    {
        $period = $request->request->get('period');
        $uids = $request->request->get('uids');
        if(
               !empty( $uids )
            && $period != ''
        ){

        }

        return new JsonResponse(['response'=>true]);
    }
}