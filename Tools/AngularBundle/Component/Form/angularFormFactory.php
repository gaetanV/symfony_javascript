<?php

namespace Tools\AngularBundle\Component\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\Form\ResolvedFormTypeFactoryInterface;

class angularFormFactory implements FormFactoryInterface
{
    private $registry;
    
    public function __construct(FormRegistryInterface $registry, ResolvedFormTypeFactoryInterface $resolvedTypeFactory)
    {
        $this->registry = $registry;
        $this->resolvedTypeFactory = $resolvedTypeFactory;
    }
    
    /**
     * {@inheritdoc}
     */
    public function create($type = 'Symfony\Component\Form\Extension\Core\Type\FormType', $data = null, array $options = array())
    {
        return $this->createBuilder($type, $data, $options)->getForm();
    }

    /**
     * {@inheritdoc}
     */
    public function createNamed($name, $type = 'Symfony\Component\Form\Extension\Core\Type\FormType', $data = null, array $options = array())
    {
        return $this->createNamedBuilder($name, $type, $data, $options)->getForm();
    }

    /**
     * {@inheritdoc}
     */
    public function createForProperty($class, $property, $data = null, array $options = array())
    {
        return $this->createBuilderForProperty($class, $property, $data, $options)->getForm();
    }

    /**
     * {@inheritdoc}
     */
    public function createBuilder($type = 'Symfony\Component\Form\Extension\Core\Type\FormType', $data = null, array $options = array())
    {
        if (!is_string($type)) {
            throw new UnexpectedTypeException($type, 'string');
        }

        return $this->createNamedBuilder($this->registry->getType($type)->getBlockPrefix(), $type, $data, $options);
    }

     /**
     * {@inheritdoc}
     */
    public function createNamedBuilder($name, $type = 'Symfony\Component\Form\Extension\Core\Type\FormType', $data = null, array $options = array())
    {
        if (null !== $data && !array_key_exists('data', $options)) {
            $options['data'] = $data;
        }

        if (!is_string($type)) {
            throw new UnexpectedTypeException($type, 'string');
        }

        $type = $this->registry->getType($type);
        dump($type->getTypeExtensions());
        return "";
    }

    /**
     * {@inheritdoc}
     */
    public function createBuilderForProperty($class, $property, $data = null, array $options = array())
    {
        if (null === $guesser = $this->registry->getTypeGuesser()) {
            return $this->createNamedBuilder($property, 'Symfony\Component\Form\Extension\Core\Type\TextType', $data, $options);
        }

        $typeGuess = $guesser->guessType($class, $property);
        $maxLengthGuess = $guesser->guessMaxLength($class, $property);
        $requiredGuess = $guesser->guessRequired($class, $property);
        $patternGuess = $guesser->guessPattern($class, $property);

        $type = $typeGuess ? $typeGuess->getType() : 'Symfony\Component\Form\Extension\Core\Type\TextType';

        $maxLength = $maxLengthGuess ? $maxLengthGuess->getValue() : null;
        $pattern = $patternGuess ? $patternGuess->getValue() : null;

        if (null !== $pattern) {
            $options = array_replace_recursive(array('attr' => array('pattern' => $pattern)), $options);
        }

        if (null !== $maxLength) {
            $options = array_replace_recursive(array('attr' => array('maxlength' => $maxLength)), $options);
        }

        if ($requiredGuess) {
            $options = array_merge(array('required' => $requiredGuess->getValue()), $options);
        }

        // user options may override guessed options
        if ($typeGuess) {
            $options = array_merge($typeGuess->getOptions(), $options);
        }

        return $this->createNamedBuilder($property, $type, $data, $options);
    }
  
}