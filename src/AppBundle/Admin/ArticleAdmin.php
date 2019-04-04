<?php
// src/AppBundle/Admin/ArticleAdmin.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ArticleAdmin extends Admin{
    public $supportsPreviewMode = true;
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->with('article_content')
                    ->add('title','text')
                    ->add('content','text')
                ->end()

                ->with('user_content')
                    ->add('user_id', 'entity', array(
                        'class' => 'AppBundle\Entity\User',
                        'property' => 'id',
                    ))
                ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title')
                       ->add('content')
                       ->add('user_id')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('title')
                   ->addIdentifier('content')
                   ->addIdentifier('user_id')
        ;
    }
}