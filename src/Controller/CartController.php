<?php

namespace App\Controller;

use App\Services\CartService;
use App\Repository\CarrierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    public function __construct(
        private CartService $cartService, 
    ) {
        $this->cartService = $cartService;
    }
    

    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        $cart = $this->cartService->getCartDetails();
        
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart
        ]);
    }

    #[Route('/cart/add/{productId}/{count}', name: 'app_add_to_cart')]
    public function addToCart(string $productId, $count = 1): Response
    {
        $this->cartService->addToCart($productId,$count);
        return $this->redirectToRoute("app_cart");
         
    }

   #[Route('/cart/remove/{productId}/{count}', name: 'app_remove_to_cart')]
    public function removeToCart(string $productId, $count = 1): Response
    {
        $this->cartService->removeToCart($productId,$count);
        return $this->redirectToRoute("app_cart");
        
    }

    #[Route('/cart/get', name: 'app_get_cart')]
    public function getCart(): Response
    {
        return $this->redirectToRoute("app_cart");
        
    }

    /* #[Route('/cart/carrier', name: 'app_update_cart_carrier', methods: ["POST"])]
    public function updateCartCarrier(Request $req): Response
    {
        $id = $req->getPayload()->get("carrierId");
        
        $carrier = $this->carrierRepo->findOneById($id);

        if(!$carrier){
            return $this->redirectToRoute("app_home");
        }
        $this->cartService->update("carrier", [
            "id"=> $carrier->getId(),
            "name"=> $carrier->getName(),
            "description"=> $carrier->getDescription(),
            "price"=> $carrier->getPrice(),
        ]);

        return $this->redirectToRoute("app_cart");

        // return $this->json($cart);
        
    } */
}
