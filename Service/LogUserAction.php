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
class LogUserAction {
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {        
        $this->em = $em;
    }

    public function createRecord($className, $operationType, $user, $idObject, $dataBefore = null, $dataAfter = null){
        $classNameO = $this->getObjectClassNsi($className);
        $operationTypeO = $this->getObjectOperationTypeNsi($operationType);
        $userGroups = $user->getGroups();
        if(!empty($userGroups[0])){ 
            /*Get opration title */
            if(!empty($dataBefore)){
                $temp = unserialize($dataBefore);
                                
                if(!empty($temp['title'])){
                    $oprationTitle = $temp['title'];
                }else{
                    $oprationTitle = (!empty($temp['name']) ? $temp['name'] : $temp['username']);
                }
            }else{
                $temp = unserialize($dataAfter);
                if(!empty($temp['title'])){
                    $oprationTitle = $temp['title'];
                }else{
                    $oprationTitle = (!empty($temp['name']) ? $temp['name'] : $temp['username']);
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
            if(!empty($dataBefore)){
                $operation->setObjectBefore($dataBefore);
            }
            if(!empty($dataAfter)){
                $operation->setObjectAfter($dataAfter);
            }
            
            $this->em->persist($operation);
            $this->em->flush();            
        }     
               
    }
    
    protected function getObjectClassNsi($className){
       $repository = $this->em->getRepository(ClassName::class);
       $objects = $repository->findBy(['name' => $className]);
       if(empty($objects)){
          /*Create new ClassName*/
           $entityN = new ClassName();
           $entityN->setName($className);           
           $this->em->persist($entityN);
           $this->em->flush();
           
           return $entityN;
       }
       return $objects[0];
    }
    
    protected function getObjectOperationTypeNsi($name){
       $repository = $this->em->getRepository(OperationType::class);
       $objects = $repository->findBy(['name' => $name]);
       if(empty($objects)){
          /*Create new ClassName*/
           $entityN = new OperationType();
           $entityN->setName($name);           
           $this->em->persist($entityN);
           $this->em->flush();
           
           return $entityN;
       }
       return $objects[0];
    }
    
}
