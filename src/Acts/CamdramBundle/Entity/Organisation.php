<?php

namespace Acts\CamdramBundle\Entity;

use Acts\CamdramBundle\Search\SearchableInterface;
use Acts\CamdramSecurityBundle\Security\OwnableInterface;

/**
 * Organisation common features
 */
abstract class Organisation implements OwnableInterface
{
    /**
     * Set short_name
     *
     * @param string $shortName
     *
     * @return Society
     */
    abstract public function setShortName($shortName);

    /**
     * Get short_name
     *
     * @return string
     */
    abstract public function getShortName();

    /**
     * Set college
     *
     * @param string $college
     *
     * @return Society
     */
    abstract public function setCollege($college);

    /**
     * Get college
     *
     * @return string
     */
    abstract public function getCollege();

    /**
     * Set logo_url
     *
     * @param string $logoUrl
     *
     * @return Society
     */
    abstract public function setLogoUrl($logoUrl);

    /**
     * Get logo_url
     *
     * @return string
     */
    abstract public function getLogoUrl();

    /**
     * Get id
     *
     * @return int
     */
    abstract public function getId();

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Organisation
     */
    abstract public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    abstract public function getName();

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
     * Set twitter_id
     *
     * @param string $twitterId
     *
     * @return Organisation
     */
    abstract public function setTwitterId($twitterId);

    /**
     * Get twitter_id
     *
     * @return string
     */
    abstract public function getTwitterId();

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
        return 'http://www.facebook.com/'.$this->getFacebookId();
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
     *
     * @param string $slug
     *
     * @return Organisation
     */
    abstract public function setSlug($slug);

    /**
     * Get slug
     *
     * @return string
     */
    abstract public function getSlug();

    public function getRank()
    {
        return PHP_INT_MAX;
    }

    /**
     * Add news
     *
     * @param \Acts\CamdramBundle\Entity\News $news
     *
     * @return Organisation
     */
    abstract public function addNew(\Acts\CamdramBundle\Entity\News $news);

    /**
     * Remove news
     *
     * @param \Acts\CamdramBundle\Entity\News $news
     */
    abstract public function removeNew(\Acts\CamdramBundle\Entity\News $news);

    /**
     * Get news
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    abstract public function getNews();

    /**
     * Add news
     *
     * @param \Acts\CamdramBundle\Entity\News $news
     *
     * @return Organisation
     */
    abstract public function addNews(\Acts\CamdramBundle\Entity\News $news);

    /**
     * Remove news
     *
     * @param \Acts\CamdramBundle\Entity\News $news
     */
    abstract public function removeNews(\Acts\CamdramBundle\Entity\News $news);

    /**
     * Add applications
     *
     * @param \Acts\CamdramBundle\Entity\Application $applications
     *
     * @return Organisation
     */
    abstract public function addApplication(\Acts\CamdramBundle\Entity\Application $applications);

    /**
     * Remove applications
     *
     * @param \Acts\CamdramBundle\Entity\Application $applications
     */
    abstract public function removeApplication(\Acts\CamdramBundle\Entity\Application $applications);

    /**
     * Get applications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    abstract public function getApplications();

    abstract public function getOrganisationType();
}
