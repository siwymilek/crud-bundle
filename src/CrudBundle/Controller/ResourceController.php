<?php

namespace Siwymilek\CrudBundle\Controller;

use AppBundle\Entity\Campaign\Campaign;
use AppBundle\Form\Campaign\CampaignType;
use Siwymilek\CrudBundle\Grid\GridInterface;
use Siwymilek\CrudBundle\Grid\Types\GridTypeInterface;
use Siwymilek\CrudBundle\Handler\ResourceInterface;
use Siwymilek\CrudBundle\Model\ModelInterface;
use Siwymilek\CrudBundle\Repository\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ResourceController
 * @package Siwymilek\CrudBundle\Controller
 */
class ResourceController extends Controller {

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request) {

        /** @var GridInterface $grid */
        $grid = $this->getGrid();

        /** @var ResourceInterface $resource */
        $resource = $grid->getResource();

        /** @var RepositoryInterface $repository */
        $repository = $grid->getResource()->getRepository();

        /** @var GridTypeInterface $gridType */
        $gridType = $this->getType();

        try {
            $this->checkVoters($gridType->getSecurity()['voters'], $resource->getModel());
        } catch (AccessDeniedException $e) {
            return $this->response([
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ]
            ], $e->getCode());
        }

        $paginationConfig = $gridType->getPagination();

        $paginationKey = $paginationConfig['key'] ?: 'page';
        $limit = $paginationConfig['limit'] ?: 10;

        $page = (int)$request->get($paginationKey, 1);
        $firstResult = ($page-1) * $limit;

        $repositoryMethod = $gridType->getRepositoryMethod();
        $repositoryMethodArguments = &$repositoryMethod['arguments'];
        $this->parseArgs($repositoryMethodArguments);

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = call_user_func_array([$repository, $repositoryMethod['name']], [$repositoryMethodArguments]);

        if($queryBuilder instanceof QueryBuilder) {
            $res = $queryBuilder
                ->orderBy($repository->getAlias().'.'.$paginationConfig['sorting']['sort'], $paginationConfig['sorting']['order']);

            if($paginationConfig['enabled']) {
                $res = $res->setMaxResults($limit)
                    ->setFirstResult($firstResult);
            }

            $res = $res
                ->getQuery()
                ->getResult()
            ;
        } else {
            $res = $queryBuilder;
        }

        return $this->response([
            Inflector::pluralize($resource->getName()) => $res
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request) {

        /** @var GridInterface $grid */
        $grid = $this->getGrid();

        /** @var ResourceInterface $resource */
        $resource = $grid->getResource();

        /** @var RepositoryInterface $repository */
        $repository = $grid->getResource()->getRepository();

        /** @var GridTypeInterface $gridType */
        $gridType = $this->getType();

        try {
            $this->checkVoters($gridType->getSecurity()['voters'], $resource->getModel());
        } catch (AccessDeniedException $e) {
            return $this->response([
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ]
            ], $e->getCode());
        }

        $repositoryMethod = $gridType->getRepositoryMethod();
        $repositoryMethodArguments = &$repositoryMethod['arguments'];
        $this->parseArgs($repositoryMethodArguments);

        /** @var QueryBuilder $queryBuilder */
        $result = call_user_func_array([$repository, $repositoryMethod['name']], [$repositoryMethodArguments]);

        if($request->isXmlHttpRequest()) {
//        if(true) {
            return $this->getAjaxResponse($this->serialize(['initialState' => [
                $resource->getName() => $result
            ]], $gridType->getSerialization()), 200);
        }


        return $this->render($grid->getTemplate(), [
            'initialState' => $this->serialize([
                $resource->getName() => $result
            ], $gridType->getSerialization())
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createAction(Request $request) {
        /** @var GridInterface $grid */
        $grid = $this->getGrid();

        /** @var ResourceInterface $resource */
        $resource = $grid->getResource();

        /** @var RepositoryInterface $repository */
        $repository = $grid->getResource()->getRepository();

        /** @var GridTypeInterface $gridType */
        $gridType = $this->getType();

        try {
            $this->checkVoters($gridType->getSecurity()['voters'], $resource->getModel());
        } catch (AccessDeniedException $e) {
            return $this->response([
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ]
            ], $e->getCode());
        }

        if($request->getMethod() === 'GET') {
            $form = $this->createForm($gridType->getForm(), new Campaign(), ['csrf_protection' => false]);
            $formSchema = $this->get('liform')->transform($form);

            if($request->isXmlHttpRequest()) {
//            if(true) {
                return $this->getAjaxResponse($this->serialize(['initialState' => [
                    Inflector::camelize( $resource->getName().'_form') => $formSchema
                ]], $gridType->getSerialization()), 200);
            }


            return $this->render($grid->getTemplate(), [
                'initialState' => $this->serialize([
                    Inflector::camelize( $resource->getName().'_form') => $formSchema
                ], $gridType->getSerialization())
            ]);
        } else {
            $formFactory = $this->get('form.factory');

            $entity = new Campaign();
            $form = $formFactory->create(CampaignType::class, $entity);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                //TODO: set user/owner condition
                $this->injectOwner($entity);

                $em = $this->get('doctrine.orm.default_entity_manager');
                $em->persist($entity);
                $em->flush();

                return $this->response([
                    $resource->getName() => $entity
                ], 201);
            }

            return $this->response([
                'errors' => $form->getErrors()
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateAction(Request $request) {
        /** @var GridInterface $grid */
        $grid = $this->getGrid();

        /** @var ResourceInterface $resource */
        $resource = $grid->getResource();

        /** @var RepositoryInterface $repository */
        $repository = $grid->getResource()->getRepository();

        /** @var GridTypeInterface $gridType */
        $gridType = $this->getType();

        $repositoryMethod = $gridType->getRepositoryMethod();
        $repositoryMethodArguments = &$repositoryMethod['arguments'];
        $this->parseArgs($repositoryMethodArguments);

        /** @var QueryBuilder $queryBuilder */
        $result = call_user_func_array([$repository, $repositoryMethod['name']], [$repositoryMethodArguments]);

        try {
            $this->checkVoters($gridType->getSecurity()['voters'], $result);
        } catch (AccessDeniedException $e) {
            return $this->response([
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ]
            ], $e->getCode());
        }

        if($request->getMethod() === 'GET') {
            $form = $this->createForm($gridType->getForm(), $result, ['csrf_protection' => false]);
            $formSchema = $this->get('liform')->transform($form);
            $serializer = $this->get('serializer');

            if($request->isXmlHttpRequest()) {
//            if(true) {
                return $this->getAjaxResponse($this->serialize(['initialState' => [
                    Inflector::camelize( $resource->getName().'_form') => $formSchema,
                    'initialValues' => json_decode(json_encode(json_decode($serializer->serialize($form, 'json'))), true)
                ]], $gridType->getSerialization()), 200);
            }

            return $this->render($grid->getTemplate(), [
                'initialState' => $this->serialize([
                    Inflector::camelize( $resource->getName().'_form') => $formSchema
                ], $gridType->getSerialization())
            ]);
        } else {
            $formFactory = $this->get('form.factory');

            $form = $formFactory->create(CampaignType::class, $result);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->get('doctrine.orm.default_entity_manager');
                $em->persist($result);
                $em->flush();

                return $this->response([
                    $resource->getName() => $result
                ], 200);
            }
            return $this->response([
                'errors' => $form->getErrors()
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request) {
        return $this->render($this->getGrid()->getTemplate(), ['initialState' => []]);
    }
}