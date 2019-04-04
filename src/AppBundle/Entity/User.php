<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @OneToMany(targetEntity="Article", mappedBy="userId")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
        $this->id = new ArrayCollection();
    }

}
