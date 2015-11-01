<?php

namespace Acts\CamdramSecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimilarName
 *
 * @ORM\Table(name="acts_similar_names", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="names_unique",columns={"name1", "name2"})
 * })
 * @ORM\Entity(repositoryClass="SimilarNameRepository")
 */
class SimilarName
{
    const EQUIVALENT = 1;
    const NOT_EQUIVALENT = -1;
    const UNKNOWN = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name1", type="string", length=255)
     */
    private $name1;

    /**
     * @var string
     *
     * @ORM\Column(name="name2", type="string", length=255)
     */
    private $name2;

    /**
     * @var bool
     *
     * @ORM\Column(name="equivalence", type="boolean")
     */
    private $equivalence;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name1
     *
     * @param string $name1
     *
     * @return SimilarName
     */
    public function setName1($name1)
    {
        $this->name1 = $name1;

        return $this;
    }

    /**
     * Get name1
     *
     * @return string
     */
    public function getName1()
    {
        return $this->name1;
    }

    /**
     * Set name2
     *
     * @param string $name2
     *
     * @return SimilarName
     */
    public function setName2($name2)
    {
        $this->name2 = $name2;

        return $this;
    }

    /**
     * Get name2
     *
     * @return string
     */
    public function getName2()
    {
        return $this->name2;
    }

    /**
     * Set equivalence
     *
     * @param bool $equivalence
     *
     * @return SimilarName
     */
    public function setEquivalence($equivalence)
    {
        $this->equivalence = $equivalence;

        return $this;
    }

    /**
     * Get equivalence
     *
     * @return bool
     */
    public function getEquivalence()
    {
        return $this->equivalence;
    }
}
