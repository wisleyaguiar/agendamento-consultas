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
use AppBundle\Entity\Paciente;

class PacienteController extends FOSRestController
{

    /**
     * @Rest\Get("/rest/pacientes")
     */
    public function getAction()
    {
        $restResult = $this->getDoctrine()->getRepository('AppBundle:Paciente')->findAll();
        if($restResult === null){
            return new View("Não existem pacientes", Response::HTTP_NOT_FOUND);
        }

        return $restResult;
    }

    /**
     * @Rest\Get("/rest/paciente/{id}")
     */
    public function idAction($id)
    {
        $restIdResult = $this->getDoctrine()->getRepository('AppBundle:Paciente')->find($id);
        if($restIdResult === null){
            return new View("Este paciente não existe", Response::HTTP_NOT_FOUND);
        }
        return $restIdResult;
    }

    /**
     * @Rest\Post("/rest/pacientes")
     */
    public function postAction(Request $request)
    {
        $data = new Paciente();
        $nome = $request->get('nome');
        $telefone = $request->get('telefone');
        $dataCadastro = new \DateTime();
        if(empty($nome) || empty($telefone)){
            return new View("Dados obrigatórios não enviandos", Response::HTTP_NOT_ACCEPTABLE);
        } else {
            $data->setNome($nome);
            $data->setTelefone($telefone);
            $data->setDataCadastro($dataCadastro);
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();
            return new View("Paciente cadastrado com sucesso", Response::HTTP_OK);
        }
    }

    /**
     * @Rest\Put("/rest/paciente/{id}")
     */
    public function updateAction($id, Request $request)
    {
        $data = new Paciente();
        $nome = $request->get('nome');
        $telefone = $request->get('telefone');
        $dataAtualizacao = new \DateTime();
        $sn = $this->getDoctrine()->getManager();
        $paciente = $this->getDoctrine()->getRepository('AppBundle:Paciente')->find($id);
        if(empty($paciente)){
            return new View("Paciente não encontrado", Response::HTTP_NOT_FOUND);
        }
        elseif(!empty($nome) && !empty($telefone)){
            $paciente->setNome($nome);
            $paciente->setTelefone($telefone);
            $paciente->setDataAtualizacao($dataAtualizacao);
            $sn->flush();
            return new View("Paciente atualizado com sucesso", Response::HTTP_OK);
        }
        elseif(!empty($nome) && empty($telefone)){
            $paciente->setNome($nome);
            $paciente->setDataAtualizacao($dataAtualizacao);
            $sn->flush();
            return new View("Nome do paciente atualizado com sucesso", Response::HTTP_OK);
        }
        elseif (empty($nome) && !empty($telefone)){
            $paciente->setTelefone($telefone);
            $paciente->setDataAtualizacao($dataAtualizacao);
            $sn->flush();
            return new View("Telefone do paciente atualizado com sucesso", Response::HTTP_OK);
        }
        else {
            return new View("Nome ou telefone não informados", Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * @Rest\Delete("/rest/paciente/{id}")
     */
    public function deleteAction($id)
    {
        $data = new Paciente();
        $sn =  $this->getDoctrine()->getManager();
        $paciente = $this->getDoctrine()->getRepository('AppBundle:Paciente')->find($id);
        if(empty($paciente)){
            return new View("Paciente não encontrado", Response::HTTP_NOT_FOUND);
        } else {
            $sn->remove($paciente);
            $sn->flush();
        }
        return new View("Paciente excluído com sucesso", Response::HTTP_OK);
    }
}