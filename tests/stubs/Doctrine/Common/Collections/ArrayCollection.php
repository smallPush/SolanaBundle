<?php

namespace Doctrine\Common\Collections;

if (!class_exists('Doctrine\Common\Collections\ArrayCollection')) {
    class ArrayCollection implements Collection
    {
        private array $elements;

        public function __construct(array $elements = [])
        {
            $this->elements = $elements;
        }

        public function contains($element)
        {
            return in_array($element, $this->elements, true);
        }

        public function add($element)
        {
            $this->elements[] = $element;
            return true;
        }

        public function removeElement($element)
        {
            $key = array_search($element, $this->elements, true);
            if ($key !== false) {
                unset($this->elements[$key]);
                $this->elements = array_values($this->elements);
                return true;
            }
            return false;
        }

        public function isEmpty()
        {
            return empty($this->elements);
        }

        public function count()
        {
            return count($this->elements);
        }
    }
}
