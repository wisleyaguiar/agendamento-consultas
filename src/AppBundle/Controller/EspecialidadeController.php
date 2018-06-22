<?php
/**
 * Created by PhpStorm.
 * User: wisleyaguiar
 * Date: 07/06/18
 * Time: 16:11
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Usuario;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Especialidade;
use Ramsey\Uuid\Uuid;

class EspecialidadeController extends FOSRestController
{

    /**
     * @Rest\Get("/rest/especialidades")
     */
    public function getAction()
    {
        $restResult = $this->getDoctrine()->getRepository('AppBundle:Especialidade')->findAll();
        if($restResult === null){
            return new View("Não existem especialidades", Response::HTTP_NOT_FOUND);
        }

        return $restResult;
    }

    /**
     * @Rest\Get("/rest/especialidade/{id}")
     */
    public function idAction($id)
    {
        $restIdResult = $this->getDoctrine()->getRepository('AppBundle:Especialidade')->find($id);
        if($restIdResult === null){
            return new View("Esta especialidade não existe", Response::HTTP_NOT_FOUND);
        }
        return $restIdResult;
    }

    /**
     * @Rest\Post("/rest/especialidades")
     */
    public function postAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $nome = $request->get('nome');

        $dataCadastro = new \DateTime();

        if(empty($nome)){
            return new View("Dados obrigatórios não enviandos", Response::HTTP_NOT_ACCEPTABLE);
        } else {
            $data = new Especialidade();
            $data->setNome($nome);
            $data->setDataCadastro($dataCadastro);
            $em->persist($data);
            $em->flush();
            return new View("Especialidade cadastrado com sucesso", Response::HTTP_OK);
        }
    }

    /**
     * @Rest\Put("/rest/especialidade/{id}")
     */
    public function updateAction($id, Request $request)
    {
        $nome = $request->get('nome');
        $dataAtualizacao = new \DateTime();
        $sn = $this->getDoctrine()->getManager();
        $especialidade = $this->getDoctrine()->getRepository('AppBundle:Especialidade')->find($id);
        if(empty($especialidade)){
            return new View("Especialidade não encontrada", Response::HTTP_NOT_FOUND);
        }
        elseif(!empty($nome)){
            $especialidade->setNome($nome);
            $especialidade->setDataAtualizacao($dataAtualizacao);
            $sn->flush();
            return new View("Especialidade atualizado com sucesso", Response::HTTP_OK);
        }
        else {
            return new View("Especialidade não informados", Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * @Rest\Delete("/rest/especialidade/{id}")
     */
    public function deleteAction($id)
    {
        $sn =  $this->getDoctrine()->getManager();
        $especialidade = $this->getDoctrine()->getRepository('AppBundle:Especialidade')->find($id);
        if(empty($especialidade)){
            return new View("Especialidade não encontrada", Response::HTTP_NOT_FOUND);
        } else {
            $sn->remove($especialidade);
            $sn->flush();
        }
        return new View("Especialidade excluída com sucesso", Response::HTTP_OK);
    }
}