<?php

namespace Cyve\JsonSchemaFormBundle\Form\Helper;

use Cyve\JsonSchemaFormBundle\Form\Type\SchemaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Cyve\JsonSchemaFormBundle\Validator\Constraint\Schema;

class FormHelper
{
    /**
     * @param object $schema
     * @return string|null
     */
    public static function resolveFormType($schema): ?string
    {
        if (!isset($schema->widget) || !isset($schema->widget->id)) {
            return null;
        }

        switch ($schema->widget->id) {
            case 'select':
            case 'radio':
                return ChoiceType::class;
            case 'object':
                return SchemaType::class;
            case 'number':
                return NumberType::class;
            default:
                return null;
        }
    }

    /**
     * @param object $schema
     * @return array
     */
    public static function resolveFormOptions($schema): array
    {
        $options = [];

        if (isset($schema->title)) {
            $options['label'] = $schema->title;
        }

        if (isset($schema->description)) {
            $options['help'] = $schema->description;
        }

        if (isset($schema->placeholder)) {
            $options['empty_data'] = $schema->placeholder;
        }

        if (isset($schema->oneOf)) {
            $tab = [];
            foreach ($schema->oneOf as $oneOf) {
                foreach ($oneOf->enum as $value) {
                    $tab[$oneOf->description] = $value;
                    if (isset($schema->default) && $schema->default == $value) {
                        $options['data'] = $value;
                    }
                }
            }
            $options = $options + ['choices' => $tab];
        }

        if (!isset($schema->widget) || !isset($schema->widget->id)) {
            return null;
        }

        switch ($schema->widget->id) {
            case 'radio':
                return  $options + [
                    'expanded' => true,
                    'multiple' => false,
                    'required' => true,
                ];
            case 'select':
                return  $options + [
                    'expanded' => false,
                    'multiple' => false,
                    'required' => true,
                ];
            case 'object':
                return $options + [
                    'data_schema' => $schema,
                    'data' => $schema,
                ];
            default:
                return $options;
        }
    }
}
