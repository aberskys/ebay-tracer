<?php

namespace ApiBundle\Controller;

use ApiBundle\Controller\Traits\AuthTrait;
use ApiBundle\Controller\Traits\DoctrineTrait;
use ApiBundle\Entity\Item;
use ApiBundle\Form\ItemType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ItemController extends FOSRestController
{
    use DoctrineTrait, AuthTrait;

    /**
     * @Rest\Get("/itemslist")
     * @param Request $request
     * @return View
     */
    public function getItemsList(Request $request): View
    {
        return $this->authorize($request->query->get('token')) ?? new View($this->get('repo.item')->getItems(), Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/itemslist/seller/{id}")
     * @param int $id
     * @param Request $request
     * @return View
     */
    public function getSellerItemsList(int $id, Request $request): View
    {
        return $this->authorize($request->query->get('token')) ?? new View($this->get('repo.item')->getSellerItems($id), Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/view/{id}")
     * @param int $id
     * @param Request $request
     * @return View
     */
    public function getItemAction(int $id, Request $request): View
    {
        $authorization = $this->authorize($request->query->get('token'));
        if ($authorization) {
            return $authorization;
        }

        $item = $this->repo(Item::class)->find($id);

        if ($item) {
            return new View($item, Response::HTTP_OK);
        }

        return new View('Item does not exist', Response::HTTP_NOT_FOUND);
    }

    /**
     * @Rest\Post("/items/new")
     * @param Request $request
     * @return View
     */
    public function newItemAction(Request $request): View
    {
        $authorization = $this->authorize($request->query->get('token'));
        if ($authorization) {
            return $authorization;
        }

        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->submit($request->query->all());

        if ($form->isValid()) {
            if (!$request->get('dry-run')) {
                $this->persist($form->getData())->flush();
            }

            return new View('Item successfully created!', Response::HTTP_OK);
        }

        return new View($form->getErrors(true), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Post("/items/edit/{id}")
     * @param int $id
     * @param Request $request
     * @return View
     */
    public function editItemAction(int $id, Request $request): View
    {
        $authorization = $this->authorize($request->query->get('token'));
        if ($authorization) {
            return $authorization;
        }

        $item = $this->repo(Item::class)->find($id);
        if (!$item) {
            return new View('Item does not exist', Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ItemType::class, $item);
        $form->submit($request->query->all(), false);

        if ($form->isValid()) {
            if (!$request->get('dry-run')) {
                $this->flush();
            }

            return new View('Item successfully updated', Response::HTTP_OK);
        }

        return new View($form->getErrors(true), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Get("/items/delete/{id}")
     * @param int $id
     * @param Request $request
     * @return View
     */
    public function removeItemAction(int $id, Request $request): View
    {
        $authorization = $this->authorize($request->query->get('token'));
        if ($authorization) {
            return $authorization;
        }

        $item = $this->repo(Item::class)->find($id);
        if ($item) {
            if (!$request->get('dry-run')) {
                $this->remove($item)->flush();
            }

            return new View('Item successfully removed', Response::HTTP_OK);
        }

        return new View('Item does not exist', Response::HTTP_NOT_FOUND);
    }
}
