<?php

namespace Haynner\ClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Haynner\ClientBundle\Entity\Admin;
use Haynner\ClientBundle\Form\AdminType;
use Haynner\ClientBundle\Entity\Client;
use Haynner\ClientBundle\Form\ClientType;
use Haynner\ClientBundle\Form\ClientEditType;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class DefaultController extends Controller
{
    public function indexAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->render('HaynnerClientBundle:Default:accesrefuse.html.twig');
        }
        $clients = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('HaynnerClientBundle:Client')
                        ->findAll();

        return $this->render('HaynnerClientBundle:Default:index.html.twig',array('clients' => $clients));
    }

    public function voirAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->render('HaynnerClientBundle:Default:accesrefuse.html.twig');
        }

        $repository = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('HaynnerClientBundle:Client');
                        
        $client = $repository->find($id);                    

    	return $this->render('HaynnerClientBundle:Default:voir.html.twig',array('client' => $client));
    }

    public function ajouterAction()
    {

        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->render('HaynnerClientBundle:Default:accesrefuse.html.twig');
        }

    	$client = new Client;
        $form = $this->createForm(new ClientType, $client);

        $request = $this->get('request');

        if($request->getMethod() == 'POST'){
            $form->bind($request);

            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($client);
                $em->flush();

                return $this->redirect($this->generateUrl('homepage'));
            }
        }
        return $this->render('HaynnerClientBundle:Default:ajouter.html.twig', array('form' => $form->createView()));
    }

    public function modifierAction(Client $client)
    {

        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->render('HaynnerClientBundle:Default:accesrefuse.html.twig');
        }

        $form = $this->createForm(new ClientEditType(), $client);
        $request = $this->getRequest();

        if($request->getMethod() == 'POST'){
            $form->bind($request);

            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($client);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'Client modifié');
                return $this->redirect($this->generateUrl('voir', array('id' => $client->getId())));
            }
        }
    	return $this->render('HaynnerClientBundle:Default:modifier.html.twig', array('form' => $form->createView(),'client' => $client));
    }

    public function menuAction()
    {
    	return $this->render('HaynnerClientBundle:Default:menu.html.twig');
    }

    public function serializeAction()
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $clients = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('HaynnerClientBundle:Client')
                        ->findAll();

        $json = $serializer->serialize($clients,'json');
        return $this->render('HaynnerClientBundle:Default:clients.html.twig',array('clients' => $json));
    }
}
