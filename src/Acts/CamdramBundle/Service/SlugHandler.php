<?php

namespace Acts\CamdramBundle\Service;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Gedmo\Sluggable\Handler\SlugHandlerWithUniqueCallbackInterface;
use Gedmo\Sluggable\Mapping\Event\SluggableAdapter;
use Gedmo\Sluggable\SluggableListener;
use Gedmo\Exception\InvalidMappingException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class SlugHandler implements SlugHandlerWithUniqueCallbackInterface
{
    /**
     * @var SluggableListener
     */
    protected $sluggable;

    /**
     * Used options
     *
     * @var array
     */
    private $options;

    /**
     * {@inheritDoc}
     */
    public function __construct(SluggableListener $sluggable)
    {
        $this->sluggable = $sluggable;
    }

    /**
     * {@inheritDoc}
     */
    public function onChangeDecision(SluggableAdapter $ea, array &$config, $object, &$slug, &$needToChangeSlug): void
    {
        $om = $ea->getObjectManager();
        $isInsert = $om->getUnitOfWork()->isScheduledForInsert($object);
        $this->options = array_merge(
            ['separator' => '-', 'format' => 'Y',
                'check_included' => true, 'nameField' => 'name'],
            $config['handlers'][get_called_class()]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function postSlugBuild(SluggableAdapter $ea, array &$config, $object, &$slug): void
    {
        if (!array_key_exists('dateField', $this->options)) return;

        $accessor = PropertyAccess::createPropertyAccessor();
        $date = $accessor->getValue($object, $this->options['dateField']);
        if ($date instanceof \DateTime) {
            $year = $date->format($this->options['format']);
            if (!$this->options['check_included'] || strpos($slug, $year) === false) {
                $config['prefix'] = $year . $this->options['separator'];
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function validate(array $options, ClassMetadata $meta): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function beforeMakingUnique(SluggableAdapter $ea, array &$config, $object, &$slug): void
    {
        // Deal with cases of no slug, #448.
        if ($slug === '' || preg_match("/^\d{4}-?$/", $slug)) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $name = $accessor->getValue($object, $this->options['nameField']);
            $slug .= substr(md5($name ?? ''), 0, 8);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function onSlugCompletion(SluggableAdapter $ea, array &$config, $object, &$slug): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function handlesUrlization(): bool
    {
        return false;
    }
}
