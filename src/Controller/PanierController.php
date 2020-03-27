<?php namespace App\Controller;

use App\Entity\Panier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PanierController extends AbstractController {

    /**
     * @Route("/", name="panier")
     */
    public function index() {
        $em=$this->getDoctrine()->getManager();
        $cart=$em->getRepository(Panier::class)->findBy(['etat'=> false]);
        return $this->render('panier/index.html.twig', [ 'cart'=> $cart,
            ]);
    }


    /**
     * @Route("/buy", name="panier_buy")
     */
    public function buy(TranslatorInterface $translator) {
        $em=$this->getDoctrine()->getManager();
        $cart=$em->getRepository(Panier::class)->findBy(['etat'=> false]);

        foreach($cart as $item) {

            //Soustraction de la valeur Acheté à la valeure Totale.
            // $produit = $em->getRepository(Produit::class)->findOneBy(['id' => $item ]);
            // $produit->setQuantite(900);            
            // $em->persist($produit); 


            //Mise Etat > True
            $item->setEtat(true);
            $item->setDateAchat(new \DateTime());

            $em->persist($item);
            $em->flush();
        }

        $this->addFlash("success", $translator->trans('file.produit.ok1'));
        return $this->redirectToRoute('commande');
    }


    /**
     * @Route("/{id}/remove", name="panier_remove")
     */
    public function delete(Panier $item=null, TranslatorInterface $translator) {
        if($item !=null) {
            $em=$this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

            $this->addFlash("success", $translator->trans('file.produit.ok2'));
        }

        return $this->redirectToRoute('panier');
    }
}