<?php
namespace Acts\CamdramBundle\Service;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Gedmo\Sluggable\Handler\SlugHandlerInterface;
use Gedmo\Sluggable\Mapping\Event\SluggableAdapter;
use Gedmo\Sluggable\SluggableListener;
use Gedmo\Exception\InvalidMappingException;
use Gedmo\Tool\Wrapper\AbstractWrapper;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DateSlugHandler implements SlugHandlerInterface
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
    public function onChangeDecision(SluggableAdapter $ea, array &$config, $object, &$slug, &$needToChangeSlug)
    {
        $om = $ea->getObjectManager();
        $isInsert = $om->getUnitOfWork()->isScheduledForInsert($object);
        $this->options = array_merge(
            array('separator' => '-', 'dateField' => 'date', 'format' => 'Y'),
            $config['handlers'][get_called_class()]
        );

        if (!$isInsert && !$needToChangeSlug) {
            $changeSet = $ea->getObjectChangeSet($om->getUnitOfWork(), $object);
            if (isset($changeSet[$this->options['dateField']])) {
                $needToChangeSlug = true;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function postSlugBuild(SluggableAdapter $ea, array &$config, $object, &$slug)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $date = $accessor->getValue($object, $this->options['dateField']);
        if ($date instanceof \DateTime) {
            $year = $date->format($this->options['format']);
            $config['prefix'] = $year . $this->options['separator'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function validate(array $options, ClassMetadata $meta)
    {
        if ($meta->getTypeOfField($options['dateField']) !== 'datetime') {
            throw new InvalidMappingException("Unable to find datetime field - [{$options['dateField']}] in class - {$meta->name}");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function onSlugCompletion(SluggableAdapter $ea, array &$config, $object, &$slug)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function handlesUrlization()
    {
        return false;
    }
}
