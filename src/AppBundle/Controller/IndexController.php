<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
/*use Symfony\Component\BrowserKit\Response;*/
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route("/wx_login", name="wx_login")
     */
    public function wx_loginAction(Request $request)
    {
        $username=$request->query->get(username);
        $password=password_hash($request->query->get(password),PASSWORD_BCRYPT);
        return new Response(''.$password.'');
    }

    /**
     * @Route("/add", name="add")
     */
    public function AddAction(Request $request)
    {
        if($_POST){
            $user = $this->container->get('security.context')->getToken()->getUser();
            $user_id=$user->getId();
            $Article = new Article();
            $Article->setUserId($user_id);
            $Article->setTitle($_POST['title']);
            $Article->setContent($_POST['content']);
            $em = $this->getDoctrine()->getManager();
            // Tells Doctrine you want to save the article (no queries yet)
            $em->persist($Article);
            // This line actually executes the SQL query
            $em->flush();
            return $this->redirect('/list');
        }else{
            return $this->render('default/add.html.twig');
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
