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
use AppBundle\Entity\Funcionario;

class FuncionarioController extends FOSRestController
{

    /**
     * @Rest\Get("/rest/funcionarios")
     */
    public function getAction()
    {
        $restResult = $this->getDoctrine()->getRepository('AppBundle:Funcionario')->findAll();
        if($restResult === null){
            return new View("Não existem funcionarios", Response::HTTP_NOT_FOUND);
        }

        return $restResult;
    }

    /**
     * @Rest\Get("/rest/funcionario/{id}")
     */
    public function idAction($id)
    {
        $restIdResult = $this->getDoctrine()->getRepository('AppBundle:Funcionario')->find($id);
        if($restIdResult === null){
            return new View("Este funcionário não existe", Response::HTTP_NOT_FOUND);
        }
        return $restIdResult;
    }

    /**
     * @Rest\Post("/rest/funcionarios")
     */
    public function postAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = new Funcionario();
        $nome = $request->get('nome');
        $dataNascimento = $request->get('data_nascimento');
        $sexo = $request->get('sexo');
        $estadoCivil = $request->get('estado_civil');
        $cargo = $request->get('cargo');
        $telefone = $request->get('telefone');
        $especialidade = $request->get('especialidade');
        $dataCadastro = new \DateTime();

        $dbEspecialidade = $this->getDoctrine()->getRepository('AppBundle:Especialidade')->find($especialidade);

        if(is_null($dbEspecialidade)) {
            return new View("Especialidade não encontrada",Response::HTTP_NOT_ACCEPTABLE);
        }
        elseif(empty($nome) || empty($dataNascimento) || empty($sexo) || empty($estadoCivil) || empty($cargo) || empty($telefone)){
            return new View("Dados obrigatórios não enviandos", Response::HTTP_NOT_ACCEPTABLE);
        } else {
            $data->setNome($nome);
            $data->setDataNascimento($dataNascimento);
            $data->setSexo($sexo);
            $data->setEstadoCivil($estadoCivil);
            $data->setCargo($cargo);
            $data->setTelefone($telefone);
            $data->setDataCadastro($dataCadastro);
            $em->persist($data);
            $em->flush();
            return new View("Funcionario cadastrado com sucesso", Response::HTTP_OK);
        }
    }

    /**
     * @Rest\Put("/rest/funcionario/{id}")
     */
    public function updateAction($id, Request $request)
    {
        $data = new Funcionario();
        $nome = $request->get('nome');
        $dataNascimento = $request->get('data_nascimento');
        $sexo = $request->get('sexo');
        $estadoCivil = $request->get('estado_civil');
        $cargo = $request->get('cargo');
        $telefone = $request->get('telefone');
        $especialidade = $request->get('especialidade');
        $dataAtualizacao = new \DateTime();
        $sn = $this->getDoctrine()->getManager();
        $funcionario = $this->getDoctrine()->getRepository('AppBundle:Funcionario')->find($id);
        $dbEspecialidade = $this->getDoctrine()->getRepository('AppBundle:Especialidade')->find($especialidade);
        if(is_null($dbEspecialidade)){
            return new View("Especialidade não encontrada", Response::HTTP_NOT_ACCEPTABLE);
        }
        elseif(empty($funcionario)){
            return new View("Funcionário não encontrado", Response::HTTP_NOT_FOUND);
        }
        elseif(!empty($nome) && !empty($dataNascimento) && !empty($sexo) && !empty($estadoCivil) && !empty($cargo) && !empty($telefone)){
            $funcionario->setNome($nome);
            $funcionario->setDataNascimento($dataNascimento);
            $funcionario->setSexo($sexo);
            $funcionario->setEstadoCivil($estadoCivil);
            $funcionario->setCargo($cargo);
            $funcionario->setTelefone($telefone);
            $funcionario->setDataAtualizacao($dataAtualizacao);
            $sn->flush();
            return new View("Paciente atualizado com sucesso", Response::HTTP_OK);
        }
        else {
            return new View("Dados obrigatórios não informados", Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * @Rest\Delete("/rest/funcionario/{id}")
     */
    public function deleteAction($id)
    {
        $data = new Funcionario();
        $sn =  $this->getDoctrine()->getManager();
        $funcionario = $this->getDoctrine()->getRepository('AppBundle:Funcionario')->find($id);
        if(empty($funcionario)){
            return new View("Funcionário não encontrado", Response::HTTP_NOT_FOUND);
        } else {
            $sn->remove($funcionario);
            $sn->flush();
        }
        return new View("Funcionário excluído com sucesso", Response::HTTP_OK);
    }
}