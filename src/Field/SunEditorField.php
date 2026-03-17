<?php

declare(strict_types=1);

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * Champ EasyAdmin utilisant SunEditor WYSIWYG.
 * Initialisé par assets/admin.js via la classe CSS 'ea-suneditor-field'.
 */
final class SunEditorField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/text_editor')
            ->setFormType(TextareaType::class)
            ->setFormTypeOption('attr', [
                'class'                         => 'ea-suneditor-field',
                'data-suneditor-upload-url'     => '/admin/media/upload',
                'data-suneditor-height'         => '450',
            ])
            ->addCssClass('field-suneditor')
            ->setDefaultColumns('col-md-11 col-xxl-9')
        ;
    }

    public function setHeight(int $height): self
    {
        $attr = $this->dto->getFormTypeOption('attr') ?? [];
        $attr['data-suneditor-height'] = (string) $height;
        $this->setFormTypeOption('attr', $attr);

        return $this;
    }
}
