<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Product;

/**
 * @Route("/api", name="api_")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/product/view", name="product_view_all", methods={"GET"})
     */
    public function viewAction(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $viewAllProduct = $em->getRepository(Product::class)
            ->findBy([
                "action" => ['I','U']
            ]);
        
        return $this->json($viewAllProduct);
    }

    /**
     * @Route("/product/view/{:id}", name="product_view_by_id", methods={"GET"})
     */
    public function viewByIdAction(ManagerRegistry $doctrine,$id): Response
    {
        $em = $doctrine->getManager();
        $viewByIdProduct = $em->getRepository(Product::class)
            ->findOneBy([
                "id" => $id,
                "action" => ['U','I']
            ]);

        if(!$viewByIdProduct){
            $viewByIdProduct['response']['failed'] = "Product not found";
        }

        return $this->json($viewByIdProduct);
    }

    /**
     * @Route("/product/new", name="product_new", methods={"POST"})
     */
    public function newAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $em = $doctrine->getManager();

        $checkProduct = $em->getRepository(Product::class)
            ->findOneBy([
                'name' => $data['name']
            ]);

        if($checkProduct){
            $product['response']['failed'] = "Product Already Exist at id : ".$checkProduct->getId();
        }
        else{
            $product = new Product();
            $product->setName($data['name']);
            $product->setCategoryId($data['categoryId']);
            $product->setPrice($data['price']);
            $product->setAction('I');
            $product->setAddTime(new \Datetime());
    
            $em->persist($product);
            $em->flush();

            $message['response']['success'] = 'Product berhasil ditambahkan..';
        }

        return $this->json($message);
    }

    /**
     * @Route("/product/edit", name="product_edit", methods={"PUT"})
     */
    public function editAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $product = $em->getRepository(Product::class)
            ->findOneBy([
                'id' => $data['id']
            ]);

        if(!$product){
            $product['response']['failed'] = "Product not found";
        } else {
            $product->setName($data['name']);
            $product->setAction('U');
            $product->setAddTime(new \DateTime());
            $product->setPrice($data['price']);
            $product->setCategoryId($data['categoryId']);
            $em->persist($product);
            $em->flush();
        }
        
        $message['response']['success'] = 'Success Update '.$product->getName().' Data';
        return $this->json($message);
    }

    /**
     * @Route("/product/delete", name="product_delete", methods={"POST"})
     */
    public function deleteAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $product = $em->getRepository(Product::class)
            ->findOneBy([
                'id' => $data['id']
            ]);

        if(!$product){
            $product['response']['failed'] = "Product not found";
        }

        $product->setAction('D');
        $em->persist($product);
        $em->flush();

        $message['response']['success'] = "success delete data";
        return $this->json($message);
    }
}
