<?php

namespace AppBundle\Entity\Snapshots;

use AppBundle\Entity\AuditEngine\NoteEngine;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="note",indexes={@ORM\Index(name="search_idx_1", columns={"note_engine_id", "snapshot_id"})} )
 * Note d'un snapshot
 */
class Note
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;

    /** nécessaire pour un OneToMany dans @link Ressources
     * @ORM\ManyToOne(targetEntity="Snapshot", inversedBy="notes")
     */
    private $snapshot;
    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\AuditEngine\NoteEngine")
     */
    private $noteEngine;
    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $value;

    public function getName(){
        return $this->noteEngine->getName();
    }
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set snapshot
     *
     * @param \AppBundle\Entity\Snapshots\Snapshot $snapshot
     *
     * @return Note
     */
    public function setSnapshot(\AppBundle\Entity\Snapshots\Snapshot $snapshot = null)
    {
        $this->snapshot = $snapshot;

        return $this;
    }

    /**
     * Get snapshot
     *
     * @return \AppBundle\Entity\Snapshots\Snapshot
     */
    public function getSnapshot()
    {
        return $this->snapshot;
    }

    /**
     * Set value
     *
     * @param float $value
     *
     * @return Note
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set noteEngine
     *
     * @param \AppBundle\Entity\AuditEngine\NoteEngine $noteEngine
     *
     * @return Note
     */
    public function setNoteEngine(\AppBundle\Entity\AuditEngine\NoteEngine $noteEngine = null)
    {
        $this->noteEngine = $noteEngine;

        return $this;
    }

    /**
     * Get noteEngine
     *
     * @return \AppBundle\Entity\AuditEngine\NoteEngine
     */
    public function getNoteEngine()
    {
        return $this->noteEngine;
    }
}
