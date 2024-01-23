<?php

namespace App\Controller;

use App\Repository\CollectionRepository;
use App\Repository\PageRepository;
use App\Repository\SettingRepository;
use App\Repository\SlidersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        SettingRepository $settingRepo,
        Request $request,
        SlidersRepository $slidersRepo,
        CollectionRepository $collectionRepo,
        PageRepository $pageRepo
    ): Response {
        $session = $request->getSession();
        $data = $settingRepo->findAll();
        $sliders = $slidersRepo->findAll();
        $collections = $collectionRepo->findAll();
        
        $session->set("setting", $data[0]);

        $headerPages = $pageRepo->findby(['isHead'=>true]); 
        $footerPages = $pageRepo->findby(['isFoot'=>true]); 
        $session->set("headerPages", $headerPages);
        $session->set("footerPages", $footerPages);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'sliders' => $sliders,
            'collections' => $collections
        ]);
    }
}
