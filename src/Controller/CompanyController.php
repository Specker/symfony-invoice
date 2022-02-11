<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\CreateCompanyType;
use App\Repository\CompanyRepository;
use App\Repository\DevsRepository;
use App\Repository\InvoiceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/company', name: 'company.')]
class CompanyController extends AbstractController
{
//    Displays List of all companies existing in database
    #[Route('/', name: 'index')]
    public function index(CompanyRepository $companyRepository): Response
    {
//        Gets all companies from the repository
        $companies = $companyRepository->findAll();

        return $this->render('company/index.html.twig',[ 'companies' => $companies ]);
    }

//    Displays information about company with given id
//    additional info are all sold and bought invoices
    #[Route('/get/{id}', name: 'getCompany')]
    public function showCompany(CompanyRepository $companyRepository,InvoiceRepository $invoiceRepository,Request $request): Response
    {
//        Gets id of the company from URL request
        $id = $request->get('id');
//        Gets company object
        $company = $companyRepository->find($id);
//        Fetches all sold invoices
        $sellInvoices = $invoiceRepository->findBy(['seller' => $id]);
//        Fetches all bought invoices
        $buyInvoices = $invoiceRepository->findBy(['buyer' => $id]);

        return $this->render('company/about.html.twig',[ 'company' => $company,'sellInvoices' => $sellInvoices,'buyInvoices' => $buyInvoices]);
    }

//    Creates company object and saves it in database
    #[Route('/post', name: 'postCompany')]
    public function createCompany(ManagerRegistry $doctrine,Request $request): Response
    {
//       Creates empty new company object
        $company = new Company();
//       Creates form which will fill company object
        $form = $this->createForm(CreateCompanyType::class, $company);
        $form->handleRequest($request);
//        When form gets submitted
        if($form->isSubmitted()){
//            Gets entityManager
            $em = $doctrine->getManager();
//            Saves object into database
            $em->persist($company);
            $em->flush();
//            Displays message that Company post was successful
            $this->addFlash('success', 'Company was created');
//            Redirects to list of all companies
            return $this->redirect($this->generateUrl('company.index'));
        }
//        Renders form
        return $this->render('company/createForm.html.twig', ['form' => $form->createView()]);
    }

//    Creates new company when called from Create Invoice Page
    #[Route('/post/fromInvoice', name: 'postCompanyFromInvoice')]
    public function createCompanyFromInvoice(ManagerRegistry $doctrine,Request $request): Response
    {
        $company = new Company();

        $form = $this->createForm(CreateCompanyType::class, $company);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            $em = $doctrine->getManager();

            $em->persist($company);
            $em->flush();

            $this->addFlash('success', 'Company was created');
//            Returns user to the Create Invoice Page
            return $this->redirect($this->generateUrl('invoice.postInvoice'));
        }

        return $this->render('company/createForm.html.twig', ['form' => $form->createView()]);
    }
//    Removes Company from database
    #[Route('/remove/{id}', name: 'delCompany')]
    public function deleteCompany(CompanyRepository $companyRepository,Request $request,ManagerRegistry $doctrine,): Response
    {
        $id = $request->get('id');
        $company = $companyRepository->find($id);

        $em = $doctrine->getManager();

//        Removes object from database
        $em->remove($company);
        $em->flush();

        $this->addFlash('success', 'Company '.$company->getName().' was removed');

        return $this->redirect($this->generateUrl('company.index'));
    }
}
