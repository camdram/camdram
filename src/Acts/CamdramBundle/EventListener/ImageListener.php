<?php
namespace Acts\CamdramBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Oneup\UploaderBundle\Event\ValidationEvent;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Oneup\UploaderBundle\Uploader\Exception\ValidationException;
use Acts\CamdramBundle\Entity\Image;
use Symfony\Component\HttpFoundation\File\File;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Psr\Log\LoggerInterface;

class ImageListener
{

    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var ImagineInterface
     */
    private $imagine;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(

        ObjectManager $em,
        AuthorizationCheckerInterface $authorizationChecker,
        ImagineInterface $imagine,

        LoggerInterface $logger

    ) {
        $this->entityManager = $em;
        $this->authorizationChecker = $authorizationChecker;
        $this->imagine = $imagine;
        $this->logger = $logger;
    }

    private function getEntity($type, $identifier)
    {
        switch ($type) {
            case 'event':
                return $this->entityManager->getRepository('\Acts\CamdramBundle\Entity\Event')->find($identifier);
            case 'show':
                return $this->entityManager->getRepository('\Acts\CamdramBundle\Entity\Show')->findOneBySlug($identifier);
            case 'society':
                return $this->entityManager->getRepository('\Acts\CamdramBundle\Entity\Society')->findOneBySlug($identifier);
            case 'venue':
                return $this->entityManager->getRepository('\Acts\CamdramBundle\Entity\Venue')->findOneBySlug($identifier);
            default:
                return null;
        }
    }

    public function validate(ValidationEvent $event)
    {
        $file = $event->getFile();

        if (is_null($file)) {
            $this->logger->error('ImageListener: Null file uploaded');
            throw new ValidationException('No image uploaded');
        }

        if (!preg_match('/^image\/.*$/', $file->getMimeType())) {
            $this->logger->error(
                'ImageListener: MIME type is unsupported',
                ['filename' => $file->getBasename(), 'type' => $file->getMimeType()]
            );
            throw new ValidationException('File is not a valid image');
        }

        if ($event->getFile()->getSize() > 2 * 1024 * 1024) {
            $this->logger->error(
                'ImageListener: File > 2Mb uploaded',
                ['filename' => $file->getBasename(), 'size' => $file->getSize()]
            );
            throw new ValidationException('Files over 2Mb cannot be processed');
        }

        $type = $event->getRequest()->request->get('type', '');
        $identifier = $event->getRequest()->request->get('identifier', '');

        $entity = $this->getEntity($type, $identifier);
        if (!$entity) {
            $this->logger->error(
                'ImageListener: Identifier not found',
                ['filename' => $file->getBasename(), 'type' => $type, 'identifier' => $identifier]
            );
            throw new ValidationException('Invalid '.$type.' identifier');
        }

        if (!$this->authorizationChecker->isGranted('EDIT', $entity)) {
            throw new ValidationException('Not authorized to edit '.$type);
        }

        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new ValidationException('Re-authentication required');
        }
    }

    public function onUpload(PostPersistEvent $event)
    {
        $type = $event->getRequest()->request->get('type', '');
        $identifier = $event->getRequest()->request->get('identifier', '');
        $entity = $this->getEntity($type, $identifier);
        if (!$this->authorizationChecker->isGranted('EDIT', $entity)
            || !$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return;
        }

        /**
         * @var File $file
         */
        $file = $event->getFile();

        $this->logger->debug('ImageListener: Attempting too open image', ['file' => $file->getPathname()]);
        $imageFile = $this->imagine->open($file->getPathname());
        if ($imageFile->getSize()->getWidth() > 1024 || $imageFile->getSize()->getHeight() > 768) {
            $this->logger->debug('ImageListener: Resizing uploaded image');
            //Resize file to save disk space
            $maxSize = new Box(1024, 768);
            $imageFile->thumbnail($maxSize, ImageInterface::THUMBNAIL_OUTBOUND);
            $imageFile->save($file->getPathname());
        }
        $size = $imageFile->getSize();

        $this->logger->debug('ImageListener: Creating entity for uploaded image');
        $image = new Image();
        $image->setFilename($file->getFilename());
        $image->setType($file->getMimeType());
        $image->setCreatedAt(new \DateTime());
        $image->setExtension($file->getExtension());
        $image->setWidth($size->getWidth());
        $image->setHeight($size->getHeight());

        $this->logger->debug('ImageListener: Linking image entity for uploaded image');
        $entity->setImage($image);
        $this->entityManager->persist($image);
        $this->logger->debug('ImageListener: Flushing Doctrine');
        $this->entityManager->flush();

        //if everything went fine
        $response = $event->getResponse();
        $response['success'] = true;
        return $response;
    }
}
