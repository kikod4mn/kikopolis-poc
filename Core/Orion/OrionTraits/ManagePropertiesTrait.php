<?php

declare(strict_types=1);

namespace Kikopolis\Core\Orion\OrionTraits;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * OrionTrait
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

trait ManagePropertiesTrait
{
    /**
     * Visible and fillable properties of the class.
     * @var array
     */
    protected $fillable = [];

    /**
     * Visible but not fillable properties of the class.
     * @var array
     */
    protected $visible = [];

    /**
     * Protected properties, not visible or fillable. Can be accessed by the extending classes.
     * @var array
     */
    protected $guarded = ['*'];

    /**
     * Private properties, not visible or accessible for the outside world.
     * @var array
     */
    protected $hidden = ['stmt', 'errors'];

    /**
     * Return the fillable properties of the model.
     * @return array
     */
    public function getFillable(): array
    {
        return $this->fillable;
    }

    /**
     * Set the fillable properties of the model.
     * @param array $fillable
     * @return void
     */
    public function fillable(array $fillable): void
    {
        $this->fillable = $fillable;
    }

    /**
     * Get the visible, but not fillable properties of the model.
     * @return array
     */
    public function getVisible(): array
    {
        return $this->visible;
    }

    /**
     * Set the visible, but not fillable properties of the model.
     * @param array $visible
     * @return void
     */
    public function visible(array $visible): void
    {
        $this->visible = $visible;
    }

    /**
     * Return the guarded properties of the model.
     * @return array
     */
    public function getGuarded(): array
    {
        return $this->guarded;
    }

    /**
     * Guard an array of properties. Protects the properties from all outside influence.
     * @param array $attributes
     * @return void
     */
    public function guard(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            $this->guarded[$key] = $value;
        }
    }

    /**
     * Return the hidden properties of the model.
     * @return array
     */
    public function getHidden(): array
    {
        return $this->hidden;
    }

    /**
     * Hide an array of properties from public view.
     * @param array $attributes
     * @return void
     */
    public function hide(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            $this->guarded[$key] = $value;
        }
    }

    /**
     * Verify a property is fillable.
     * @param string $key
     * @return bool
     */
    public function isFillable(string $key): bool
    {
        return in_array($key, $this->fillable) && !in_array($this->guarded)? true : false;
    }

    /**
     * Verify a property is visible.
     * @param string $key
     * @return bool
     */
    public function isVisible(string $key): bool
    {
        return in_array($key, $this->visible);
    }

    /**
     * Verify a property is guarded.
     * Can not be set by the outside world or any setter functions.
     * @param string $key
     * @return bool
     */
    public function isGuarded(string $key): bool
    {
        return in_array($key, $this->guarded);
    }

    /**
     * Verify a property is hidden.
     * @param string $key
     * @return bool
     */
    public function isHidden(string $key): bool
    {
        return in_array($key, $this->hidden);
    }

    /**
     * Map an array of attributes to corresponding class properties.
     * @param $attributes
     * @throws \Exception
     * @return $this
     */
    public function fill($attributes)
    {
        foreach ($attributes as $key => $value) {
            if(!$this->isGuarded($key)) {
                $this->attributes[$key] = $value;
            } else {
                throw new \Exception("Property {$key} is not mass assignable. Add it to the fillable array and remove it from guarded.");
            }
        }

        return $this;
    }

    /**
     * Return an array of model properties for view.
     * Anything not in the hidden array is returned.
     * @param $attributes
     * @return mixed
     */
    public function show($attributes)
    {
        foreach ($attributes as $key => $value) {
            if (!$this->isHidden($key)) {
                $attributes->$key = $value;
            } else {
                unset($attributes->$key);
            }
        }

        return $attributes;
    }
}
