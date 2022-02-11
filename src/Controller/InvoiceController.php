<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceProduct;
use App\Form\AddProductToInvoiceType;
use App\Form\CreateInvoiceType;
use App\Repository\InvoiceProductRepository;
use App\Repository\InvoiceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/invoice', name: 'invoice.')]
class InvoiceController extends AbstractController
{
//    Displays List of all invoices existing in database
    #[Route('/', name: 'index')]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
//        Gets all invoices from the repository
        $invoices = $invoiceRepository->findAll();

        return $this->render('invoice/index.html.twig',[ 'invoices' => $invoices ]);
    }

//    Displays information about Invoice with given id
    #[Route('/get/{id}', name: 'getInvoice')]
    public function showInvoice(InvoiceRepository $invoiceRepository, InvoiceProductRepository $invoiceProductRepository , Request $request): Response
    {
//        Gets id of the Invoice from URL request
        $id = $request->get('id');
//        Gets Invoice object
        $invoice = $invoiceRepository->find($id);
//        Fetches all Products to this Invoice
        $invoiceProducts = $invoiceProductRepository->findBy(['invoice_id' => $id]);
        return $this->render('invoice/about.html.twig',[ 'invoice' => $invoice, 'invoiceProducts' => $invoiceProducts]);
    }

//    Adds new Product to the existing Invoice
    #[Route('/get/{id}/addProduct', name: 'invoiceAddProduct')]
    public function showInvoiceAddProduct(InvoiceRepository $invoiceRepository, InvoiceProductRepository $invoiceProductRepository ,ManagerRegistry $doctrine,Request $request): Response
    {
//        Creates new empty InvoiceProduct object
        $invoiceProduct = new InvoiceProduct();

        $id = $request->get('id');
//        Fetches Invoice to which product is going to be added
        $invoice = $invoiceRepository->find($id);
//       Creates form which will fill InvoiceProduct object
        $form = $this->createForm(AddProductToInvoiceType::class, $invoiceProduct);
        $form->handleRequest($request);
//        When form gets submitted
        if($form->isSubmitted()){
//            Gets Product object which is getting added
            $product = $form->get('product_id')->getData();
//            Gets entityManager
            $em = $doctrine->getManager();
//            Sets InvoiceID variable within InvoiceProduct with Invoice Object
            $invoiceProduct->setInvoiceId($invoice);
//            Calculates TotalPrice of InvoiceProduct based on the Price of the Product object
//            and Quantity
            $invoiceProduct->setTotalPrice($invoiceProduct->getQuantity()*$product->getPrice());
//            Updates TotalPrice of an Invoice with a new InvoiceProduct TotalPrice
            $invoice->setTotalPrice($invoice->getTotalPrice() + $invoiceProduct->getTotalPrice());
//            Saves and Updates objects in Database
            $em->persist($invoiceProduct);
            $em->persist($invoice);
            $em->flush();

            $this->addFlash('success', 'Product was added Successfully');
//            Checks which button in form was Clicked
//            When 'saveAndAdd' button is clicked it will Redirect to add Product to Invoice Page
//            When other button is clicked it redirects to details about Invoice Page
            if($form->get('saveAndAdd')->isClicked()){
                return $this->redirect($this->generateUrl('invoice.invoiceAddProduct', ['id'=>$invoice->getId()]));
            }else{
                return $this->redirect($this->generateUrl('invoice.getInvoice', ['id'=>$id]));
            }


        }
        return $this->render('invoice/createFormEmpty.html.twig', ['form' => $form->createView()]);
    }

//    Deletes Product from Invoice
    #[Route('/get/{id}/delProduct/{invProID}', name: 'invoiceDelProduct')]
    public function showInvoiceDelProduct(InvoiceRepository $invoiceRepository, InvoiceProductRepository $invoiceProductRepository ,ManagerRegistry $doctrine,Request $request): Response
    {

        $id = $request->get('id');
        $invProID = $request->get('invProID');
        $invoice = $invoiceRepository->find($id);
        $invoiceProduct = $invoiceProductRepository->find($invProID);

        $em = $doctrine->getManager();
//        Updates Invoices TotalPrice
        $invoice->setTotalPrice($invoice->getTotalPrice()-$invoiceProduct->getTotalPrice());

//        Removes InvoiceProduct object from Database
        $em->remove($invoiceProduct);
//        Updates Invoice Object with new TotalPrice in database
        $em->persist($invoice);
        $em->flush();

        $this->addFlash('success', 'Product was removed from invoice');

        return $this->redirect($this->generateUrl('invoice.getInvoice', ['id'=>$id]));
    }
//    Creates Invoice object and saves it in database
    #[Route('/post', name: 'postInvoice')]
    public function createInvoice(ManagerRegistry $doctrine,Request $request): Response
    {
//       Creates new empty company object
        $invoice = new Invoice();
//       Creates form which will fill Invoice object
        $form = $this->createForm(CreateInvoiceType::class, $invoice);
        $form->handleRequest($request);
//        When form gets submitted
        if($form->isSubmitted()){
//            Gets entityManager
            $em = $doctrine->getManager();
//            Sets Invoice TotalPrice to 0 as no products in the Invoice are added yet
            $invoice->setTotalPrice(0);
//            Saves object into database
//            $em->persist($invoice);
            $em->persist($invoice);
            $em->flush();
//            Displays message that Invoice post was successful
            $this->addFlash('success', 'Invoice was created');
//            Redirects to Add Product to Invoice Page
            return $this->redirect($this->generateUrl('invoice.invoiceAddProduct', ['id'=>$invoice->getId()]));
        }
//        Renders form
        return $this->render('invoice/createForm.html.twig', ['form' => $form->createView()]);
    }
//    Removes Invoice from database
    #[Route('/remove/{id}', name: 'delInvoice')]
    public function deleteInvoice(InvoiceRepository $invoiceRepository,Request $request,ManagerRegistry $doctrine,): Response
    {

        $id = $request->get('id');
        $invoice = $invoiceRepository->find($id);

        $em = $doctrine->getManager();
//        Removes object from database
        $em->remove($invoice);
        $em->flush();

        $this->addFlash('success', 'Invoice '.$invoice->getId().' was removed');

        return $this->redirect($this->generateUrl('invoice.index'));
    }
}
