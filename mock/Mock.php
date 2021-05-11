<?php

# ugly trick to resolve issue with "not normal" fuel's loader

if (!interface_exists('Sanitization')) {
    interface Sanitization {}
}

if (!class_exists('Db')) {
    class Db {
        public static function list_columns() {}
    }
}

if (!class_exists('Inflector')) {
    class Inflector {
        public static function tableize() {
            return [];
        }
    }
}
