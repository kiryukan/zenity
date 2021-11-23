<?php
/**
 * Created by PhpStorm.
 * User: renjithVanWolput
 * Date: 21/11/18
 * Time: 11:15
 */

namespace AppBundle\Entity\ComplementaryFlow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="daily")
 *
 * Contient le contenu des daily
 */

class Daily
{
    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string",length=40,nullable =true)
     */
    private $edition;
    /**
     * @ORM\Column(type="string",length=70,nullable =true)
     */
    private $banner;
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Daily
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * @param mixed $text
     * @return Daily
     */
    public function setEdition($edition)
    {
        $this->text = $edition;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * @param mixed $banner
     * @return Daily
     */
    public function setBanner($banner)
    {
        $this->banner = $banner;
        return $this;
    }

}
