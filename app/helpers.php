<?php

function array_every(Iterable $array, Closure $predicate) 
{
    foreach ($array as $item) {
        if (call_user_func($predicate, $item) !== true) {
            return false;
        }
    }
    return true;
}

function array_some(Iterable $array, Closure $predicate) 
{
    foreach ($array as $item) {
        if (call_user_func($predicate, $item) === true) {
            return true;
        }
    }
    return false;
}

function test_array_keys(array $array, array $keys, $strict = false) 
{
    if (array_diff(array_keys($array), $keys) !== []) {
        return false;
    }
    return !$strict || array_diff($keys, array_keys($array)) === [];
}