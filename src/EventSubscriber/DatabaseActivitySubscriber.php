<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;


class DatabaseActivitySubscriber implements EventSubscriber
{
    /** KernelInterface $appKernel */
    private $appKernel;
    private $rootDir;

    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
        $this->rootDir = $appKernel->getProjectDir();
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate,
            Events::postRemove,
        ];
    }

    public function postRemove(PostRemoveEventArgs $args): void 
    {
        $this->logActivity('remove', $args->getObject());
    }
    public function postUpdate(PostUpdateEventArgs $args): void 
    {
        $this->logActivity('update', $args->getObject());
    }

    public function logActivity(string $action, mixed $entity): void 
    {
        if(($entity instanceof Product) && $action === "remove"){
            // remove image
            $imageUrls = $entity->getImageUrls();

            foreach ($imageUrls as $imageUrl) {
                $filelink = $this->rootDir. "/public/assets/images/products/".$imageUrl;
                // dd($filelink);
                $this->deleteImage($filelink);
                // dd($filelink);
            }

        }
        if(($entity instanceof Category) && $action === "remove"){
            // remove image
            $filename = $entity->getImageUrl();

            $filelink = $this->rootDir. "/public/assets/images/categories/".$filename;

            $this->deleteImage($filelink);

            
        }
        
    }

    public function deleteImage(string $filelink): void
    {
        try {
            //code...
            $result = unlink($filelink);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
