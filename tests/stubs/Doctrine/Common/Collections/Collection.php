<?php

namespace Doctrine\Common\Collections;

if (!interface_exists('Doctrine\Common\Collections\Collection')) {
    interface Collection
    {
        public function contains($element);
        public function add($element);
        public function removeElement($element);
        public function isEmpty();
        public function count();
    }
}
