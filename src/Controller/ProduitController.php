<?php namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Form\PanierType;
use App\Form\ProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProduitController extends AbstractController {

    /**
     * @Route("/", name="produit")
     */
    public function index(Request $request, TranslatorInterface $translator) {
        $em=$this->getDoctrine()->getManager();
        $produit=new Produit();
        $form=$this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $photo=$form->get('photoUpload')->getData();

            if($photo) {
                $nomPhoto=uniqid() . '.'. $photo->guessExtension();

                try {
                    $photo->move($this->getParameter('upload_dir'),
                        $nomPhoto);
                }

                catch(FileException $e) {
                    return $this->redirectToRoute('produit');
                }

                $produit->setPhoto($nomPhoto);
            }

            $em->persist($produit);
            $em->flush();

            $this->addFlash("success", $translator->trans('file.produit.ok1'));
            return $this->redirectToRoute("produit");
        }

        $produits=$em->getRepository(Produit::class)->findAll();
        return $this->render('produit/index.html.twig', [ 'produits'=> $produits,
            'form_produit_add'=> $form->createView(),
            ]);
    }


    /**
     * @Route("/produit/update/{id}", name="produit_update")
     */
    public function update(Request $request, Produit $produit=null, TranslatorInterface $translator) {

        if($produit !=null) {
            $form=$this->createForm(ProduitType::class, $produit);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $em=$this->getDoctrine()->getManager();
                $em->persist($produit);
                $em->flush();

                $this->addFlash("sucess", $translator->trans('file.produit.ok2'));
                return $this->redirectToRoute('produit');
            }

            return $this->render('produit/produit_update.html.twig', [ 'produit'=> $produit,
                'form_produit_update'=> $form->createView(),
                ]);
        }

        else {
            $this->addFlash("danger", $translator->trans('file.produit.error1'));
            return $this->redirectToRoute('produit');
        }
    }


    /**
     * @Route("/produit/{id}/delete", name="produit_delete")
     */
    public function delete(Produit $produit=null, TranslatorInterface $translator) {
        if($produit !=null) {
            $em=$this->getDoctrine()->getManager();
            $em->remove($produit);
            $em->flush();

            $this->addFlash("success", $translator->trans('file.produit.ok3'));
            return $this->redirectToRoute('produit');
        }
    }


    /**
     * @Route("produit/{id}", name="mon_produit")
     */
    public function produit(Request $request, Produit $produit=null, TranslatorInterface $translator) {

        if($produit !=null) {

            $em=$this->getDoctrine()->getManager();
            $cart=new Panier();
            $form=$this->createForm(PanierType::class, $cart);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                if($produit->getQuantite() >=$cart->getQuantite() && $cart->getQuantite() > 0) {

                    $cart->setDateAjout(new \DateTime());
                    $cart->setEtat(false);
                    $cart->setProduit($produit);
                    $em->persist($cart);
                    $em->flush();

                    $this->addFlash("success", $translator->trans('file.produit.ok4'));
                    return $this->redirectToRoute('panier');

                }

                else {
                    $this->addFlash("danger", $translator->trans('file.produit.error2'));
                }
            }

            return $this->render('produit/produit.html.twig', [ 'produit'=> $produit,
                'cart'=> $cart,
                'form_panier_add'=> $form->createView(),
                ]);
        }

        else {
            $this->addFlash("danger", $translator->trans('file.produit.error1'));
            return $this->redirectToRoute('produit');
        }
    }
}