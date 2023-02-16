<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\CartInfo;
use App\Entity\CartItem;

/**
 * @Route("/api", name="api_")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart_view_by_customer_id", methods={"GET"})
     */
    public function viewCartByCustomerIdAction(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $findAllCart = $em->getRepository(CartItem::class)
            ->findCartByCustomerId($this->getUser()->getId());
        
        return $this->json($findAllCart);
    }

    /**
     * @Route("/cart/view/{id}", name="cart_view_by_id", methods={"GET"})
     */
    public function viewByIdAction(ManagerRegistry $doctrine,int $id): Response
    {
        $em = $doctrine->getManager();
        $viewAllProduct = $em->getRepository(CartItem::class)
            ->findOneBy([
                "id" => $id,
                "action" => ['I','U']
            ]);
        
        return $this->json($viewAllProduct);
    }

    /**
     * @Route("/cart/new", name="cart_new", methods={"POST"})
     */
    public function newAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $em = $doctrine->getManager();

        $checkCartInfo = $em->getRepository(CartInfo::class)
            ->findOneBy([
                'customerId' => $this->getUser()->getId(),
                'action' => (['U','I'])
            ]);
        if(!$checkCartInfo){
            $cartInfo = new CartInfo();
            $cartInfo->setCustomerId($this->getUser()->getId());
            $cartInfo->setAction('I');
            $cartInfo->setAddTime(new \Datetime());
    
            $em->persist($cartInfo);
            $em->flush();

            foreach($data['item'] as $key => $item)
            {
                $cartItem = new CartItem();
                $cartItem->setProductId($item['productId']);
                $cartItem->setProductQuantity($item['quantity']);
                $cartItem->setCartInfoId($cartInfo->getId());
                $cartItem->setAction('I');
                $cartItem->setAddTime(new \Datetime());
    
                $em->persist($cartItem);
            }
            $em->flush();
    
        }
        else{
            foreach($data['item'] as $key => $item)
            {
                $cartItem = new CartItem();
                $cartItem->setProductId($item['productId']);
                $cartItem->setProductQuantity($item['quantity']);
                $cartItem->setCartInfoId($cartInfo->getId());
                $cartItem->setAction('I');
                $cartItem->setAddTime(new \Datetime());
    
                $em->persist($cartItem);
            }
            $em->flush();
        }

        $message['response']['success'] = 'Cart Successfully Added !';
        return $this->json($message);
    }

    /**
     * @Route("/cart/edit", name="cart_edit", methods={"PUT"})
     */
    public function editAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $em = $doctrine->getManager();

        $cartInfo = $em->getRepository(CartInfo::class)
            ->findOneBy([
                'id' => $data['id'],
                "action" => ['I','U']
            ]);

        $cartInfo->setCustomerId($this->getUser()->getId());
        $cartInfo->setAction('U');
        $cartInfo->setAddTime(new \Datetime());

        $em->persist($cartInfo);
        $em->flush();
        
        $deleteCartItem = $em->getRepository(CartItem::class)
            ->removeCartItem($data['id']);

        foreach($data['item'] as $key -> $item)
        {
            $cartItem = new CartItem();
            $cartItem->setProductId($item['productId']);
            $cartItem->setProductQuantity($item['quantity']);
            $cartItem->setCartInfoId($cartInfo->getId());
            $cartItem->setAction('U');
            $cartItem->setAddTime(new \Datetime());

            $em->persist($cartItem);
        }
        $em->flush();

        $message['response']['success'] = 'Cart Successfully Updated !';
        return $this->json($message);
    }

    /**
     * @Route("/cart/delete", name="cart_delete", methods={"POST"})
     */
    public function deleteAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $checkCartItem = $em->getRepository(CartItem::class)
            ->findOneBy([
                'id' => $data['id']
            ]);

        if(!$checkCartItem){
            $message['response']['failed'] = "Cart Item Not Found";
        }
        else{
            $checkCartItem->setAction('D');
            $em->persist($checkCartItem);
            $em->flush();
    
            $message['response']['success'] = "Success Delete Data";
        }

        return $this->json($message);
    }
}
