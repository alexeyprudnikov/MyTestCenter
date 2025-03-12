<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adds created at and updated at timestamps to entities.
 * Entities using this must have HasLifecycleCallbacks annotation.
 * #[ORM\HasLifecycleCallbacks]
 */
trait Timestamp
{
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTime $created_at = null;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTime $updated_at = null;

    private bool $activeLifecycle = true;

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        if ($this->created_at === null) {
            $this->created_at = new \DateTime();
        }
        $this->updated_at = $this->created_at;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        if ($this->activeLifecycle === true) {
            $this->updated_at = new \DateTime();
        }
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTime $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActiveLifecycle(): bool
    {
        return $this->activeLifecycle;
    }

    /**
     * @param bool $activeLifecycle
     * @return $this
     */
    public function setActiveLifecycle(bool $activeLifecycle): self
    {
        $this->activeLifecycle = $activeLifecycle;

        return $this;
    }
}
