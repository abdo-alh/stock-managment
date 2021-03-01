<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceDetails;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * @Route("/admin/invoice")
 */
class InvoiceController extends AbstractController
{
    /**
     * @Route("/", name="invoice")
     */
    public function index(Request $request, PaginatorInterface $paginator, InvoiceRepository $invoiceRepository): Response
    {
        $pagination = $paginator->paginate(
            $invoiceRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('admin/invoice/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/update-status/{id}", name="update-status")
     */
    public function updateStatus(Request $request, int $id, InvoiceRepository $invoiceRepository)
    {
        $status = $request->request->get('status');
        $invoice = $invoiceRepository->find($id);
        if ($status != null && $status != "") {

            $invoice->setStatus($status);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($invoice);
            $entityManager->flush();

            return $this->redirectToRoute('invoice_show', ['id' => $id]);
        }
    }

    /**
     * @Route("/pdf/{id}", name="invoice_pdf", methods={"GET","POST"})
     */
    public function indexPDF(Request $request, int $id, InvoiceRepository $invoiceRepository)
    {
        $invoice = $invoiceRepository->find($id);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('admin/invoice/pdf.html.twig', [
            'invoice' => $invoice
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        if ($request->get('l') == 1) {
            // Output the generated PDF to Browser (force download)
            $dompdf->stream('facture-' . $invoice->getId() . '.pdf', [
                "Attachment" => 0
            ]);
        } else {
            // Output the generated PDF to Browser (force download)
            $dompdf->stream('facture-' . $invoice->getId() . '.pdf', [
                "Attachment" => true
            ]);
        }
    }

    /**
     * @Route("/new", name="invoice_add", methods={"GET","POST"})
     */
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productName = $request->request->get('productName');
            $quantity = $request->request->get('quantity');
            $price = $request->request->get('price');
            $amount = $request->request->get('amount');

            $gross_amount = $request->request->get('gross-amount');
            $invoice->setStatus("Paid");
            $invoice->setAmount($gross_amount);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($invoice);

            for ($i = 0; $i < count($productName); $i++) {
                $invoiceDetails = new InvoiceDetails();
                $invoiceDetails->setProductName($productName[$i]);
                $product = $productRepository->findProductByNameV2($productName[$i]);
                $product->setQuantity($product->getQuantity() - $quantity[$i]);
                $invoiceDetails->setQuantity($quantity[$i]);
                $invoiceDetails->setPrice($price[$i]);
                $invoiceDetails->setAmount($amount[$i]);
                $invoiceDetails->setInvoice($invoice);
                $entityManager->persist($invoiceDetails);
                $entityManager->persist($product);
            }

            $entityManager->flush();


            return $this->redirectToRoute('invoice');
        }

        return $this->render('admin/invoice/new.html.twig', [
            'form' => $form->createView(),
            'products' => $productRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}", name="invoice_show", methods={"GET"})
     */
    public function show(Invoice $invoice): Response
    {
        return $this->render('admin/invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="invoice_delete")
     */
    public function delete(ProductRepository $productRepository, Request $request, $id): Response
    {
        $invoice = $this->getDoctrine()->getRepository(Invoice::class)->find($id);
        if ($invoice != null) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($invoice->getInvoiceDetails() as $key => $value) {
                $product = $productRepository->findProductByNameV2($value->getProductName());
                $product->setQuantity($product->getQuantity() + $value->getQuantity());
                $entityManager->persist($product);
            }

            $entityManager->remove($invoice);
            $entityManager->flush();
        }
        $response = new Response();
        $response->send();

        return $this->redirectToRoute('invoice');
    }
}
