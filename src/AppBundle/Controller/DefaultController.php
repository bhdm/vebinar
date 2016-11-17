<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $user = null;

        if ($request->getMethod() === 'POST'){
            $lastName = htmlspecialchars($request->request->get('lastName'));
            $firstName = htmlspecialchars($request->request->get('firstName'));
            $surName = htmlspecialchars($request->request->get('surName'));
            $city = htmlspecialchars($request->request->get('city'));
            $post = htmlspecialchars($request->request->get('post'));
            $specialty = htmlspecialchars($request->request->get('specialty'));

            $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy([
                'lastName' => $lastName,
                'firstName' => $firstName,
                'surName' => $surName,
                'city' => $city,
            ]);
            if ($user){
                $user->setAmount($user->getAmount()+1);
            }else{
                $user = new User();
                $user->setLastName($lastName);
                $user->setFirstName($firstName);
                $user->setSurName($surName);
                $user->setCity($city);
                $user->setPost($post);
                $user->setSpecialty($specialty);
                $em->persist($user);
            }
            $em->flush($user);
            $em->persist($user);
            $em->refresh($user);
            $session->set('user',['lastName' => $user->getLastName(), 'firstName' => $user->getFirstName(), 'id' => $user->getId()]);
            $session->save();
        }

        $user = $session->get('user');

        if ($user){
            $messages = $this->getDoctrine()->getRepository('AppBundle:Message')->findBy([],['id'=> 'DESC']);
            return $this->render('@App/webinar-translation.html.twig',[$user, $messages]);
        }else{
            return $this->render('@App/index.html.twig');
        }
    }
}


