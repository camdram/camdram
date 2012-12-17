<?php
// src/Acme/ApiBundle/Entity/AccessToken.php

namespace Acts\CamdramSecurityBundle\Entity;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ExternalClient extends BaseClient
{


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="scope", type="string", length=20)
     */
    private $scope;

}