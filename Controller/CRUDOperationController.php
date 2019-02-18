<?php

/*
 * Overriding CRUDController(createAction) in SonataAdmin Partner entity
 */

namespace Vtereshenkov\SonataOperationBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CRUDOperationController extends Controller {
    
    public function moderatedAction($id){
    
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $object->setModerated(true);
        $object->setModerationDate(new \DateTime());
        $object->setModerator($user);
        
        $em->persist($object);
        $em->flush();
                
        $this->addFlash('sonata_flash_success', 'Operation successfull moderated');
        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
