<?php

namespace Solspace\Freeform\Bundles\Transformers\Builder\Form;

use Solspace\Freeform\Bundles\Fields\AttributeProvider;
use Solspace\Freeform\Library\Composer\Components\Form;
use Solspace\Freeform\Services\Form\FieldsService;
use Solspace\Freeform\Services\Form\LayoutsService;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class FormTransformer
{
    public function __construct(
        private AttributeProvider $attributeProvider,
        private PropertyAccessor $propertyAccess,
        private FieldsService $fieldsService,
        private LayoutsService $layoutsService,
        private FieldTransformer $fieldTransformer,
        private LayoutTransformer $layoutTransformer,
    ) {
    }

    public function transformList(array $forms): array
    {
        return array_map(
            [$this, 'transformBasic'],
            $forms
        );
    }

    public function transform(Form $form): object
    {
        $transformed = $this->transformBasic($form);

        $fields = $this->fieldsService->getFields($form);

        $id = $form->getId();
        $transformed->layout = (object) [
            'fields' => array_map([$this->fieldTransformer, 'transform'], $fields),
            'pages' => array_map(
                [$this->layoutTransformer, 'transformPage'],
                $this->layoutsService->getPages($id),
            ),
            'layouts' => array_map(
                [$this->layoutTransformer, 'transformLayout'],
                $this->layoutsService->getLayouts($id)
            ),
            'rows' => array_map(
                [$this->layoutTransformer, 'transformRow'],
                $this->layoutsService->getRows($id)
            ),
            'cells' => array_map(
                [$this->layoutTransformer, 'transformCell'],
                $this->layoutsService->getCells($id)
            ),
        ];

        return $transformed;
    }

    private function transformBasic(Form $form): object
    {
        $typeClass = \get_class($form);
        $editableProperties = $this->attributeProvider->getEditableProperties($typeClass);

        $properties = [];
        foreach ($editableProperties as $property) {
            $handle = $property->handle;
            $properties[$handle] = $this->propertyAccess->getValue($form, $handle);
        }

        return (object) [
            'id' => $form->getId(),
            'uid' => $form->getUid(),
            'type' => $typeClass,
            'properties' => $properties,
        ];
    }
}
