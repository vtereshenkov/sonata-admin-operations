<?php

namespace Vtereshenkov\SonataOperationBundle\Service;

use Vtereshenkov\SonataOperationBundle\Entity\ClassName;
use Vtereshenkov\SonataOperationBundle\Entity\OperationType;
use Vtereshenkov\SonataOperationBundle\Entity\Operation;

/**
 * Description of LogUserAction
 *
 * @author User
 */
class LogUserAction
{

    private $useShortName = false;

    public function __construct(\Doctrine\ORM\EntityManager $em, bool $useShortName)
    {
        $this->em = $em;
        $this->useShortName = $useShortName;
    }

    public function createRecord($className, $operationType, $user, $idObject, $dataBefore = null, $dataAfter = null)
    {
        $classNameO = $this->getObjectClassNsi($className);
        $operationTypeO = $this->getObjectOperationTypeNsi($operationType);
        $userGroups = $user->getGroups();
        if (!empty($userGroups[0])) {
            /* Get opration title */
            if (!empty($dataBefore)) {
                $temp = unserialize($dataBefore);

                if (!empty($temp['title'])) {
                    $oprationTitle = $temp['title'];
                } else {
                    $oprationTitle = (!empty($temp['name']) ? $temp['name'] : 'Undefined title');
                }
            } else {
                $temp = unserialize($dataAfter);
                if (!empty($temp['title'])) {
                    $oprationTitle = $temp['title'];
                } else {
                    $oprationTitle = (!empty($temp['name']) ? $temp['name'] : 'Undefined title');
                }
            }
            $operation = new Operation();
            $operation->setTitle($oprationTitle);
            $operation->setAuthor($user);
            $operation->setAuthorGroup($userGroups[0]);
            $operation->setClassName($classNameO);
            $operation->setClassObjectId($idObject);
            $operation->setDate(new \DateTime());
            $operation->setModerated(false);
            $operation->setType($operationTypeO);
            if (!empty($dataBefore)) {
                $operation->setObjectBefore($dataBefore);
            }
            if (!empty($dataAfter)) {
                $operation->setObjectAfter($dataAfter);
            }

            $this->em->persist($operation);
            $this->em->flush();
        }
    }

    protected function getObjectClassNsi($className)
    {
        $classNameF = $className;        
        if (true === $this->useShortName) {
            $temp = explode("\\", $classNameF);
            $classNameF = end($temp);
        } 
        $repository = $this->em->getRepository(ClassName::class);
        $objects = $repository->findBy(['name' => $classNameF]);
        if (empty($objects)) {
            /* Create new ClassName */
            $entityN = new ClassName();
            $entityN->setName($classNameF);
            $this->em->persist($entityN);
            $this->em->flush();

            return $entityN;
        }
        return $objects[0];
    }

    protected function getObjectOperationTypeNsi($name)
    {
        $repository = $this->em->getRepository(OperationType::class);
        $objects = $repository->findBy(['name' => $name]);
        if (empty($objects)) {
            /* Create new ClassName */
            $entityN = new OperationType();
            $entityN->setName($name);
            $this->em->persist($entityN);
            $this->em->flush();

            return $entityN;
        }
        return $objects[0];
    }

}
