<?php

namespace Kikopolis\Core\Orion\OrionTraits;

defined('_KIKOPOLIS') or die('No direct script access!');

trait ManagePropertiesTrait
{
    /**
     * Visible and fillable properties of the class.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Visible but not fillable properties of the class.
     *
     * @var array
     */
    protected $visible = [];

    /**
     * Protected properties, not visible or fillable. Can be accessed by the extending classes.
     *
     * @var array
     */
    protected $guarded = ['*'];

    /**
     * Private properties, not visible or accessible for the outside world.
     *
     * @var array
     */
    protected $hidden = ['stmt', 'errors'];

    protected $attributes = [];

    public function getFillable(): array
    {
        return $this->fillable;
    }

    public function fillable(array $fillable): void
    {
        $this->fillable = $fillable;
    }

    public function getVisible(): array
    {
        return $this->visible;
    }

    public function visible(array $visible): void
    {
        $this->visible = $visible;
    }

    public function getGuarded(): array
    {
        return $this->guarded;
    }

    public function getHidden(): array
    {
        return $this->hidden;
    }

    public function isFillable(string $key): bool
    {
        return in_array($key, $this->fillable);
    }

    public function isVisible(string $key): bool
    {
        return in_array($key, $this->visible);
    }

    public function isGuarded(string $key): bool
    {
        return in_array($key, $this->guarded) || $this->guarded === ['*'];
    }

    public function isHidden(string $key): bool
    {
        return in_array($key, $this->hidden);
    }

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    public function guard(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (!$this->isHidden($key)) {
                $this->guarded[$key] = $value;
            }
        }
    }

    public function hide(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->guarded[$key] = $value;
        }
    }
}
