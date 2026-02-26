<?php

namespace Doctrine\Bundle\DoctrineBundle\Repository {
    if (!class_exists('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository')) {
        class ServiceEntityRepository {
            public function __construct($registry, $entityClass) {}
        }
    }
}

namespace Doctrine\Persistence {
    if (!interface_exists('Doctrine\Persistence\ManagerRegistry')) {
        interface ManagerRegistry {}
    }
}

namespace Doctrine\Common\Collections {
    if (!interface_exists('Doctrine\Common\Collections\Collection')) {
        interface Collection {
            public function add($element);
            public function clear();
            public function contains($element);
            public function isEmpty();
            public function remove($key);
            public function removeElement($element);
            public function containsKey($key);
            public function get($key);
            public function getKeys();
            public function getValues();
            public function set($key, $value);
            public function toArray();
            public function first();
            public function last();
            public function key();
            public function current();
            public function next();
            public function exists(\Closure $p);
            public function filter(\Closure $p);
            public function forAll(\Closure $p);
            public function map(\Closure $func);
            public function partition(\Closure $p);
            public function indexOf($element);
            public function slice($offset, $length = null);
        }
    }

    if (!class_exists('Doctrine\Common\Collections\ArrayCollection')) {
        class ArrayCollection implements Collection {
            private $elements;
            public function __construct(array $elements = []) { $this->elements = $elements; }
            public function add($element) { $this->elements[] = $element; return true; }
            public function clear() { $this->elements = []; }
            public function contains($element) { return in_array($element, $this->elements, true); }
            public function isEmpty() { return empty($this->elements); }
            public function remove($key) {
                if (isset($this->elements[$key]) || array_key_exists($key, $this->elements)) {
                    $removed = $this->elements[$key];
                    unset($this->elements[$key]);
                    return $removed;
                }
                return null;
            }
            public function removeElement($element) {
                $key = array_search($element, $this->elements, true);
                if ($key !== false) {
                    unset($this->elements[$key]);
                    return true;
                }
                return false;
            }
            public function containsKey($key) { return isset($this->elements[$key]) || array_key_exists($key, $this->elements); }
            public function get($key) { return $this->elements[$key] ?? null; }
            public function getKeys() { return array_keys($this->elements); }
            public function getValues() { return array_values($this->elements); }
            public function set($key, $value) { $this->elements[$key] = $value; }
            public function toArray() { return $this->elements; }
            public function first() { return reset($this->elements); }
            public function last() { return end($this->elements); }
            public function key() { return key($this->elements); }
            public function current() { return current($this->elements); }
            public function next() { return next($this->elements); }
            public function exists(\Closure $p) { foreach ($this->elements as $key => $element) { if ($p($key, $element)) return true; } return false; }
            public function filter(\Closure $p) { return new ArrayCollection(array_filter($this->elements, $p)); }
            public function forAll(\Closure $p) { foreach ($this->elements as $key => $element) { if (!$p($key, $element)) return false; } return true; }
            public function map(\Closure $func) { return new ArrayCollection(array_map($func, $this->elements)); }
            public function partition(\Closure $p) { $matches = $noMatches = []; foreach ($this->elements as $key => $element) { if ($p($key, $element)) { $matches[$key] = $element; } else { $noMatches[$key] = $element; } } return [new ArrayCollection($matches), new ArrayCollection($noMatches)]; }
            public function indexOf($element) { return array_search($element, $this->elements, true); }
            public function slice($offset, $length = null) { return array_slice($this->elements, $offset, $length, true); }
        }
    }
}

namespace Doctrine\DBAL\Types {
    if (!class_exists('Doctrine\DBAL\Types\Types')) {
        class Types {
            const TEXT = 'text';
            const DECIMAL = 'decimal';
        }
    }
}

namespace Doctrine\ORM\Mapping {
    if (!class_exists('Doctrine\ORM\Mapping\Entity')) { class Entity { public function __construct($repositoryClass = null) {} } }
    if (!class_exists('Doctrine\ORM\Mapping\Table')) { class Table { public function __construct($name = null) {} } }
    if (!class_exists('Doctrine\ORM\Mapping\Id')) { class Id {} }
    if (!class_exists('Doctrine\ORM\Mapping\GeneratedValue')) { class GeneratedValue {} }
    if (!class_exists('Doctrine\ORM\Mapping\Column')) { class Column { public function __construct($type = null, $length = null, $unique = false, $nullable = false, $precision = null, $scale = null) {} } }
    if (!class_exists('Doctrine\ORM\Mapping\OneToMany')) { class OneToMany { public function __construct($mappedBy = null, $targetEntity = null) {} } }
    if (!class_exists('Doctrine\ORM\Mapping\ManyToOne')) { class ManyToOne { public function __construct($inversedBy = null, $targetEntity = null) {} } }
    if (!class_exists('Doctrine\ORM\Mapping\JoinColumn')) { class JoinColumn { public function __construct($nullable = true) {} } }
}
