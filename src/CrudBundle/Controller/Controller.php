<?php

namespace Siwymilek\CrudBundle\Controller;

use Siwymilek\CrudBundle\Grid\Types\GridTypeInterface;
use Siwymilek\CrudBundle\Model\ModelInterface;
use http\Exception\RuntimeException;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Controller
 * @package Siwymilek\CrudBundle\Controller
 */
class Controller extends BaseController
{
    protected $serializationMethod = 'json';

    /**
     * @return \Siwymilek\CrudBundle\Grid\GridInterface
     */
    public function getGrid()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $gridHandler = $this->get('core.crud_bundle.handler.grid');

        return $gridHandler->getGrid($request->get('_gridName'));
    }

    /**
     * @param string $type
     * @return GridTypeInterface
     */
    public function getType($type = null)
    {
        if(!$type) {
            $calledActionBacktrace = array_filter(debug_backtrace(), function($arr) {
                if(isset($arr['function']) && preg_match('/[a-zA-Z0-9]+Action/', $arr['function'])) {
                    return $arr;
                }
            });

            $calledAction = array_values($calledActionBacktrace)[0]['function'];
            $type = str_replace('Action', null, $calledAction);
        }

        $type = array_filter($this->getGrid()->getTypes(), function($t) use($type) {
            /** @var GridTypeInterface $t */
            return $t->getType() === $type;
        });
        $type = end($type);
        return $type;
    }

//    const DEFAULT_SERIALIZATION_METHOD = 'json';
//
//    /**
//     * @var Request
//     */
//    protected $request;
//;
//
//    public function __construct()
//    {
//        $this->serializationMethod = static::DEFAULT_SERIALIZATION_METHOD;
//    }
//
//    /**
//     * @param Request $request
//     */
//    protected function handleRequest(Request $request)
//    {
//        $this->request = $request;
//    }
//
//    /**
//     * @return mixed
//     */
//    protected function getGrid()
//    {
//        return $this->request->get('_gridName');
//    }
//
//    /**
//     * @param array $gridNode
//     * @return mixed
//     */
//    private function getNode($gridNode = [])
//    {
//        if (!isset($gridNode['repository']['class'])) {
//            throw new RuntimeException('Repository class is missing.');
//        }
//
//        if (!isset($gridNode['repository']['method'])) {
//            throw new RuntimeException('Repository method is missing.');
//        }
//
//        $arguments = $gridNode['repository']['arguments'];
//        $this->parseArgs($arguments);
//
//        $repository = $this->get($gridNode['repository']['class']);
//        $method = $gridNode['repository']['method'];
//
//        $nodes = call_user_func_array([$repository, $method], [$arguments]);
//
//        return $nodes;
//    }
//
//    /**
//     * @return mixed
//     */
//    protected function getNodes()
//    {
//        $grid = $this->getGrid();
//        $dataGrids = $grid['orm'];
//
//        $nodes = [];
//        foreach ($dataGrids as $key => $gridNode) {
//            $nodes[$key] = $this->getNode($gridNode);
//        }
//
//        return $nodes;
//    }
//
//
    /**
     * @param $args
     */
    protected function parseArgs(&$args)
    {
        foreach ($args as $key => &$value) {
            $value = $this->parseRequestValueExpression($value);
        }
    }

    /**
     * @param $value
     * @return null|string|string[]
     */
    private function parseRequestValueExpression($value)
    {
        $pattern = '/expr\:service\([\'"](.*?)[\'"]\)/';
        if (!preg_match($pattern, $value, $m)) return $value;
        $value = preg_replace($pattern, 'container', $value);
        return (new ExpressionLanguage())->evaluate($value, ['container' => $this->get($m[1])]);
    }

    /**
     * @param array $data
     * @param int $status
     * @return Response
     */
    protected function getAjaxResponse($data = [], $status = 200)
    {
        $response = new Response($data);
        switch (strtolower($this->serializationMethod)) {
            case 'json':
                $response->headers->set('Content-Type', 'json');
                break;

            case 'xml':
                $response->headers->set('Content-Type', 'xml');
                break;
        }

        $response->setStatusCode($status);
        return $response;
    }

    /**
     * @param array $data
     * @param array $serialization
     * @return array|mixed|string
     */
    protected function serialize($data = [], $serialization = [])
    {
        $serializer = $this->get('jms_serializer');
        $groups = $serialization['groups'];
        $data = $serializer->serialize($data, $serialization['method'], count($groups) ? SerializationContext::create()->setGroups($groups) : null);
        return $data;
    }

    /**
     * @param array $voters
     * @param ModelInterface $model
     */
    protected function checkVoters($voters = [], ModelInterface $model) {

        foreach($voters as $voter) {
            $this->denyAccessUnlessGranted($voter, $model);
        }
    }

    /**
     * @param array $content
     * @param int $statusCode
     * @param bool $forceJson
     * @return Response
     */
    protected function response($content = [], $statusCode = 200, $forceJson = false)
    {
        $gridType = $this->getType();
        $grid = $this->getGrid();

        $request = $this->get('request_stack')->getCurrentRequest();

        if($request->isXmlHttpRequest() || $forceJson) {
            return $this->getAjaxResponse($this->serialize(['initialState' => $content], $gridType->getSerialization()), $statusCode);
        }

        return $this->render($grid->getTemplate(), [
            'initialState' => $this->serialize($content, $gridType->getSerialization())
        ]);
    }

    /**
     * @param ModelInterface $entity
     * @return bool
     * @throws \Exception
     */
    protected function injectOwner(ModelInterface &$entity)
    {
        $injectionMethods = [
            'prefix' => ['set', 'add'],
            'suffix' => ['owner', 'user']
        ];

        foreach($injectionMethods['prefix'] as $prefix) {
            foreach($injectionMethods['suffix'] as $suffix) {
                $method = $prefix.ucfirst($suffix);

                if(method_exists($entity, $method)) {
                    $entity->{$method}($this->getUser());
                    return true;
                }
            }
        }

        throw new \Exception(sprintf('There is no owner-setter method in %s entity.', get_class($entity)));
    }
}