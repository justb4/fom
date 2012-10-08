<?php

namespace FOM\UserBundle\Controller;

use FOM\UserBundle\Entity\Role;
use FOM\UserBundle\Form\Type\RoleType;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOM\ManagerBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Role management controller
 *
 * TODO: Role Repository
 *
 * @author Christian Wygoda
 */
class RoleController extends Controller {
    /**
     * Renders role list.
     *
     * @Route("/role")
     * @Method({ "GET" })
     * @Template
     */
    public function indexAction() {
        $roles = $this->getDoctrine()->getRepository('FOMUserBundle:Role')
            ->findAll();

        return array(
            'roles' => $roles);
    }

    /**
     * @Route("/role/new")
     * @Method({ "GET" })
     * @Template
     */
    public function newAction() {
        $role = new Role();
        $form = $this->createForm(new RoleType(), $role);

        return array(
            'role' => $role,
            'form' => $form->createView(),
            'form_name' => $form->getName());
    }

    /**
     * @Route("/role")
     * @Method({ "POST" })
     * @Template("MapbenderManagerBundle:Role:new.html.twig")
     */
    public function createAction() {
        $role = new Role();
        $form = $this->createForm(new RoleType(), $role);

        $form->bindRequest($this->get('request'));

        if($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($role);
            $em->flush();

            $this->get('session')->setFlash('success',
                'The role has been saved.');

            return $this->redirect(
                $this->generateUrl('fom_user_role_index'));
        }

        return array(
            'role' => $role,
            'form' => $form->createView());
    }

    /**
     * @Route("/role/{id}/edit")
     * @Method({ "GET" })
     * @Template
     */
    public function editAction($id) {
        $role = $this->getDoctrine()->getRepository('FOMUserBundle:Role')
            ->find($id);
        if($role === null) {
            throw new NotFoundHttpException('The role does not exist');
        }

        $form = $this->createForm(new RoleType(), $role);

        return array(
            'role' => $role,
            'form' => $form->createView(),
            'form_name' => $form->getName());
    }

    /**
     * @Route("/role/{id}/update")
     * @Method({ "POST" })
     * @Template("MapbenderManagerBundle:Role:edit.html.twig")
     */
    public function updateAction($id) {
        $role = $this->getDoctrine()->getRepository('FOMUserBundle:Role')
            ->find($id);
        if($role === null) {
            throw new NotFoundHttpException('The role does not exist');
        }

        $form = $this->createForm(new RoleType(), $role);
        $form->bindRequest($this->get('request'));

        if($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->flush();

            $this->get('session')->setFlash('success',
                'The role has been updated.');

            return $this->redirect(
                $this->generateUrl('fom_user_role_index'));

        }

        return array(
            'role' => $role,
            'form' => $form->createView());
    }

    /**
     * @Route("/role/{id}/delete")
     * @Method({ "GET" })
     * @Template("FOMUserBundle:Role:delete.html.twig")
     */
    public function confirmDeleteAction($id) {
        $role = $this->getDoctrine()->getRepository('FOMUserBundle:Role')
            ->find($id);
        if($role === null) {
            throw new NotFoundHttpException('The role does not exist');
        }

        $form = $this->createDeleteForm($id);

        return array(
            'role' => $role,
            'form' => $form->createView());
    }

    /**
     * @Route("/role/{id}/delete")
     * @Method({ "POST" })
     * @Template
     */
    public function deleteAction($id) {
        $role = $this->getDoctrine()->getRepository('FOMUserBundle:Role')
            ->find($id);

        if($role === null) {
            throw new NotFoundHttpException('The role does not exist');
        }

        if($role->getOverride() == 'IS_AUTHENTICATED_FULLY') {
            throw new NotFoundHttpException('The role IS_AUTHENTICATED_FULLY can not be deleted');
        }

        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);
        if($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($role);
            $em->flush();

            $this->get('session')->setFlash('success',
                'The role has been deleted.');
        } else {
            $this->get('session')->setFlash('error',
                'The role couldn\'t be deleted.');
        }
        return $this->redirect(
            $this->generateUrl('fom_user_role_index'));
    }

    /**
     * Creates the form for the confirm delete page.
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
}

