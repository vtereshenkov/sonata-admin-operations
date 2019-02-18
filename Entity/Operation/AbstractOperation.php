<?php

/*
 * This file is part of the VtereshenkovSonataOperationBundle package.
 *
 * (c) Vitaliy Tereshenkov
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vtereshenkov\SonataOperationBundle\Entity\Operation;

use Vtereshenkov\SonataOperationBundle\Entity\ClassName;
use Vtereshenkov\SonataOperationBundle\Entity\OperationType;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Model\GroupInterface;

class AbstractOperation
{

    /**
     * @var integer
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $title;

    /**
     * @var UserInterface
     */
    protected $author;

    /**
     *
     * @var GroupInterface|null 
     */
    protected $authorGroup = null;

    /**
     *
     * @var ClassName
     */
    protected $className;

    /**
     *
     * @var int
     */
    protected $classObjectId;

    /**
     *
     * @var \DateTime
     */
    protected $date;

    /**
     *
     * @var bool
     */
    protected $moderated;

    /**
     *
     * @var \DateTime|null
     */
    protected $moderationDate = null;

    /**
     * @var UserInterface
     */
    protected $moderator;

    /**
     *
     * @var string
     */
    protected $objectAfter = null;

    /**
     *
     * @var string
     */
    protected $objectBefore = null;

    /**
     *
     * @var OperationType 
     */
    protected $type;

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }

    public function setAuthor(?UserInterface $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getAuthorGroup(): ?GroupInterface
    {
        return $this->authorGroup;
    }

    public function setAuthorGroup(?GroupInterface $authorGroup): self
    {
        $this->authorGroup = $authorGroup;

        return $this;
    }

    public function getClassName(): ?ClassName
    {
        return $this->className;
    }

    public function setClassName(?ClassName $className): self
    {
        $this->className = $className;

        return $this;
    }

    public function getClassObjectId(): ?int
    {
        return $this->classObjectId;
    }

    public function setClassObjectId(int $classObject): self
    {
        $this->classObjectId = $classObject;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getModerated(): ?bool
    {
        return $this->moderated;
    }

    public function setModerated(bool $moderated): self
    {
        $this->moderated = $moderated;

        return $this;
    }

    public function getModerationDate(): ?\DateTimeInterface
    {
        return $this->moderationDate;
    }

    public function setModerationDate(?\DateTimeInterface $moderationDate): self
    {
        $this->moderationDate = $moderationDate;

        return $this;
    }

    public function getModerator(): ?UserInterface
    {
        return $this->moderator;
    }

    public function setModerator(?UserInterface $moderator): self
    {
        $this->moderator = $moderator;

        return $this;
    }

    public function getObjectAfter(): ?string
    {
        return $this->objectAfter;
    }

    public function setObjectAfter(?string $objectAfter): self
    {
        $this->objectAfter = $objectAfter;

        return $this;
    }

    public function getObjectBefore(): ?string
    {
        return $this->objectBefore;
    }

    public function setObjectBefore(?string $objectBefore): self
    {
        $this->objectBefore = $objectBefore;

        return $this;
    }

    public function getType(): ?OperationType
    {
        return $this->type;
    }

    public function setType(?OperationType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Format object before data for sonata admin show page
     * 
     * @return string
     */
    public function getObjectBeforeFormat(): string
    {
        $text = '';
        if (!empty($this->getObjectBefore())) {
            $objectA = unserialize($this->getObjectBefore());
            /* Object after for comparison */
            $objectAComp = [];
            if (!empty($this->getObjectAfter())) {
                $objectAComp = unserialize($this->getObjectAfter());
            }
            foreach ($objectA as $key => $value) {
                $classC = '';
                if (!is_array($value)) {
                    if ((!empty($objectAComp[$key]) && $objectAComp[$key] !== $objectA[$key]) || (empty($objectAComp[$key]) && !empty($objectA[$key]) && !empty($objectAComp))) {
                        $classC = 'text-danger text-bold';
                    }

                    $text .= '<p class="' . $classC . '">' . $key . ': ' . $value . '</p>';
                } else {
                    $differencesExist = false;
                    $subText = '';
                    foreach ($value as $subKey => $subvalue) {
                        if (!is_array($subvalue)) {
                            if (!empty($objectAComp[$key][$subKey]) && $objectAComp[$key][$subKey] !== $objectA[$key][$subKey]) {
                                $differencesExist = true;
                            }
                            if ($subKey === 'name') {
                                $subText .= '<li>' . $subvalue . '</li>';
                            }
                        } else {
                            if (!empty($objectAComp[$key][$subKey]) && $objectAComp[$key][$subKey]['name'] !== $objectA[$key][$subKey]['name']) {
                                $differencesExist = true;
                            }
                            $subText .= '<li>' .
                                    '<span>' . $subvalue['name'] . '</span>'
                                    . '</li>';
                        }
                    }
                    if (!empty($objectAComp) && count($objectA[$key]) !== count($objectAComp[$key])) {
                        $differencesExist = true;
                    }
                    $text .= '<p class="' . (true === $differencesExist ? 'text-danger text-bold' : '') . '">' . $key . ': <ul class="' . (true === $differencesExist ? 'text-danger text-bold' : '') . '">' . $subText . '</ul></p>';
                }
            }
        }
        return $text;
    }

    /**
     * Format object after data for sonata admin show page
     * 
     * @return string
     */
    public function getObjectAfterFormat(): string
    {
        $text = '';
        if (!empty($this->getObjectAfter())) {
            $objectA = unserialize($this->getObjectAfter());
            /* Object after for comparison */
            $objectAComp = [];
            if (!empty($this->getObjectBefore())) {
                $objectAComp = unserialize($this->getObjectBefore());
            }
            foreach ($objectA as $key => $value) {
                $classC = '';
                if (!is_array($value)) {
                    if ((!empty($objectAComp[$key]) && $objectAComp[$key] !== $objectA[$key]) || (empty($objectAComp[$key]) && !empty($objectA[$key] && !empty($objectAComp)))) {
                        $classC = 'text-danger text-bold';
                    }

                    $text .= '<p class="' . $classC . '">' . $key . ': ' . $value . '</p>';
                } else {
                    $differencesExist = false;
                    $subText = '';
                    foreach ($value as $subKey => $subvalue) {
                        if (!is_array($subvalue)) {
                            if (!empty($objectAComp[$key][$subKey]) && $objectAComp[$key][$subKey] !== $objectA[$key][$subKey]) {
                                $differencesExist = true;
                            }
                            if ($subKey === 'name') {
                                $subText .= '<li>' . $subvalue . '</li>';
                            }
                        } else {
                            if (!empty($objectAComp[$key][$subKey]) && $objectAComp[$key][$subKey]['name'] !== $objectA[$key][$subKey]['name']) {
                                $differencesExist = true;
                            }
                            $subText .= '<li>' .
                                    '<span>' . $subvalue['name'] . '</span>'
                                    . '</li>';
                        }
                    }
                    if (!empty($objectAComp) && count($objectA[$key]) !== count($objectAComp[$key])) {
                        $differencesExist = true;
                    }
                    $text .= '<p class="' . (true === $differencesExist ? 'text-danger text-bold' : '') . '">' . $key . ': <ul class="' . (true === $differencesExist ? 'text-danger text-bold' : '') . '">' . $subText . '</ul></p>';
                }
            }
        }
        return $text;
    }

}
