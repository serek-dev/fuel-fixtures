<?php

namespace Orm;

# ugly trick to resolve issue with "not normal" fuel's loader

if (!class_exists('Orm\Model')) {

    class Model implements \ArrayAccess, \Iterator
    {
        private array $_data;

        protected static $_primary_key = ['id'];
        private bool $isNew;

        public function __construct($data = [], $new = true, $view = null, $cache = true)
        {
            $this->_data = $data;
            $this->isNew = $new;
        }

        public function current()
        {
            return current($this->_data);
        }

        public function next()
        {
            return next($this->_data);
        }

        public function key()
        {
            return key($this->_data);
        }

        public function valid()
        {
            return $this->offsetExists(0);
        }

        public function rewind()
        {

        }

        public function offsetExists($offset)
        {
            return isset($this->_data[$offset]);
        }

        public function offsetGet($offset)
        {
            if ($offset === 'id') {
                return rand(1, 1000);
            }
            return $this->_data[$offset];
        }

        public function offsetSet($offset, $value)
        {
            $this->_data[$offset] = $value;
        }

        public function offsetUnset($offset)
        {
            unset($this->_data[$offset]);
        }

        public function to_array(): array
        {
            return $this->_data;
        }

        public function save($cascade = null, $use_transaction = false)
        {

        }

        public function __set($name, $value)
        {
            $this->offsetSet($name, $value);
        }

        public function __get($name)
        {
            return $this->offsetGet($name);
        }

        /**
         * Get the primary key(s) of this class
         *
         * @return  array
         */
        public static function primary_key()
        {
            return static::$_primary_key;
        }

        /**
         * Provide the identifying details in the form of an array
         *
         * @return array
         */
        public function get_pk_assoc()
        {
            $array = array_flip(static::primary_key());

            foreach ($array as $key => &$value)
            {
                $value = rand(1, 1000);
            }

            return $array;
        }

        public function is_new()
        {
            return $this->isNew;
        }
    }
}
