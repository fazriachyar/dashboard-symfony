<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Category;

/**
 * @Route("/api", name="api_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/category/view", name="category_view_all", methods={"GET"})
     */
    public function viewAction(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $viewAllCategory = $em->getRepository(Category::class)
            ->findBy([
                "action" => ['I','U']
            ]);
        
        return $this->json($viewAllCategory);
    }

    /**
     * @Route("/category/view/{:id}", name="category_view_by_id", methods={"GET"})
     */
    public function viewByIdAction(ManagerRegistry $doctrine,$id): Response
    {
        $em = $doctrine->getManager();
        $viewByIdCategory = $em->getRepository(Category::class)
            ->findOneBy([
                "id" => $id,
                'action' => ['U','I']
            ]);

        if(!$viewByIdCategory){
            $viewByIdCategory['response']['failed'] = 'Data id '.$id.' not found !';
        }

        return $this->json($viewByIdCategory);
    }

    /**
     * @Route("/category/new", name="category_new", methods={"POST"})
     */
    public function newAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $em = $doctrine->getManager();

        $checkCategory = $em->getRepository(Category::class)
            ->findOneBy([
                "name" => $data['name'],
                "action" => ['U','I']
            ]);

        if($checkCategory){
            $message['response']['failed'] = $data['name'].' Category Already Exist !';
        } else {
            $category = new Category();
            $category->setName($data['name']);
            $category->setAction('I');
            $category->setAddTime(new \Datetime());
    
            $em->persist($category);
            $em->flush();

            $message['response']['success'] = 'Success add '.$data['name'];
        }
        return $this->json($message);
    }

    /**
     * @Route("/category/edit", name="category_edit", methods={"PUT"})
     */
    public function editAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $category = $em->getRepository(Category::class)
            ->findOneBy([
                'id' => $data['id'],
                'action' => ['U','I']
            ]);

        if(!$category){
            $category['response']['failed'] = "category ".$data['id']." not found";
        } else {
            $category->setName($data['name']);
            $category->setAction('U');
            $category->setAddTime(new \DateTime());

            $em->persist($category);
            $em->flush();
            
            $message['response']['success'] = 'Success Update '.$category->getName().' Data';
        }
        
        return $this->json($message);
    }

    /**
     * @Route("/category/delete", name="category_delete", methods={"POST"})
     */
    public function deleteAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        $category = $em->getRepository(Category::class)
            ->findOneBy([
                'id' => $data['id'],
                'action' => ['U','I']
            ]);

        if(!$category){
            $message['response']['failed'] = 'Data id '.$data['id'].' not found !';
        } else {
            $category->setAction('D');
            $em->persist($category);
            $em->flush();

            $message['response']['success'] = 'Success Delete '.$category->getName().' Data';
        }
            
        return $this->json($message);
    }
}
