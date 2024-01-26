<?php 
namespace App\Services;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;    


class CartService {

    private $session;

    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepo
    ) {
        // Accessing the session in the constructor is *NOT* recommended, since
        // it might not be accessible yet or lead to unwanted side-effects
        $this->session = $requestStack->getSession();
        $this->productRepo = $productRepo;
    }

     public function getCart()
    {
        return $this->session->get("cart", []); //Retourne un tableau vide si la session n'existe pas
    }

    //Ajout des articles
    public function addToCart($productId, $count = 1,)
    {
        // [
        //     '1' => 3,
        //     '25' => 1,
        // ] 
        
        $cart = $this->getCart();  //Récupérer le panier courant

        if(!empty($cart[$productId])){
            // product exist in cart
            $cart[$productId] += $count;
        }else{
            // product not exist
            $cart[$productId] = $count;
        }

        $this->updateCart($cart); //Session mise à jour du panier
    }

    //Mise à jour du panier si ajout ou suppression
    public function updateCart($cart)
    {
        return $this->session->set("cart", $cart);
    } 

    
    //Suppression d'articles
    public function removeToCart($productId, $count = 1)
    {
        $cart = $this->getCart();

        if(isset($cart[$productId])){
            if($cart[$productId]  <= $count){
                unset($cart[$productId]); //Retire les éléments du panier
            }else{
                $cart[$productId] -= $count;
            }

            $this->updateCart($cart);
        }

    }

    //Netoyer le panier
    public function clearCart()
    {
        $this->updateCart([]);
    }
    
    
    //Détail du panier selon chaque articles
    public function getCartDetails()
    {
        // [
        //     "items" => [
        //             [
        //                 'product' => [],
        //                 'quantity' => 2,
        //                 'taxe' => 20,
        //                 'sub_total' => 199,
        //             ]
        //         ],
        //          "cart_sub_total" => 199
        //          'cart_count' => 0,
        // ]

        //Récupération des articles du panier
        $cart = $this->getCart();

        //Initialisation des variables
        $result = [
            'items' => [],
            'sub_total' => 0,
            'cart_count' => 0,
        ];

        $tva = 0;
        $ttc=0;
        $sub_total = 0;
        $sub_total_tva  = 0;
        $sub_total_ttc = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $this->productRepo->find($productId);
            if($product){
                $current_sub_total = $product->getSoldePrice()*$quantity;
                $tva = ($current_sub_total*20) / 100;
                $ttc =  (($current_sub_total + $tva));
                $sub_total_tva += $tva;
                $sub_total_ttc += $ttc;
                $sub_total += $current_sub_total;
                
               
                $result['items'][] = [
                    'product' => [
                        'id'=>$product->getId(),
                        'name'=>$product->getName(),
                        'slug'=>$product->getSlug(),
                        'imageUrls'=>$product->getImageUrls(),
                        'soldePrice'=>$product->getSoldePrice(),
                        'regularPrice'=>$product->getRegularPrice(),
                    ],
                    'quantity' => $quantity,
                    'tva' => $tva,
                    'ttc' => $ttc,  
                    'sub_total' => $current_sub_total,
                    'sub_total_tva' => $sub_total_tva,
                    'sub_total_ttc' => $sub_total_ttc,
                ];
                $result['sub_total'] = $sub_total;
                $result['sub_total_tva'] = $sub_total_tva;
                $result['sub_total_ttc'] = $sub_total_ttc;
                /* $result['cart_count'] += $quantity; */
                

            }else{
                unset($cart[$productId]);
                $this->updateCart($cart);
            }
        }

        //dd($result);
        return $result;
    }

    


}