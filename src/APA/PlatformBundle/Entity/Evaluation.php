<?php

namespace APA\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Evaluation
{
    /**
     * @ORM\ManyToOne(targetEntity="APA\SecurityBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="APA\PlatformBundle\Entity\Seance")
     * @ORM\JoinColumn(nullable=false)
     */
    private $seance;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var bool
     *
     * @ORM\Column(name="presence", type="boolean")
     */
    private $presence;

    /**
     * @var int
     *
     * @ORM\Column(name="ressenti", type="smallint", nullable=true)
     */
    private $ressenti;

    /**
     * @var int
     *
     * @ORM\Column(name="intensite", type="smallint", nullable=true)
     */
    private $intensite;

    /**
     * @var bool
     *
     * @ORM\Column(name="autonomie", type="boolean")
     */
    private $autonomie;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", nullable=true)
     */
    private $commentaire;

    /**
     * @var array
     */
    private $activite;



    public function __construct(){
        $this->date = new \Datetime();
    }

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Evaluation
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set presence
     *
     * @param boolean $presence
     *
     * @return Evaluation
     */
    public function setPresence($presence)
    {
        $this->presence = $presence;

        return $this;
    }

    /**
     * Get presence
     *
     * @return bool
     */
    public function getPresence()
    {
        return $this->presence;
    }

    /**
     * Set ressenti
     *
     * @param integer $ressenti
     *
     * @return Evaluation
     */
    public function setRessenti($ressenti)
    {
        $this->ressenti = $ressenti;

        return $this;
    }

    /**
     * Get ressenti
     *
     * @return int
     */
    public function getRessenti()
    {
        return $this->ressenti;
    }

    /**
     * Set intensite
     *
     * @param integer $intensite
     *
     * @return Evaluation
     */
    public function setIntensite($intensite)
    {
        $this->intensite = $intensite;

        return $this;
    }

    /**
     * Get intensite
     *
     * @return int
     */
    public function getIntensite()
    {
        return $this->intensite;
    }

    /**
     * Set autonomie
     *
     * @param boolean $autonomie
     *
     * @return Evaluation
     */
    public function setAutonomie($autonomie)
    {
        $this->autonomie = $autonomie;

        return $this;
    }

    /**
     * Get autonomie
     *
     * @return bool
     */
    public function getAutonomie()
    {
        return $this->autonomie;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Evaluation
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set activite
     *
     * @param array $activite
     *
     * @return Evaluation
     */
    public function setActivite($activite)
    {
        $this->activite = $activite;

        return $this;
    }

    /**
     * Get activite
     *
     * @return array
     */
    public function getActivite()
    {
        return $this->activite;
    }

    public function setUser($user){
        $this->user = $user;
    }

    public function getUser(){
        return $this->user;
    }

    public function setSeance($seance){
        $this->seance = $seance;
    }

    public function getSeance(){
        return $this->seance;
    }
}
