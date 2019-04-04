<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function IndexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }

    /**
     * @Route("/add", name="add")
     */
    public function AddAction()
    {
        if($_POST){
            $Article = new Article();
            $Article->setUserId($_POST['user_id']);
            $Article->setTitle($_POST['title']);
            $Article->setContent($_POST['content']);
            $em = $this->getDoctrine()->getManager();
            // Tells Doctrine you want to save the article (no queries yet)
            $em->persist($Article);
            // This line actually executes the SQL query
            $em->flush();
            return $this->redirect('/list');
        }else{
            $user = $this->container->get('security.context')->getToken()->getUser();
            return $this->render('default/add.html.twig', array('user' => $user));
        }
    }

    /**
     * @Route("/list", name="list")
     */
    public function ListAction()
    {
        $article_list = $this->getDoctrine()->getRepository('AppBundle:Article')->findAll();
        return $this->render('default/list.html.twig',array('article_list'=>$article_list));
    }

    /**
     * @Route("/detail/{slug}", name="detail")
     */
    public function DetailAction($slug)
    {
        $article_detail=$this->getDoctrine()->getRepository('AppBundle:Article')->find($slug);
        return $this->render('default/detail.html.twig',array('article_detail'=>$article_detail));
    }
}
