<?php

namespace App\Controller;

use App\Entity\Supplier;
use App\Form\SupplierType;
use App\Repository\SupplierRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin")
 */
class SupplierController extends AbstractController
{
    /**
     * @Route("/supplier", name="supplier")
     */
    public function index(Request $request, PaginatorInterface $paginator, SupplierRepository $supplierRepo): Response
    {
        $pagination = $paginator->paginate(
            $supplierRepo->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('admin/supplier/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="supplier_add", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $supplier = new Supplier();
        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($supplier);
            $entityManager->flush();

            return $this->redirectToRoute('supplier');
        }

        return $this->render('admin/supplier/new.html.twig', [
            'supplier' => $supplier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="supplier_show", methods={"GET"})
     */
    public function show(Supplier $supplier): Response
    {
        return $this->render('admin/supplier/show.html.twig', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="supplier_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Supplier $supplier): Response
    {
        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('supplier');
        }

        return $this->render('admin/supplier/edit.html.twig', [
            'supplier' => $supplier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="supplier_delete")
     */
    public function delete(Request $request, $id): Response
    {
        $supplier = $this->getDoctrine()->getRepository(Supplier::class)->find($id);
        if ($supplier != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($supplier);
            $entityManager->flush();
        }
        $response = new Response();
        $response->send();

        return $this->redirectToRoute('supplier');
    }
}
