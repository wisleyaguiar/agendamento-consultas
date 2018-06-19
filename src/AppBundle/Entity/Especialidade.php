<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Especialidade
 *
 * @ORM\Table(name="especialidade")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EspecialidadeRepository")
 */
class Especialidade
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=255)
     */
    private $nome;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dataCadastro", type="datetime")
     */
    private $dataCadastro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dataAtualizacao", type="datetime", nullable=true)
     */
    private $dataAtualizacao;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nome
     *
     * @param string $nome
     *
     * @return Especialidade
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set dataCadastro
     *
     * @param \DateTime $dataCadastro
     *
     * @return Especialidade
     */
    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;

        return $this;
    }

    /**
     * Get dataCadastro
     *
     * @return \DateTime
     */
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    /**
     * Set dataAtualizacao
     *
     * @param \DateTime $dataAtualizacao
     *
     * @return Especialidade
     */
    public function setDataAtualizacao($dataAtualizacao)
    {
        $this->dataAtualizacao = $dataAtualizacao;

        return $this;
    }

    /**
     * Get dataAtualizacao
     *
     * @return \DateTime
     */
    public function getDataAtualizacao()
    {
        return $this->dataAtualizacao;
    }
}

