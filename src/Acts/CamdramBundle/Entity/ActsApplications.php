<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsApplications
 *
 * @ORM\Table(name="acts_applications")
 * @ORM\Entity
 */
class ActsApplications
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="showid", type="integer", nullable=false)
     */
    private $showid;

    /**
     * @var integer
     *
     * @ORM\Column(name="socid", type="integer", nullable=false)
     */
    private $socid;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadlinedate", type="date", nullable=false)
     */
    private $deadlinedate;

    /**
     * @var string
     *
     * @ORM\Column(name="furtherinfo", type="text", nullable=false)
     */
    private $furtherinfo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadlinetime", type="time", nullable=false)
     */
    private $deadlinetime;


}
