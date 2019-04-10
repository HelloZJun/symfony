<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\User;
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

    /**
     * @Route("/wx_login", name="wx_login")
     */
    public function wx_loginAction(Request $request)
    {
        $username_input=$request->query->get(username);
        $password_input=$request->query->get(password);
        $code=$request->query->get(code);

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $user=$repository->findOneByUsername($username_input);
        if($user){
            $password=$user->getPassword();
            if(password_verify($password_input,$password)){
                $s3rd['code']='1';
                $user_id=$user->getId();
                //初始化
                $curl = curl_init();
                //设置抓取的url
                curl_setopt($curl, CURLOPT_URL, 'https://api.weixin.qq.com/sns/jscode2session?appid=wxf19a1c260eb537a8&secret=17352dc8a4983c46916978852f5e3d7f&js_code='.$code.'&grant_type=authorization_code');
                //设置头文件的信息作为数据流输出
                curl_setopt($curl, CURLOPT_HEADER, 0);
                //设置获取的信息以文件流的形式返回，而不是直接输出。
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                //执行命令
                $data = curl_exec($curl);
                //关闭URL请求
                curl_close($curl);

                $res=json_decode($data,true);

                $session3rd=rand();
                $value['user_id']=$user_id;
                $value['openid']=$res['openid'];
                $value['session_key']=$res['session_key'];

                $session=$request->getSession();
                // store an attribute for reuse during a later user request
                $session->set($session3rd, $value);
                // get the attribute set by another controller in another request
                $s3rd['key']=$session3rd;
            }else{
                $s3rd['code']='0';
                $s3rd['hint']='密码错误';
            }
        }else{
            $s3rd['code']='-1';
            $s3rd['hint']='用户不存在';
        }

        $s3rd=json_encode($s3rd);
        return new Response($s3rd);
    }

    /**
     * @Route("/wx_register", name="wx_register")
     */
    public function wx_registerAction(Request $request)
    {
        $username_input=$request->query->get(username);
        $password_input=$request->query->get(password);
        $email_input=$request->query->get(email);

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $user=$repository->findOneByUsername($username_input);
        $email=$repository->findOneByemail($email_input);
        if(!$user){
            if(!$email) {
                $user = new User();
                $user->setUsername($username_input);
                $user->setEmail($email_input);
                $user->setPassword(password_hash($password_input, PASSWORD_BCRYPT));
                $user->setEnabled('1');
                $em = $this->getDoctrine()->getManager();
                // Tells Doctrine you want to save the article (no queries yet)
                $em->persist($user);
                // This line actually executes the SQL query
                $em->flush();
                $value['code'] = '1';
            }else{
                $value['code']='-1';
                $value['hint']='该邮箱已被注册';
            }
        }else{
            $value['code']='0';
            $value['hint']='该用户名已被注册';
        }
        $value=json_encode($value);
        return new Response($value);
    }

    /**
     * @Route("/wx_list", name="wx_list")
     */
    public function wx_listAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $Article_list=$repository->findAll();
        foreach($Article_list as  $key=>$value){
            $article[$key]['id']=$value->getId();
            $article[$key]['title']=$value->getTitle();
        }
        $article=json_encode($article);
        return new Response($article);
    }

    /**
     * @Route("/wx_detail/{slug}", name="wx_detail")
     */
    public function wx_detailAction($slug)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $Article=$repository->find($slug);
        $article_detail['title']=$Article->getTitle();
        $article_detail['content']=$Article->getContent();
        $article=json_encode($article_detail);
        return new Response($article);
    }

    /**
     * @Route("/wx_add", name="wx_add")
     */
    public function wx_addAction(Request $request)
    {
        $title=$request->query->get(title);
        $content=$request->query->get(content);
        $session_id=$request->query->get(session_id);

        $session=$request->getSession();
        $session_data=$session->get($session_id);

        /*$Article = new Article();
        $Article->setUserId($user_id);
        $Article->setTitle($_POST['title']);
        $Article->setContent($_POST['content']);
        $em = $this->getDoctrine()->getManager();
        // Tells Doctrine you want to save the article (no queries yet)
        $em->persist($Article);
        // This line actually executes the SQL query
        $em->flush();*/
        return new Response($session_data);
    }
}
