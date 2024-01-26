<?php

namespace App\Controller;

use App\Repository\CollectionRepository;
use App\Repository\PageRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SettingRepository;
use App\Repository\SlidersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private $repoProduct;
    private $repoCategory;

    public function __construct(ProductRepository $repoProduct, CategoryRepository $repoCategory)
    {
        $this->repoProduct = $repoProduct;
    }

    #[Route('/', name: 'app_home')]
    public function index(
        SettingRepository $settingRepo,
        Request $request,
        SlidersRepository $slidersRepo,
        CategoryRepository $categoryRepo, 
        CollectionRepository $collectionRepo,
        PageRepository $pageRepo
    ): Response {
        $session = $request->getSession();
        $data = $settingRepo->findAll();
        $sliders = $slidersRepo->findAll();
        $collections = $collectionRepo->findBy(['isMega'=>true]);
        $categories = $categoryRepo->findBy(['isMega'=>true]);
        $megaCollections = $collectionRepo->findBy(['isMega'=>true]);
        
        $session->set("setting", $data[0]);

        $headerPages = $pageRepo->findby(['isHead'=>true]); 
        $footerPages = $pageRepo->findby(['isFoot'=>true]); 

        $session->set("headerPages", $headerPages);
        $session->set("footerPages", $footerPages);
        $session->set("categories", $categories);
        $session->set("megaCollections", $megaCollections);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'sliders' => $sliders,
            'collections' => $collections,
            'productsBestSeller' => $this->repoProduct->findBy(['isBestSeller'=>true]),
            'productsNewArrival' => $this->repoProduct->findBy(['isNewArrival'=>true]),
            'productsFeatured' => $this->repoProduct->findBy(['isFeatured'=>true]),
            'productsSpecialOffer' => $this->repoProduct->findBy(['isSpecialOffer'=>true])
        ]);
    }

    #[Route('/product/{slug}', name: 'app_product_by_slug')]
    public function showProduct(string $slug)
    {
        $product = $this->repoProduct->findOneBy(['slug'=>$slug]);

        if(!$product){
            // error
            return $this->redirectToRoute('app_error');
        }

        return $this->render('product/show_product_by_slug.html.twig', [
            'product'=> $product
        ]);


    }
    #[Route('/product/get/{id}', name: 'app_product_by_id')]
    public function getProductById(string $id)
    {
        $product = $this->repoProduct->findOneBy(['id'=>$id]);
        $category = $this->repoCategory->findOneBy('product.id');

        if(!$product){
            // error
            return $this->json(false);
        }

        /* $relatedProductId = $product->getRelatedProducts(); */
    
        return $this->json([
            'id'=>$product->getId(),
            'name'=>$product->getName(),
            'imageUrls'=>$product->getImageUrls(),
            'soldePrice'=>$product->getSoldePrice(),
            'regularPrice'=>$product->getRegularPrice(),
            'relatedProducts'=>$product->getRelatedProducts(),
            'nameCategory'=>$category->getProducts()
        ]);


    }
    #[Route('/error', name: 'app_error')]
    public function errorPage()
    {
        return $this->render('page/not-found.html.twig', [
            'controller_name' => 'PageController'
        ]);

    }

}
