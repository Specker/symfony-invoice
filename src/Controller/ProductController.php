<?php

namespace App\Controller;


use App\Entity\Product;
use App\Form\CreateProductType;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/product', name: 'product.')]
class ProductController extends AbstractController
{
//    Displays List of all Products existing in database
    #[Route('/', name: 'index')]
    public function index(ProductRepository $productRepository): Response
    {
//        Gets all Products from the repository
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig',[ 'products' => $products ]);
    }
//    Displays information about Product with given id
    #[Route('/get/{id}', name: 'getProduct')]
    public function showProduct(ProductRepository $productRepository, Request $request): Response
    {
//        Gets id of the company from URL request
        $id = $request->get('id');
//        Gets Product object
        $product = $productRepository->find($id);

        return $this->render('product/about.html.twig',[ 'product' => $product]);
    }
//    Creates Product object and saves it in database
    #[Route('/post', name: 'postProduct')]
    public function createProduct(ManagerRegistry $doctrine,Request $request): Response
    {
//       Creates new empty Product object
        $product = new Product();
//       Creates form which will fill Product object
        $form = $this->createForm(CreateProductType::class, $product);
        $form->handleRequest($request);
//        When form gets submitted
        if($form->isSubmitted()){
//            Gets entityManager
            $em = $doctrine->getManager();
//            Saves object into database
            $em->persist($product);
            $em->flush();
//            Displays message that Product post was successful
            $this->addFlash('success', 'Product was created');
//            Redirects to list of all Products
            return $this->redirect($this->generateUrl('product.index'));
        }
//        Renders form
        return $this->render('product/createForm.html.twig', ['form' => $form->createView()]);
    }
//    Removes Product from database
    #[Route('/remove/{id}', name: 'delProduct')]
    public function deleteProduct(ProductRepository $productRepository,Request $request,ManagerRegistry $doctrine,): Response
    {
        $id = $request->get('id');
        $product = $productRepository->find($id);

        $em = $doctrine->getManager();
//        Removes object from database
        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'Product '.$product->getName().' was removed');

        return $this->redirect($this->generateUrl('product.index'));
    }

}
