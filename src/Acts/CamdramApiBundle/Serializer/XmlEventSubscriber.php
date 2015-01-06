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
use JMS\Serializer\XmlSerializationVisitor;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ExpressionLanguage;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\RouterInterface;

class XmlEventSubscriber  implements EventSubscriberInterface
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
                'format' => 'xml',
                'method' => 'onPostSerialize',
            ),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $links = $this->reader->read($event->getObject())->getLinks();

        /** @var XmlSerializationVisitor $visitor */
        $visitor = $event->getVisitor();
        $object = $event->getObject();
        $accessor = PropertyAccess::createPropertyAccessor();
        $language = new ExpressionLanguage();

        foreach ($links as $link) {
            $child = $accessor->getValue($event->getObject(), $link->getProperty());
            $compiledParams = array();
            foreach ($link->getParams() as $key => $expr) {
                $compiledParams[$key] = $language->evaluate($expr, array('object' => $object));
            }

            $linkNode = $visitor->getDocument()->createElement('link');
            $visitor->getCurrentNode()->appendChild($linkNode);

            $linkNode->setAttribute('rel', $link->getShortEntity());
            $linkNode->setAttribute('href', $this->router->generate($link->getRoute(), $compiledParams, true));

            if ($link->getEmbed()) {
                $entryNode = $visitor->getDocument()->createElement($link->getName());

                $visitor->getCurrentNode()->appendChild($entryNode);
                $visitor->setCurrentNode($entryNode);
                $visitor->getCurrentNode()->setAttribute('rel', $link->getShortEntity());

                foreach (array('id', 'name', 'slug') as $property) {
                    if ($accessor->isReadable($child, $property)) {
                        $childNode = $visitor->getDocument()->createElement($property);
                        $valueNode = $visitor->getDocument()->createTextNode($accessor->getValue($child, $property));

                        $visitor->getCurrentNode()->appendChild($childNode);
                        $visitor->setCurrentNode($childNode);
                        $visitor->getCurrentNode()->appendChild($valueNode);

                        $visitor->revertCurrentNode();
                    }
                }

                $visitor->revertCurrentNode();

            }
        }
    }

} 