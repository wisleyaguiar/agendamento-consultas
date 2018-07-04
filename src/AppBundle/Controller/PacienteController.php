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
use AppBundle\Entity\Paciente;
use Ramsey\Uuid\Uuid;

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

        $retorno = [];

        foreach ($restResult as $paciente) {
            $retorno[] = [
                'id' => $paciente->getId(),
                'nome' => $paciente->getNome(),
                'telefone' => $paciente->getTelefone(),
                'usuario' => $paciente->getUsuario(),
                'data_cadastro' => $paciente->getDataCadastro(),
                'data_atualizacao' => $paciente->getDataAtualizacao()
            ];
        }

        return new View($retorno,Response::HTTP_OK);
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

        $paciente = $restIdResult;

        $retorno[] = [
            'id' => $paciente->getId(),
            'nome' => $paciente->getNome(),
            'telefone' => $paciente->getTelefone(),
            'usuario' => $paciente->getUsuario(),
            'data_cadastro' => $paciente->getDataCadastro(),
            'data_atualizacao' => $paciente->getDataAtualizacao()
        ];

        return new View($retorno,Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/rest/pacientes")
     */
    public function postAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $nome = $request->get('nome');
        $telefone = $request->get('telefone');

        $email = $request->get('email');
        $senha = $request->get('password');

        $dataCadastro = new \DateTime();

        $usuario = $this->getDoctrine()->getRepository('AppBundle:Usuario')->findOneBy(['email' => $email]);

        if(!is_null($usuario)){
            return new View("Este E-mail já está cadastrado", Response::HTTP_NOT_ACCEPTABLE);
        }
        elseif(empty($nome) || empty($telefone)){
            return new View("Dados obrigatórios não enviandos", Response::HTTP_NOT_ACCEPTABLE);
        } else {
            $usuario = new Usuario();
            $usuario->setUsername($email);
            $usuario->setEmail($email);
            $sec = $this->get('security.password_encoder');
            $usuario->setPassword($sec->encodePassword($usuario,$senha));
            $usuario->setRoles('ROLE_USER');
            $em->persist($usuario);
            $em->flush();

            $data = new Paciente();
            $data->setNome($nome);
            $data->setTelefone($telefone);
            $data->setUsuario($usuario);
            $data->setDataCadastro($dataCadastro);
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

    /**
     * @Rest\Post("/rest/login")
     * @param Request $request
     * @return View
     */
    public function loginAction(Request $request)
    {
        $d = $this->getDoctrine();
        $em = $d->getManager();
        $user = json_decode($request->getContent());
        if(!isset($user->username) || !isset($user->password)){
            return new View("Usuário ou senha inválidos",Response::HTTP_BAD_REQUEST);
        }
        $dbUser = $d->getRepository("AppBundle:Usuario")->findOneBy(['username'=>$user->username]);
        if(is_null($dbUser)){
            return new View("Usuário ou senha inválidos",Response::HTTP_BAD_REQUEST);
        }
        $encoder = $this->get("security.password_encoder");
        if(!$encoder->isPasswordValid($dbUser,$user->password)){
            return new View("Usuário ou senha inválidos");
        }
        $uuid = Uuid::uuid5(Uuid::uuid1(),$dbUser->getUsername());
        $token = $uuid->toString();

        $retorno = [
            'token' => $token,
            'user_id' => $dbUser->getId(),
            'username' => $dbUser->getUsername(),
            'email' => $dbUser->getEmail()
        ];

        //return new View($retorno,Response::HTTP_OK);
        return new View($retorno,Response::HTTP_OK,['Authorization'=>$token]);
    }
}