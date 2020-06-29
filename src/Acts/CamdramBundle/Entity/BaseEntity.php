<?php

namespace Acts\CamdramBundle\Entity;

interface EntityInterface {
    public function getId(): ?int;
    public function getName(): ?string;
    public function getSlug(): ?string;
    public function getEntityType(): string;
}
