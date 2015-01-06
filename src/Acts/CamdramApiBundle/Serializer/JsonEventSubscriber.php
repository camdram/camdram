<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 04/01/15
 * Time: 13:56
 */

namespace Acts\CamdramApiBundle\Serializer;


use Acts\CamdramApiBundle\Configuration\AnnotationReader;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ExpressionLanguage;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\RouterInterface;

class JsonEventSubscriber  implements EventSubscriberInterface
{

    /**
     * @var AnnotationReader
     */
    private $reader;

    /**
     * @var RouterInterface
     */
    private $router;


    public function __construct(AnnotationReader $reader, RouterInterface $router)
    {
        $this->reader = $reader;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event'  => Events::POST_SERIALIZE,
                'format' => 'json',
                'method' => 'onPostSerialize',
            ),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $links = $this->reader->read($event->getObject())->getLinks();

        /** @var JsonSerializationVisitor $visitor */
        $visitor = $event->getVisitor();
        $object = $event->getObject();
        $linkJson = array();
        $accessor = PropertyAccess::createPropertyAccessor();
        $language = new ExpressionLanguage();

        foreach ($links as $link) {
            $child = $accessor->getValue($event->getObject(), $link->getProperty());
            $compiledParams = array();
            foreach ($link->getParams() as $key => $expr) {
                $compiledParams[$key] = $language->evaluate($expr, array('object' => $object));
            }
            $linkJson[$link->getName()] = $this->router->generate($link->getRoute(), $compiledParams, true);

            if ($link->getEmbed()) {

                $childData = array();
                foreach (array('id', 'name', 'slug', 'blah') as $property) {
                    if ($accessor->isReadable($child, $property)) {
                        $childData[$property] = $accessor->getValue($child, $property);
                    }
                }
                $visitor->addData($link->getName(), $childData);
            }
        }

        $visitor->addData('_links', $linkJson);

        $class = new \ReflectionClass($object);
        $visitor->addData('_type', strtolower($class->getShortName()));
    }

} 