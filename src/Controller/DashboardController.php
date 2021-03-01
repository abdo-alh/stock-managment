<?php

namespace App\Controller;

use App\Repository\InvoiceDetailsRepository;
use App\Repository\InvoiceRepository;
use App\Repository\ProductRepository;
use App\Repository\PurshaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="admin")
     */
    public function index(InvoiceDetailsRepository $invoiceDetailsRepository, ProductRepository $productRepository, InvoiceRepository $invoiceRepository, Request $request): Response
    {
        return $this->render('admin/dashboard/dashboard.html.twig', [
            'today_invoices' => $invoiceDetailsRepository->getTodaySales(),
            'month_invoices' => $invoiceDetailsRepository->getMonthSales(),
            'month_revenu' => $invoiceRepository->getMonthllyRevenu(),
            'products_out_stock' => $productRepository->findProductLowStock(),
            'invoices' => $invoiceRepository->getAllInvoices(),
            'most_products' => $invoiceDetailsRepository->getMostProductsSales()
        ]);
    }

    /**
     * @Route("/monthlly", name="statistics")
     */
    public function monthlly_statisctics(ProductRepository $productRepository, InvoiceRepository $invoiceRepository, PurshaseRepository $purshaseRepository, Request $request): Response
    {
        return $this->render('admin/dashboard/statistics.html.twig', [
            'months_invoices' => $invoiceRepository->getMonthlly(),
            'months_purchases' => $purshaseRepository->getMonthllyPurshases(),
        ]);
    }
}
