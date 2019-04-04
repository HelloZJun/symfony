<?php
// src/AppBundle/Admin/UserAdmin.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserAdmin extends Admin{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('username','text')
                   ->add('email','text')
                   ->add('password','text')
                   ->add('enabled','text')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('username')
                       ->add('email')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('username')
                   ->addIdentifier('email')
                   ->addIdentifier('enabled')
        ;
    }

    public function preUpdate($obj){
        $this->bcrypt_pwd($obj);
    }

    public function prePersist($obj){
        $this->bcrypt_pwd($obj);
    }

    public function bcrypt_pwd(&$obj){
        $password=password_hash($obj->getPassword(),PASSWORD_BCRYPT);
        $obj->setPassword($password);
    }
}