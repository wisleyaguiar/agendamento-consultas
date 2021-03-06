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
use AppBundle\Entity\Paciente;
use AppBundle\Entity\Funcionario;
use AppBundle\Entity\Usuario;

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

        $retorno = [];

        foreach ($restResult as $agenda){
            $retorno[] = [
                'id' => $agenda->getId(),
                'data' => $agenda->getData()->format("d/m/Y"),
                'hora' => $agenda->getHora()->format("H:i"),
                'data_cadastro' => $agenda->getDataCadastro(),
                'data_atualizacao' => $agenda->getDataAtualizacao(),
                'paciente' => $agenda->getPaciente(),
                'funcionario' => $agenda->getFuncionario()
            ];
        }

        return new View($retorno,Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/rest/agendas/email")
     */
    public function getByEmail(Request $request)
    {
        $email = $request->get('value');

        $restIdResult = $this->getDoctrine()->getRepository('AppBundle:Usuario')->findOneBy(['email'=>$email]);
        $resultPaciente = $this->getDoctrine()->getRepository('AppBundle:Paciente')->findOneBy(['usuario'=>$restIdResult]);
        $agendaResult = $this->getDoctrine()->getRepository('AppBundle:Agenda')->findBy(['paciente'=>$resultPaciente]);

        if($agendaResult === null){
            return new View("Este agendas não existe", Response::HTTP_NOT_FOUND);
        }

        $retorno = [];

        foreach ($agendaResult as $agenda){
            $retorno[] = [
                'id' => $agenda->getId(),
                'data' => $agenda->getData()->format("d/m/Y"),
                'hora' => $agenda->getHora()->format("H:i"),
                'data_cadastro' => $agenda->getDataCadastro(),
                'data_atualizacao' => $agenda->getDataAtualizacao(),
                'paciente' => $agenda->getPaciente(),
                'funcionario' => $agenda->getFuncionario()
            ];
        }

        return new View($retorno,Response::HTTP_OK);

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

        $agenda = $restIdResult;

        $retorno = [
            'id' => $agenda->getId(),
            'data' => $agenda->getData(),
            'hora' => $agenda->getHora(),
            'data_cadastro' => $agenda->getDataCadastro(),
            'data_atualizacao' => $agenda->getDataAtualizacao(),
            'paciente' => $agenda->getPaciente(),
            'funcionario' => $agenda->getFuncionario()
        ];

        return new View($retorno,Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/rest/agendas")
     */
    public function postAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = new Agenda();
        $dia = $request->get('data');
        $horario = $request->get('hora');
        $dataCadastro = new \DateTime();

        $funcionario = $request->get('funcionario');
        $paciente = $request->get('paciente');

        $resultFuncionario = $this->getDoctrine()->getRepository('AppBundle:Funcionario')->find($funcionario);
        $resultPaciente = $this->getDoctrine()->getRepository('AppBundle:Paciente')->find($paciente);

        if(empty($resultFuncionario)) {
            return new View("Funcionário não encontrado",Response::HTTP_NOT_ACCEPTABLE);
        }
        elseif(empty($resultPaciente)) {
            return new View("Paciente não encontrado", Response::HTTP_NOT_ACCEPTABLE);
        }
        elseif(empty($dia) || empty($horario)){
            return new View("Dados obrigatórios não enviandos", Response::HTTP_NOT_ACCEPTABLE);
        } else {
            $data->setData(new \DateTime($dia));
            $data->setHora(new \DateTime($horario));
            $data->setFuncionario($resultFuncionario);
            $data->setPaciente($resultPaciente);
            $data->setDataCadastro($dataCadastro);
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
        $sn = $this->getDoctrine()->getManager();

        $dia = $request->get('data');
        $horario = $request->get('hora');
        $dataAtualizacao = new \DateTime();

        $funcionario = $request->get('funcionario');
        $paciente = $request->get('paciente');

        $resultFuncionario = $this->getDoctrine()->getRepository('AppBundle:Funcionario')->find($funcionario);
        $resultPaciente = $this->getDoctrine()->getRepository('AppBundle:Paciente')->find($paciente);

        $agenda = $this->getDoctrine()->getRepository('AppBundle:Agenda')->find($id);

        if(empty($agenda)){
            return new View("Agenda não encontrada", Response::HTTP_NOT_FOUND);
        }
        elseif(empty($resultFuncionario)) {
            return new View("Funcionário não encontrado",Response::HTTP_NOT_ACCEPTABLE);
        }
        elseif(empty($resultPaciente)) {
            return new View("Paciente não encontrado", Response::HTTP_NOT_ACCEPTABLE);
        }
        elseif(!empty($dia) && !empty($horario)){
            $agenda->setData($dia);
            $agenda->setHora($horario);
            $agenda->setFuncionario($resultFuncionario);
            $agenda->setPaciente($resultPaciente);
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