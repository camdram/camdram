<?php
namespace Acts\CamdramBundle\Search;

interface SearchableInterface {

    public function getEntityType();

    public function getId();

    public function getName();

    public function getDescription();

    public function getSlug();

    public function getRank();

}