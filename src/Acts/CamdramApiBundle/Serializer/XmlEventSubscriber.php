<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 04/01/15
 * Time: 13:56
 */

namespace Acts\CamdramApiBundle\Serializer;

use Acts\CamdramApiBundle\Configuration\AnnotationReader;
use Acts\CamdramApiBundle\Configuration\LinkMetadata;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\XmlSerializationVisitor;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ExpressionLanguage;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class XmlEventSubscriber implements EventSubscriberInterface
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
        $metadata = $this->reader->read($event->getObject());

        /** @var XmlSerializationVisitor $visitor */
        $visitor = $event->getVisitor();
        $object = $event->getObject();
        $accessor = PropertyAccess::createPropertyAccessor();

        if ($metadata->getLinks()) {
            foreach ($metadata->getLinks() as $link) {
                $child = $accessor->getValue($event->getObject(), $link->getProperty());
                if (!is_object($child)) {
                    continue;
                }

                $visitor->getCurrentNode()->appendChild($this->createLinkNode($link, $visitor, $object));

                if ($link->getEmbed()) {
                    $entryNode = $visitor->getDocument()->createElement($link->getName());

                    $visitor->getCurrentNode()->appendChild($entryNode);
                    $visitor->setCurrentNode($entryNode); // @phpstan-ignore-next-line
                    $visitor->getCurrentNode()->setAttribute('rel', $link->getEntity());

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

        if ($metadata->getSelfLink()) {
            $visitor->getCurrentNode()->appendChild($this->createLinkNode($metadata->getSelfLink(), $visitor, $object));
        }

        $class = new \ReflectionClass($object);
        $visitor->getCurrentNode()->setAttribute('rel', strtolower($class->getShortName()));
    }

    private function createLinkNode(LinkMetadata $link, XmlSerializationVisitor $visitor, $object)
    {
        $language = new ExpressionLanguage();
        $compiledParams = array();
        foreach ($link->getParams() as $key => $expr) {
            $compiledParams[$key] = $language->evaluate($expr, array('object' => $object));
        }

        $linkNode = $visitor->getDocument()->createElement('link');

        $linkNode->setAttribute('id', $link->getName());
        $linkNode->setAttribute('rel', $link->getEntity());
        $linkNode->setAttribute('href', $this->router->generate($link->getRoute(), $compiledParams, UrlGeneratorInterface::ABSOLUTE_PATH));

        return $linkNode;
    }
}
