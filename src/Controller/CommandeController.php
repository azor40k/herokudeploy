<?php namespace App\Controller;

use App\Entity\Panier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController {

    /**
     * @Route("/commande", name="commande")
     */
    public function index() {
        $em=$this->getDoctrine()->getManager();
        $cart=$em->getRepository(Panier::class)->findBy(['etat'=> true]);
        return $this->render('commande/index.html.twig', [ 'cart'=> $cart,
            ]);
    }
}