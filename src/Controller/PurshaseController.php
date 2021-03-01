<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Purshase;
use App\Form\PurshaseType;
use App\Entity\PurshaseDetails;
use App\Repository\ProductRepository;
use App\Repository\PurshaseRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/purshase")
 */
class PurshaseController extends AbstractController
{
    /**
     * @Route("/", name="purshase")
     */
    public function index(Request $request, PaginatorInterface $paginator, PurshaseRepository $purshaseRepository): Response
    {
        $pagination = $paginator->paginate(
            $purshaseRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('admin/purshase/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="purshase_add", methods={"GET","POST"})
     */
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $purshase = new Purshase();
        $form = $this->createForm(PurshaseType::class, $purshase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productName = $request->request->get('productName');
            $quantity = $request->request->get('quantity');
            $price = $request->request->get('price');
            $amount = $request->request->get('amount');

            $gross_amount = $request->request->get('gross-amount');
            $purshase->setStatus("Due");
            $purshase->setAmount($gross_amount);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($purshase);

            for ($i = 0; $i < count($productName); $i++) {
                $purshaseDetails = new PurshaseDetails();
                $purshaseDetails->setProductName($productName[$i]);
                $product = $productRepository->findProductByNameV2($productName[$i]);
                $product->setQuantity($product->getQuantity() + $quantity[$i]);
                $purshaseDetails->setQuantity($quantity[$i]);
                $purshaseDetails->setPrice($price[$i]);
                $purshaseDetails->setAmount($amount[$i]);
                $purshaseDetails->setPurshase($purshase);
                $entityManager->persist($purshaseDetails);
                $entityManager->persist($product);
            }

            $entityManager->flush();

            return $this->redirectToRoute('purshase');
        }

        return $this->render('admin/purshase/new.html.twig', [
            'form' => $form->createView(),
            'products' => $productRepository->findAll()
        ]);
    }

    /**
     * @Route("/update-status/{id}", name="update-status")
     */
    public function updateStatus(Request $request, int $id, PurshaseRepository $purshaseRepository)
    {
        $status = $request->request->get('status');
        $purshase = $purshaseRepository->find($id);
        if ($status != null && $status != "") {

            $purshase->setStatus($status);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($purshase);
            $entityManager->flush();

            return $this->redirectToRoute('purshase_show', ['id' => $id]);
        }
    }

    /**
     * @Route("/pdf/{id}", name="purshase_pdf", methods={"GET","POST"})
     */
    public function indexPDF(Request $request, int $id, PurshaseRepository $purshaseRepository)
    {
        $purshase = $purshaseRepository->find($id);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('admin/purshase/pdf.html.twig', [
            'purshase' => $purshase
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        if ($request->get('l') == 1) {
            // Output the generated PDF to Browser (force download)
            $dompdf->stream('facture-' . $purshase->getId() . '.pdf', [
                "Attachment" => 0
            ]);
        } else {
            // Output the generated PDF to Browser (force download)
            $dompdf->stream('facture-' . $purshase->getId() . '.pdf', [
                "Attachment" => true
            ]);
        }
    }

    /**
     * @Route("/newv2")
     */
    public function newv2(Request $request, ProductRepository $productRepository): Response
    {

        return $this->render('admin/purshase/newv2.html.twig', [
            'products' => $productRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="purshase_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Purshase $purshase): Response
    {
        $form = $this->createForm(PurshaseType::class, $purshase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('purshase');
        }

        return $this->render('admin/purshase/edit.html.twig', [
            'purshase' => $purshase,
            'purshaseForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="purshase_show", methods={"GET"})
     */
    public function show(Purshase $purshase): Response
    {
        return $this->render('admin/purshase/show.html.twig', [
            'purshase' => $purshase,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="purshase_delete")
     */
    public function delete(ProductRepository $productRepository, Request $request, $id): Response
    {
        $purshase = $this->getDoctrine()->getRepository(Purshase::class)->find($id);
        if ($purshase != null) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($purshase->getPurshaseDetails() as $key => $value) {
                $product = $productRepository->findProductByNameV2($value->getProductName());
                $product->setQuantity($product->getQuantity() - $value->getQuantity());
                $entityManager->persist($product);
            }
            
            $entityManager->remove($purshase);
            $entityManager->flush();
        }
        $response = new Response();
        $response->send();

        return $this->redirectToRoute('purshase');
    }
}
