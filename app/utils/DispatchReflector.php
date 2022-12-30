<?php


class DispatchReflector
{
    /**
     * @param string $className
     * @return bool
     * @throws ReflectionException
     */
    public static function hasDispatchedMethods(string $className)
    {
        $t = new ReflectionClass($className);
        return strpos($t->getDocComment(), "@dispatch") !== false;
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return bool
     * @throws ReflectionException
     */
    public static function isDispatchedMethod(string $className, string $methodName)
    {
        $t = new ReflectionClass($className);
        return strpos($t->getDocComment(), "@dispatch $methodName") !== false;
    }
}