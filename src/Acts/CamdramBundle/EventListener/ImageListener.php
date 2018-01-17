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
    
    public function __construct(ObjectManager $em, 
        AuthorizationCheckerInterface $authorizationChecker,
        ImagineInterface $imagine)
    {
        $this->entityManager = $em;
        $this->authorizationChecker = $authorizationChecker;
        $this->imagine = $imagine;
    }
    
    private function getRepository($type)
    {
        switch ($type)
        {
            case 'show':
                return $this->entityManager->getRepository('ActsCamdramBundle:Show');
            default:
                return null;
        }
    }
    
    public function validate(ValidationEvent $event)
    {
        if (!$event->getFile())
        {
            throw new ValidationException('No image uploaded');
        }
        
        if (!preg_match('/^image\/.*$/', $event->getFile()->getMimeType()))
        {
            throw new ValidationException('File is not a valid image');
        }
        
        if ($event->getFile()->getSize() > 2 * 1024 * 1024)
        {
            throw new ValidationException('Files over 2Mb cannot be processed');
        }
        
        $type = $event->getRequest()->request->get('type', '');
        $repo = $this->getRepository($type);
        $identifier = $event->getRequest()->request->get('identifier', '');
        
        if (is_null($repo))
        {
            throw new ValidationException('Invalid image type');
        }
        
        $entity = $repo->findOneBySlug($identifier);
        if (!$entity)
        {
            throw new ValidationException('Invalid '.$type);
        }
        
        if (!$this->authorizationChecker->isGranted('EDIT', $entity))
        {
            throw new ValidationException('Not authorized to edit '.$type);
        }
    }
    
    public function onUpload(PostPersistEvent $event)
    {
        $type = $event->getRequest()->request->get('type', '');
        $repo = $this->getRepository($type);
        $identifier = $event->getRequest()->request->get('identifier', '');
        $entity = $repo->findOneBySlug($identifier);
        if (!$this->authorizationChecker->isGranted('EDIT', $entity))
        {
            return;
        }
        
        /**
         * @var File $file
         */
        $file = $event->getFile();
        
        $imageFile = $this->imagine->open($file->getPathname());
        if ($imageFile->getSize()->getWidth() > 1024 || $imageFile->getSize()->getHeight() > 768)
        {
            //Resize file to save disk space
            $maxSize = new Box(1024, 768);
            $imageFile->thumbnail($maxSize, ImageInterface::THUMBNAIL_OUTBOUND);
            $imageFile->save($file->getPathname());
        }
        $size = $imageFile->getSize();
        
        $image = new Image();
        $image->setFilename($file->getFilename());
        $image->setType($file->getMimeType());
        $image->setCreatedAt(new \DateTime());
        $image->setExtension($file->getExtension());
        $image->setWidth($size->getWidth());
        $image->setHeight($size->getHeight());
        
        $entity->setImage($image);
        $this->entityManager->persist($image);
        $this->entityManager->flush();
        
        //if everything went fine
        $response = $event->getResponse();
        $response['success'] = true;
        return $response;
    }
}