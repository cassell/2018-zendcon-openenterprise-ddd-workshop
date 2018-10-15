<?php

namespace Beeriously\Domain\Recipe;

use Beeriously\Domain\Ingredients\Grains\Grain;
use Beeriously\Domain\Measurements\Weight\Kilograms;

class Recipe
{
    private $id; // persist in recipe table
    private $events = [];  // persist in events table

    /**
     * @var RecipeName
     */
    private $recipeName;
    private $weight;

    private function __construct(RecipeHistory $events)
    {
        $this->weight = new Kilograms(0);
        $this->events = $events->toArray();
        foreach ($this->events as $event) {
            $this->applyEvent($event);
        }
    }

    // framework or repository will call this (or done by magic)
    public static function rehydrate(RecipeHistory $recipeHistory)
    {
        return new self($recipeHistory);
    }

    public static function newRecipe(RecipeName $name, \DateTimeImmutable $occurredOn): Recipe
    {
        $id = RecipeId::newId(); // aggregate id

        $createdEvent = new RecipeWasCreated($id, $name, $occurredOn);

        return new self(new RecipeHistory([$createdEvent]));
    }

    protected function applyRecipeWasCreatedEvent(RecipeWasCreated $event)
    {
        $this->id = $event->getRecipeId();
        $this->recipeName = $event->getRecipeName();
    }

    public function changeName(RecipeName $name, \DateTimeImmutable $occurredOn)
    {
        if($name->getValue() == $this->getName()->getValue()) {
            throw new \InvalidArgumentException("Name is the same");
        }
        $this->recordThat(new RecipeNameWasChanged($this->id,new RecipeName($this->getName()), $name, $occurredOn));
    }

    protected function applyRecipeNameWasChangedEvent(RecipeNameWasChanged $event)
    {
        $this->recipeName = $event->getNewName()->getValue();
    }

    public function addGrain(Grain $grain, Kilograms $kilograms, \DateTimeImmutable $occurredOn = null): void
    {
        if($kilograms->isZeroWeight()) {
            throw new \InvalidArgumentException("Unable to measure less than 0.1 on scale.");
        }

        $this->recordThat(new GrainWasAddedToRecipe(
            $this->id,
            $grain->getId(),
            $grain->getName(),
            $grain->getDegreesLintner(),
            $grain->getLovibond(),
            $grain->getGrainType()->getId(),
            $grain->getGrainType()->getDescription(),
            $kilograms,
            $occurredOn
            )
        );

    }

    public function applyGrainWasAddedToRecipeEvent(GrainWasAddedToRecipe $event)
    {
        $this->weight = $this->weight->add($event->getKilograms());
    }

    public function getWeightOfGrainsInKilos(): Kilograms
    {
        return $this->weight;
    }

    public function getName(): RecipeName
    {
        return new RecipeName($this->recipeName);
    }

    private function applyEvent(RecipeEvent $event)
    {
        $method = $this->getApplyMethod($event);
        $this->$method($event);
    }

    private function getApplyMethod(RecipeEvent $event)
    {
        $classParts = explode('\\', get_class($event));

        return 'apply' . end($classParts) . 'Event';
    }

    private function recordThat(RecipeEvent $event)
    {
        $this->events[] = $event;
        $this->applyEvent($event);
    }


}
