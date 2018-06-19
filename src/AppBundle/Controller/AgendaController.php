<?php
/**
 * Created by PhpStorm.
 * User: wisleyaguiar
 * Date: 07/06/18
 * Time: 16:11
 */

namespace AppBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Agenda;

class AgendaController extends FOSRestController
{

    /**
     * @Rest\Get("/rest/agendas")
     */
    public function getAction()
    {
        $restResult = $this->getDoctrine()->getRepository('AppBundle:Agenda')->findAll();
        if($restResult === null){
            return new View("Não existem agenda", Response::HTTP_NOT_FOUND);
        }

        return $restResult;
    }

    /**
     * @Rest\Get("/rest/agenda/{id}")
     */
    public function idAction($id)
    {
        $restIdResult = $this->getDoctrine()->getRepository('AppBundle:Agenda')->find($id);
        if($restIdResult === null){
            return new View("Este agenda não existe", Response::HTTP_NOT_FOUND);
        }
        return $restIdResult;
    }

    /**
     * @Rest\Post("/rest/agendas")
     */
    public function postAction(Request $request)
    {
        $data = new Agenda();
        $dia = $request->get('data');
        $horario = $request->get('hora');
        $dataCadastro = new \DateTime();
        if(empty($dia) || empty($horario)){
            return new View("Dados obrigatórios não enviandos", Response::HTTP_NOT_ACCEPTABLE);
        } else {
            $data->setData($dia);
            $data->setHora($horario);
            $data->setDataCadastro($dataCadastro);
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();
            return new View("Agenda cadastrada com sucesso", Response::HTTP_OK);
        }
    }

    /**
     * @Rest\Put("/rest/agenda/{id}")
     */
    public function updateAction($id, Request $request)
    {
        $data = new Agenda();
        $dia = $request->get('data');
        $horario = $request->get('hora');
        $dataAtualizacao = new \DateTime();
        $sn = $this->getDoctrine()->getManager();
        $agenda = $this->getDoctrine()->getRepository('AppBundle:Agenda')->find($id);
        if(empty($agenda)){
            return new View("Agenda não encontrada", Response::HTTP_NOT_FOUND);
        }
        elseif(!empty($dia) && !empty($horario)){
            $agenda->setData($dia);
            $agenda->setHora($horario);
            $agenda->setDataAtualizacao($dataAtualizacao);
            $sn->flush();
            return new View("Agenda atualizada com sucesso", Response::HTTP_OK);
        }
        elseif(!empty($dia) && empty($horario)){
            $agenda->setData($dia);
            $agenda->setDataAtualizacao($dataAtualizacao);
            $sn->flush();
            return new View("Data do agendamento atualizada com sucesso", Response::HTTP_OK);
        }
        elseif (empty($dia) && !empty($horario)){
            $agenda->setHora($horario);
            $agenda->setDataAtualizacao($dataAtualizacao);
            $sn->flush();
            return new View("Hora do agendamento atualizada com sucesso", Response::HTTP_OK);
        }
        else {
            return new View("Data ou hora não informados", Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * @Rest\Delete("/rest/agenda/{id}")
     */
    public function deleteAction($id)
    {
        $data = new Agenda();
        $sn =  $this->getDoctrine()->getManager();
        $agenda = $this->getDoctrine()->getRepository('AppBundle:Agenda')->find($id);
        if(empty($agenda)){
            return new View("Agenda não encontrada", Response::HTTP_NOT_FOUND);
        } else {
            $sn->remove($agenda);
            $sn->flush();
        }
        return new View("Agenda excluída com sucesso", Response::HTTP_OK);
    }
}