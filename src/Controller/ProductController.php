<?php

namespace App\Controller;

use DateTime;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/admin/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/cat/{category}", name="product_cat",defaults={"category" = null})
     */
    public function indexWithCategory(Request $request, PaginatorInterface $paginator, ProductRepository $productRepo, $category, CategoryRepository $categoryRepository): Response
    {
        $pagination = $paginator->paginate(
            $productRepo->findSomeProducts($category), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('admin/product/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/", name="product")
     */
    public function index(Request $request, PaginatorInterface $paginator, ProductRepository $productRepo, CategoryRepository $categoryRepository): Response
    {
        $pagination = $paginator->paginate(
            $productRepo->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('admin/product/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/products_ajax", name="products_ajax")
     */
    public function indexAjax(Request $request, ProductRepository $productRepo,SerializerInterface $serializer)
    {
        $productName = $request->request->get('name');

        $json = $serializer->serialize(
            $productRepo->findProductByName($productName),
            'json',
            ['groups' => 'show_product']
        );

        return new JsonResponse($json);
    }

    /**
     * @Route("/search", name="product_search")
     */
    public function search(Request $request, PaginatorInterface $paginator, ProductRepository $productRepo): Response
    {
        $value = $request->request->get('productName');
        $pagination = $paginator->paginate(
            $productRepo->findProductsByName($value), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('admin/product/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/add", name="product_add")
     */
    public function add(ProductRepository $productRepo, Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                //$safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII;', $originalFilename);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $product->setImageName($newFilename);
            }

            // On génère un date
            $product->setCreatedAt(new DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product');
        }

        return $this->render('admin/product/new.html.twig', [
            'productForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product');
        }

        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'productForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('admin/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="product_delete")
     */
    public function delete(Request $request, $id): Response
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($product != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }
        $response = new Response();
        $response->send();

        return $this->redirectToRoute('product');
    }
}
