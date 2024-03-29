<?php

namespace Acts\CamdramBundle\Entity;

use Acts\CamdramSecurityBundle\Security\OwnableInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Organisation common features
 * @ORM\MappedSuperclass
 */
abstract class Organisation extends BaseEntity implements OwnableInterface
{
    /**
     * @var ?string
     *
     * @ORM\Column(name="contact_email", type="string", length=255, nullable=true)
     * @Assert\Email()
     * @Serializer\Exclude
     */
    private $contactEmail;

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): self
    {
        $this->contactEmail = $contactEmail;
        return $this;
    }

    /**
     * @return Organisation
     */
    abstract public function setShortName(?string $shortName);

    abstract public function getShortName(): ?string;

    /**
     * @return Organisation
     */
    abstract public function setCollege(?string $college);

    abstract public function getCollege(): ?string;

    /**
     * @return Organisation
     */
    abstract public function setLogoUrl(?string $logoUrl);

    abstract public function getLogoUrl(): ?string;

    abstract public function getId(): ?int;

    /**
     * @return Organisation
     */
    abstract public function setName(?string $name);

    abstract public function getName(): ?string;

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Organisation
     */
    abstract public function setDescription($description);

    /**
     * Get description
     *
     * @return string
     */
    abstract public function getDescription();

    /**
     * Set facebook_id
     *
     * @param string $facebookId
     *
     * @return Organisation
     */
    abstract public function setFacebookId($facebookId);

    /**
     * Get facebook_id
     *
     * @return string
     */
    abstract public function getFacebookId();

    /**
     * @return Organisation
     */
    abstract public function setTwitterId(?string $twitterId);

    abstract public function getTwitterId(): ?string;

    public function setSocialId($service, $id)
    {
        switch ($service) {
            case 'facebook': $this->setFacebookId($id); break;
            case 'twitter': $this->setTwitterId($id); break;
        }
    }

    public function getSocialId($service)
    {
        switch ($service) {
            case 'facebook': return $this->getFacebookId();
            case 'twitter': return $this->getTwitterId();
        }
    }

    public function getFacebookUrl()
    {
        $id = $this->getFacebookId();
        return is_numeric($id) ? "https://www.facebook.com/$id" : $id;
    }

    public function getTwitterUrl()
    {
        return 'https://twitter.com/intent/user?user_id='.$this->getTwitterId();
    }

    public function getSocialUrl($service)
    {
        switch ($service) {
            case 'facebook': return $this->getFacebookUrl();
            case 'twitter': return $this->getTwitterUrl();
        }
    }

    public function hasSocialId()
    {
        return $this->getFacebookId() || $this->getTwitterId();
    }

    /**
     * Set image
     *
     * @param Image $image
     *
     * @return Organisation
     */
    abstract public function setImage(Image $image = null);

    /**
     * Get image
     *
     * @return Image
     */
    abstract public function getImage();

    /**
     * Set slug
     * @return Organisation
     */
    abstract public function setSlug(?string $slug);

    abstract public function getSlug(): ?string;

    public function getRank()
    {
        return PHP_INT_MAX;
    }

    /**
     * @return $this
     */
    abstract public function addAdvert(Advert $advert);

    abstract public function removeAdvert(Advert $advert);

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    abstract public function getAdverts();

    public function getAdvertById($id)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('id', $id))
        ;

        return $this->getAdverts()->matching($criteria)->first();
    }

    abstract public function getOrganisationType();
}
