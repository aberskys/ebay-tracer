<?php

namespace ApiBundle\Controller;

use ApiBundle\Controller\Traits\AuthTrait;
use ApiBundle\Controller\Traits\DoctrineTrait;
use ApiBundle\Entity\User;
use ApiBundle\Form\UserType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends FOSRestController
{
    use DoctrineTrait, AuthTrait;

    /**
     * @Rest\Get("/userslist")
     * @param Request $request
     * @return View
     */
    public function getUsersListAction(Request $request): View
    {
        return $this->authorize($request->get('token')) ?? new View($this->get('repo.user')->getUsers(), Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/sellerslist")
     * @param Request $request
     * @return View
     */
    public function getSellersListAction(Request $request): View
    {
        return $this->authorize($request->get('token')) ?? new View($this->get('repo.user')->getSellers(), Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/user/{id}")
     * @param int $id
     * @param Request $request
     * @return View
     */
    public function getUserAction(int $id, Request $request): View
    {
        $authorization = $this->authorize($request->get('token'));
        if ($authorization) {
            return $authorization;
        }

        $user = $this->get('repo.user')->getUserById($id);

        if (!$user) {
            return new View('User does not exist', Response::HTTP_NOT_FOUND);
        }

        return new View($user, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/delete/{id}")
     * @param int $id
     * @param Request $request
     * @return View
     */
    public function removeUserAction(int $id, Request $request): View
    {
        $authorization = $this->authorize($request->get('token'));
        if ($authorization) {
            return $authorization;
        }

        $user = $this->get('repo.user')->getUserById($id);

        if (!$user) {
            return new View('User does not exist', Response::HTTP_NOT_FOUND);
        }

        if (!$request->get('dry-run')) {
            $this->remove($user)->flush();
        }

        return new View(sprintf('User was deleted (id:%d)', $id), Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/new/user")
     * @param Request $request
     * @return View
     */
    public function newUserAction(Request $request): View
    {
        $authorization = $this->authorize($request->get('token'));
        if ($authorization) {
            return $authorization;
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->query->all());

        if ($form->isValid()){
            $this->encodePassword($user);
            if (!$request->get('dry-run')) {
                $this->persist($form->getData())->flush();
            }

            return new View('User successfully created', Response::HTTP_OK);
        }

        return new View($form->getErrors(true), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Post("/new/seller")
     * @param Request $request
     * @return View
     */
    public function newSellerAction(Request $request): View
    {
        $authorization = $this->authorize($request->get('token'));
        if ($authorization) {
            return $authorization;
        }

        $seller = new User();
        $form = $this->createForm(UserType::class, $seller);
        $form->submit($request->query->all());

        if ($form->isValid()){
            $this->encodePassword($seller);
            $seller->setRole(User::ROLE_SELLER);

            if (!$request->query->get('dry-run')) {
                $this->persist($form->getData())->flush();
            }

            return new View('Seller successfully created!', Response::HTTP_OK);
        }

        return new View($form->getErrors(true), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Get("/check-seller/{sellerId}")
     * @param int $sellerId
     * @param Request $request
     * @return View
     */
    public function isSellerAction(int $sellerId, Request $request): View
    {
        $authorization = $this->authorize($request->get('token'));
        if ($authorization) {
            return $authorization;
        }

        $seller = $this->get('repo.user')->getUserById($sellerId);

        return $seller ? new View(($seller->isSeller() || $seller->isAdmin()), Response::HTTP_OK) : new View('Seller not found', Response::HTTP_NOT_FOUND);
    }

    /**
     * @param User $user
     */
    private function encodePassword(User $user)
    {
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        if (!empty($user->getPlainPassword())) {
            $user->setPassword($encoder->encodePassword($user->getPlainPassword(), null));
        }
    }
}
