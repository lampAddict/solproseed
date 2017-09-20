<?php

namespace AppBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class UserController extends Controller
{
    /**
     * Checks if user allowed to do things
     */
    private function checkUserAuth(){
        //Check if user authenticated
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        //Check user's role
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
    }

    /**
     * Show the user.
     *
     * @Route("/users/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction($id)
    {
        $this->checkUserAuth();

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('Такого пользователя не существует.');
        }

        return $this->render('@FOSUser/Profile/show.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Edit the user.
     *
     * @param Request $request
     * @param Integer $id
     *
     * @Route("/users/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     *
     * @return Response
     */
    public function editAction(Request $request, $id = null)
    {
        $this->checkUserAuth();

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);

        if(
               !is_object($user)
            || !$user instanceof UserInterface
        ){
            throw new AccessDeniedException('Такого пользователя не существует.');
        }

        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->setData($user);

        $form->handleRequest($request);

        if(
               $form->isSubmitted()
            && $form->isValid()
        ){
            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $userManager->updateUser($user);

            $url = $this->generateUrl('users');
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('user/edit.html.twig', array(
            'form' => $form->createView(),
            'id' => $id
        ));
    }

    /**
     * Show users list
     *
     * @Route("/users", name="users")
     * @Method("GET")
     */
    public function usersAction(Request $request)
    {

        $this->checkUserAuth();

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

        //$_roles = $this->container->getParameter('security.role_hierarchy.roles');

        return $this->render('user/users.html.twig', array(
             'users' => $_users
            ,'roles' => ['ROLE_ADMIN']
            ,'captionRoles' => ['ROLE_SUPER_ADMIN'=>'Администратор', 'ROLE_ADMIN'=>'Менеджер', 'ROLE_USER'=>'Пользователь', ''=>'']
        ));
    }

    /**
     * Set/unset user roles.
     *
     * @Route("/setUserRole", name="setUserRole")
     * @Method("POST")
     */
    public function setUserRoleAction(Request $request){

        $this->checkUserAuth();

        $em = $this->getDoctrine()->getManager();

        $user = $em
            ->getRepository('AppBundle:User')
            ->createQueryBuilder('u')
            ->where('u.id = :uid')
            ->setParameter('uid', intval($request->request->get('uid')))
            ->getQuery()
            ->getResult();

        $err = '';

        if( !empty($user) ){
            $user = $user[0];
            $addRole = $request->request->get('addRole');
            $removeRole = $request->request->get('removeRole');
            /* @var $user \AppBundle\Entity\User */
            $updateUser = false;

            $roles = [ 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN' ];
            if(
                $addRole != ''
                && in_array($addRole, $roles)
            ){
                if( !$user->hasRole($addRole) ){
                    $user->addRole($addRole);
                    $updateUser = true;
                }
                else{
                    $err .= "Пользователю уже назначена эта роль.\n";
                }
            }

            if(
                $removeRole != ''
                && in_array($removeRole, $roles)
            ){
                if( $user->hasRole($removeRole) ){
                    $user->removeRole($removeRole);
                    $updateUser = true;
                }
                else{
                    $err .= "У пользователя нет запрашиваемой на удаление роли.\n";
                }
            }

            if( $updateUser ){
                $userManager = $this->container->get('fos_user.user_manager');
                $userManager->updateUser($user);
                $em->flush();

                return new JsonResponse(['result'=>true, 'msg'=>$err]);
            }

        }
        else{
            return new JsonResponse(['result'=>false, 'msg'=>'Пользователь не найден.']);
        }

        return new JsonResponse(['result'=>false, 'msg'=>$err]);
    }

    /**
     * Block/unblock user profile depends on its current state
     *
     * @Route("/setUserBlock", name="setUserBlock")
     * @Method("POST")
     */
    public function setUserBlockAction(Request $request){

        $this->checkUserAuth();

        $em = $this->getDoctrine()->getManager();

        $user = $em
            ->getRepository('AppBundle:User')
            ->createQueryBuilder('u')
            ->where('u.id = :uid')
            ->setParameter('uid', intval($request->request->get('uid')))
            ->getQuery()
            ->getResult();

        if( !empty($user) ){
            /* @var $user \AppBundle\Entity\User */
            $user = $user[0];
            if( $user->hasRole('ROLE_SUPER_ADMIN') === false ){
                $user->setEnabled(!$user->isEnabled());
                $em->flush();

                return new JsonResponse(['result'=>true]);
            }
        }

        return new JsonResponse(['result'=>false]);
    }

}
