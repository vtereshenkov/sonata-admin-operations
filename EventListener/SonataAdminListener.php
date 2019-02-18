<?php

namespace Vtereshenkov\SonataOperationBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Event\PersistenceEvent;
use Sonata\AdminBundle\Event\ConfigureEvent;
use Vtereshenkov\SonataOperationBundle\Service\LogUserAction;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use ReflectionClass;

class SonataAdminListener
{

    protected $em;
    protected $lua;
    protected $sts;
    private $oldObjectData = '';

    public function __construct(EntityManagerInterface $em, TokenStorage $securityTokenStorage, LogUserAction $lua)
    {
        $this->em = $em;
        $this->lua = $lua;
        $this->sts = $securityTokenStorage;
    }
    
    
    public function onConfigureForm(ConfigureEvent $event){
        $object = $event->getAdmin()->getSubject();
        $this->oldObjectData = $this->_serealizeDataForLog($object);        
        
    }

    public function onPreUpdate(PersistenceEvent $event)
    {
        $object = $event->getObject();
        $className = get_class($object);

//        $original = $this->em->getUnitOfWork()->getOriginalEntityData($object);
        $user = $this->sts->getToken()->getUser();
        $dataBefore = $this->oldObjectData;
        $dataAfter = $this->_serealizeDataForLog($object);
       
        $this->lua->createRecord($className, 'edit', $user, $object->getId(), $dataBefore, $dataAfter);
    }

    /**
     * Save info for created entity
     * 
     * @param PersistenceEvent $event
     * @return void
     */
    public function onPostCreate(PersistenceEvent $event): void
    {
        $object = $event->getObject();
        $className = get_class($object);
        $user = $this->sts->getToken()->getUser();
        $dataAfter = $this->_serealizeDataForLog($object);

        $this->lua->createRecord($className, 'create', $user, $object->getId(), null, $dataAfter);
    }

    public function onPreRemove(PersistenceEvent $event)
    {
        $object = $event->getObject();
        $className = get_class($object);
        $user = $this->sts->getToken()->getUser();

        $dataBefore = $this->_serealizeDataForLog($object);
        $this->lua->createRecord($className, 'remove', $user, $object->getId(), $dataBefore, null);
    }

    /**
     * Prepare object data for log
     * 
     * @return string
     */
    private function _serealizeDataForLog($object)
    {
        $dataLog = [];
        if (is_object($object)) {
            $reflect = new ReflectionClass($object);
            $props = $reflect->getProperties();

            foreach ($props as $prop) {
                $methodForProp = 'get' . ucfirst($prop->getName());

                /* Check method exist */
                if ($reflect->hasMethod($methodForProp)) {
                    $propValue = $object->{$methodForProp}();
                    /* Check variable type */
                    if (is_object($propValue)) {
                        switch (get_class($propValue)) {
                            case 'Doctrine\ORM\PersistentCollection':
                                /* Collection */
                                $dataLog[$prop->getName()] = null;
                                if (!empty($propValue)) {
                                    foreach ($propValue as $key => $value) {
                                        if (is_object($value)) {
                                            /* Check method getName */
                                            if (method_exists($value, 'getName')) {
                                                $dataLog[$prop->getName()][] = [
                                                    'name' => $value->getName(),
                                                ];
                                            } elseif (method_exists($value, 'getTitle')) {
                                                $dataLog[$prop->getName()][] = [
                                                    'name' => $value->getTitle(),
                                                ];
                                            }
                                        } else {
                                            unset($object->{$methodForProp}()[$key]);
                                        }
                                    }
                                }
                                break;
                            default :
                                /* Check method getName */
                                if (method_exists($propValue, 'getName')) {
                                    $dataLog[$prop->getName()] = [
                                        'name' => $propValue->getName(),
                                    ];
                                } elseif (method_exists($propValue, 'getTitle')) {
                                    $dataLog[$prop->getName()] = [
                                        'name' => $propValue->getTitle(),
                                    ];
                                }
                                break;
                        }
                    } else {
                        $dataLog[$prop->getName()] = $propValue;
                    }
                }
            }
        } else {
            
        }

        return serialize($dataLog);
    }

}
