<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Reflection;

use Nette;
use Nette\Utils\ObjectMixin;


/**
 * Reports information about a method.
 * @property-read array $defaultParameters
 * @property-read ClassType $declaringClass
 * @property-read Method $prototype
 * @property-read Extension $extension
 * @property-read Parameter[] $parameters
 * @property-read IAnnotation[][] $annotations
 * @property-read string $description
 * @property-read bool $public
 * @property-read bool $private
 * @property-read bool $protected
 * @property-read bool $abstract
 * @property-read bool $final
 * @property-read bool $static
 * @property-read bool $constructor
 * @property-read bool $destructor
 * @property-read int $modifiers
 * @property-write bool $accessible
 * @property-read bool $closure
 * @property-read bool $deprecated
 * @property-read bool $internal
 * @property-read bool $userDefined
 * @property-read string $docComment
 * @property-read int $endLine
 * @property-read string $extensionName
 * @property-read string $fileName
 * @property-read string $name
 * @property-read string $namespaceName
 * @property-read int $numberOfParameters
 * @property-read int $numberOfRequiredParameters
 * @property-read string $shortName
 * @property-read int $startLine
 * @property-read array $staticVariables
 */
class Method extends \ReflectionMethod
{

	/**
	 * @param  string|object
	 * @param  string
	 * @return self
	 */
	public static function from($class, $method)
	{
		return new static(is_object($class) ? get_class($class) : $class, $method);
	}


	/**
	 * @deprecated
	 */
	public function toCallback()
	{
		return new Nette\Callback(parent::getDeclaringClass()->getName(), $this->getName());
	}


	public function __toString()
	{
		return parent::getDeclaringClass()->getName() . '::' . $this->getName() . '()';
	}


	public function getClosure($object = NULL)
	{
		return PHP_VERSION_ID < 50400
			? Nette\Utils\Callback::closure($object ?: parent::getDeclaringClass()->getName(), $this->getName())
			: parent::getClosure($object);
	}


	/********************* Reflection layer ****************d*g**/


	/**
	 * @return ClassType
	 */
	public function getDeclaringClass()
	{
		return new ClassType(parent::getDeclaringClass()->getName());
	}


	/**
	 * @return self
	 */
	public function getPrototype()
	{
		$prototype = parent::getPrototype();
		return new static($prototype->getDeclaringClass()->getName(), $prototype->getName());
	}


	/**
	 * @return Extension
	 */
	public function getExtension()
	{
		return ($name = $this->getExtensionName()) ? new Extension($name) : NULL;
	}


	/**
	 * @return Parameter[]
	 */
	public function getParameters()
	{
		$me = array(parent::getDeclaringClass()->getName(), $this->getName());
		foreach ($res = parent::getParameters() as $key => $val) {
			$res[$key] = new Parameter($me, $val->getName());
		}
		return $res;
	}


	/********************* Nette\Annotations support ****************d*g**/


	/**
	 * Has method specified annotation?
	 * @param  string
	 * @return bool
	 */
	public function hasAnnotation($name)
	{
		$res = AnnotationsParser::getAll($this);
		return !empty($res[$name]);
	}


	/**
	 * Returns an annotation value.
	 * @param  string
	 * @return IAnnotation
	 */
	public function getAnnotation($name)
	{
		$res = AnnotationsParser::getAll($this);
		return isset($res[$name]) ? end($res[$name]) : NULL;
	}


	/**
	 * Returns all annotations.
	 * @return IAnnotation[][]
	 */
	public function getAnnotations()
	{
		return AnnotationsParser::getAll($this);
	}


	/**
	 * Returns value of annotation 'description'.
	 * @return string
	 */
	public function getDescription()
	{
		return $this->getAnnotation('description');
	}


	/********************* Nette\Object behaviour ****************d*g**/


	public function __call($name, $args)
	{
		return ObjectMixin::call($this, $name, $args);
	}


	public function &__get($name)
	{
		return ObjectMixin::get($this, $name);
	}


	public function __set($name, $value)
	{
		ObjectMixin::set($this, $name, $value);
	}


	public function __isset($name)
	{
		return ObjectMixin::has($this, $name);
	}


	public function __unset($name)
	{
		ObjectMixin::remove($this, $name);
	}

}
