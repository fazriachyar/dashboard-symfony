<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\CheckoutInfo;

/**
 * @Route("/api", name="api_")
 */
class CheckoutController extends AbstractController
{
    /**
     * @Route("/checkout/new", name="checkout_new", methods={"POST"})
     */
    public function newAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $em = $doctrine->getManager();

        $checkCartInfo = $em->getRepository(CheckoutInfo::class)
            ->findOneBy([
                'cartInfoId' => $data['cartInfoId'],
                'status' => 'Waiting for payment',
                'action' => ['U','I']
            ]);
        if($checkCartInfo){
            $message['response']['failed'] = "Anda sudah pernah checkout Cart ini, harap selesaikan pembayaran anda.";
        }
        else{
            $checkout = new CheckoutInfo();
            $checkout->setCustomerId($this->getUser()->getId());
            $checkout->setCustomerContact($data['contact']);
            $checkout->setCustomerAddress($data['customerAddress']);
            $checkout->setCartInfoId($data['cartInfoId']);
            $checkout->setStatus('Waiting for payment');
            $checkout->setAction('I');
            $checkout->setAddTime(new \Datetime());
            $checkout->setOrderNote($data['orderNote']);
    
            $em->persist($checkout);
            $em->flush();
    
            $setOrderId = $em->getRepository(CheckoutInfo::class)
                ->findOneBy([
                    'id' => $checkout->getId()
                ]);
            $setOrderId->setOrderId("ORD"."-".date("dmY")."-".$this->getUser()->getId()."-".$checkout->getId());
    
            $em->persist($setOrderId);
            $em->flush();
    
            $message['response']['success'] = 'Checkout berhasil,Mohon lanjutkan pembayaran menggunakan kode transaksi : '.$setOrderId->getOrderId();
        }

        return $this->json($message);
    }

    /**
     * @Route("/checkout/cancel", name="checkout_cancel", methods={"POST"})
     */
    public function cancelAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $em = $doctrine->getManager();

        $checkCartInfo = $em->getRepository(CheckoutInfo::class)
            ->findOneBy([
                'id' => $data['id'],
                'action' => ['U','I']
            ]);
        if(!$checkCartInfo){
            $message['response']['failed'] = "Transaksi tidak ditemukan.";
        }
        elseif($checkCartInfo->getStatus() == "Paid"){
            $message['response']['failed'] = "Transaksi yang sudah dibayar tidak bisa dibatalkan.";
        }
        else{
            $checkCartInfo->setStatus('Canceled');
            $checkCartInfo->setAction('D');
            $checkCartInfo->setAddTime(new \Datetime());

            $em->persist($checkCartInfo);
            $em->flush();

            $message['response']['success'] = 'Transaksi berhasil dibatalkan';
        }

        return $this->json($message);
    }

    /**
     * @Route("/checkout/payment", name="checkout_payment", methods={"POST"})
     */
    public function paymentAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $em = $doctrine->getManager();

        $checkCartInfo = $em->getRepository(CheckoutInfo::class)
            ->findOneBy([
                'id' => $data['id'],
                'action' => ['U','I']
            ]);
        if(!$checkCartInfo){
            $message['response']['failed'] = "Transaksi tidak ditemukan.";
        }
        elseif($checkCartInfo->getStatus() == "Paid"){
            $message['response']['failed'] = "Transaksi sudah dibayar.";
        }
        else{
            $checkCartInfo->setPaymentCode($data['paymentCode']);
            $checkCartInfo->setStatus('Paid');
            $checkCartInfo->setAction('U');
            $checkCartInfo->setAddTime(new \Datetime());

            $em->persist($checkCartInfo);
            $em->flush();

            $message['response']['success'] = 'Pembayaran Berhasil';
        }

        return $this->json($message);
    }

    /**
     * @Route("/checkout/history", name="checkout_history", methods={"GET"})
     */
    public function viewHistoryAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();

        $checkCheckoutInfo = $em->getRepository(CheckoutInfo::class)
            ->findCheckoutInfoHistoryByCustomerId($this->getUser()->getId());
        if(!$checkCheckoutInfo){
            $message['response']['success'] = 'Anda belum pernah melakukan Checkout';
            return $this->json($message);
        }

        return $this->json($checkCheckoutInfo);
    }

     /**
     * @Route("/checkout/view", name="checkout_view", methods={"GET"})
     */
    public function viewAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();

        $checkCheckoutInfo = $em->getRepository(CheckoutInfo::class)
            ->findCheckoutInfoByCustomerId($this->getUser()->getId());
        if(!$checkCheckoutInfo){
            $message['response']['success'] = 'Anda belum pernah melakukan Checkout';
            return $this->json($message);
        }

        return $this->json($checkCheckoutInfo);
    }
}
