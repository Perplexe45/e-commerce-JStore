<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\HttpKernel\KernelInterface;
 


class Data extends Fixture
{
  /** KernelInterface $appKernel */
  private $appKernel;
  private $rootDir;
  private $categories;

  public function __construct(KernelInterface $appKernel)
    {
      $this->appKernel = $appKernel;
      $this->rootDir = $appKernel->getProjectDir();
    }
      

  public function load(ObjectManager $manager): void
  {


    $filename = $this->rootDir.'/src/DataFixtures/Data/products.json';
    $data = file_get_contents($filename);

    $products_json = json_decode($data);
    $products = [];
    foreach ($products_json as $product_item) {
            
      foreach ($product_item->imageUrls as $imageUrl) {
          try {
              $data = explode("/", $imageUrl);
              $imageFilename = $data[count($data) - 1 ];
              $result = copy(
                  $this->rootDir."/public/assets/images/products/".$imageUrl, 
                  $this->rootDir."/public/assets/images/products/".$imageFilename
              );
          } catch (\Throwable $th) {
              //throw $th;
          }
      }
      
      $product = new Product();
      $product->setName($product_item->name)
              ->setDescription($product_item->description)
              ->setMoreDescription($product_item->more_description)
              ->setImageUrls($product_item->imageUrls)
              ->setSoldePrice($product_item->solde_price*100) //Pour affichage en € le x100 
              ->setRegularPrice($product_item->regular_price*100)
              ->setIsBestSeller($product_item->isBestSeller)
              ->setIsNewArrival($product_item->isNewArrival)
              ->setIsFeatured($product_item->isFeatured)
              ->setIsSpecialOffer($product_item->isSpecialOffer)
              ->setStock(rand(400,890))
      ;
      /* for ($i=0; $i < 5; $i++) { 
          $product->addCategory($categories[rand(0,5)]);
      } */

      $products[] = $product;
      $manager->persist($product);
  }

  $filename = $this->rootDir.'/src/DataFixtures/Data/users.json';
	    $data = file_get_contents($filename);
        
        $users_json = json_decode($data);
        $users = [];
        foreach ($users_json as $user_item) {
            # code...
            
            $user = new User();
            $user->setFullName($user_item->fullName)
            ->setCivility($user_item->civility)
            ->setEmail($user_item->email)
            ->setCivility($user_item->civility)
            ->setPassword('123456')
            ;
           
            $users[] = $user;
            $manager->persist($user);
        }

        $categories = [
          "Robes", 
          "Jupes",
          "Culote",
          "Pantalons femmes", 
          "chemises femmes"
      ];
      
      foreach ($categories as $name) {
          # code...
          
          $category = new Category();
          $category->setName($name)
          ;
         
          $manager->persist($category);
      }
   

    $manager->flush();
  }
}
