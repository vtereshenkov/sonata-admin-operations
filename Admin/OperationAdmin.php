<?php

namespace Vtereshenkov\SonataOperationBundle\Admin;

use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Vtereshenkov\SonataOperationBundle\Entity\ClassName;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\AdminBundle\Show\ShowMapper;
use App\Application\Sonata\UserBundle\Entity\User;
use App\Application\Sonata\UserBundle\Entity\Group;
use Vtereshenkov\SonataOperationBundle\Entity\OperationType;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class OperationAdmin extends AbstractAdmin {
        
    protected $baseRoutePattern = 'operations';

    public function configureShowFields(ShowMapper $showMapper) {
        $showMapper
                ->with('General', [
                    'class' => 'col-md-12',
                    'box_class' => 'box box-solid box-info',
                    'description' => 'Object info',
                ])
                ->add('title', null, [
                    'label' => 'Title'
                ])
                ->add('className.name', null, [
                    'label' => 'Class'
                ])
                ->add('author.username', null, [
                    'label' => 'Author'
                ])
                ->add('authorGroup.name', null, [
                    'label' => 'Author Group'
                ])
                ->add('date')
                ->add('moderated', 'boolean', ['editable' => true])
                ->add('moderationDate')
                ->add('moderator.username', null, [
                    'label' => 'Moderator'
                ])
                ->add('type.name', null, [
                    'label' => 'Type'
                ])
                ->end()
                ->with('Object before', [
                    'class' => 'col-md-6',
                    'box_class' => 'box box-solid box-danger',
                    'description' => 'Object before',
                ])
                ->add('ObjectBeforeFormat', 'html', [
                    'label' => 'Object data'
                ])
                ->end()
                ->with('Object after', [
                    'class' => 'col-md-6',
                    'box_class' => 'box box-solid box-success',
                    'description' => 'Object after',
                ])
                ->add('ObjectAfterFormat', 'html', [
                    'label' => 'Object data'
                ])
                ->end()

        ;
    }

    protected function configureRoutes(RouteCollection $collection) {
        $collection->remove('delete')
                ->remove('batch')
                ->remove('create')
                ->remove('edit');
        $collection->add('moderated', $this->getRouterIdParameter() . '/moderated');
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->addIdentifier('title', null, [
            'label' => 'Title'
        ]);
        $listMapper->addIdentifier('className.name', null, [
            'label' => 'Class'
        ]);
        $listMapper->add('date');
        $listMapper->add('author.username', null, [
            'label' => 'Author'
        ]);
        $listMapper->add('authorGroup.name', null, [
            'label' => 'Author Group'
        ]);
        $listMapper->add('type.name', null, [
            'label' => 'Type'
        ]);
        $listMapper->add('moderated', null, ['editable' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper->add('className', null, [], EntityType::class, [
            'class' => ClassName::class,
            'choice_label' => 'name',
        ]);
        $datagridMapper->add('author', null, [], EntityType::class, [
            'class' => User::class,
            'choice_label' => 'username',
        ]);
        $datagridMapper->add('authorGroup', null, [], EntityType::class, [
            'class' => Group::class,
            'choice_label' => 'name',
        ]);
        $datagridMapper->add('type', null, [], EntityType::class, [
            'class' => OperationType::class,
            'choice_label' => 'name',
        ]);
        $datagridMapper->add('date');
        $datagridMapper->add('moderated');
    }

    public function toString($object) {
        return $object instanceof \Vtereshenkov\SonataOperationBundle\Entity\Operation ? $object->getClassName()->getName() : 'Operation';
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null) {
        if (!$childAdmin && !in_array($action, ['show'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $moderated = $this->getSubject()->getModerated();
        $id = $admin->getRequest()->get('id');
        if (true !== $moderated) {
            $menu->addChild(
                    'Moderated', [
                'uri' => $admin->generateUrl('moderated', ['id' => $id]),
                'attributes' => [
                    'class' => 'moderated-button btn-info',
                ]
                    ]
            );
        }
    }

}
