<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 11/28/13
 * Time: 11:57 AM
 */

namespace Fp\JsFormValidatorBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxController extends Controller {
    public function checkUniqueEntityAction()
    {
        $data = $this->getRequest()->request->all();
        $repo = $this->get('doctrine')->getRepository($data['entity']);

        $criteria = array();
        foreach ($data['data'] as $key => $value) {
            $value = ('' !== $value) ? $value : null;
            if ($data['ignoreNull'] && null === $repo->findBy(array($key => $value))) {
                break;
            } else {
                $criteria[$key] = $value;
            }
        }

        $result = true;
        if (count($criteria) === count($data['data'])) {
            $entity = $this
                ->get('doctrine')
                ->getRepository($data['entity'])
                ->{$data['repositoryMethod']}($data['data']);

            $result = empty($entity);
        }

        return new JsonResponse($result);
    }
} 