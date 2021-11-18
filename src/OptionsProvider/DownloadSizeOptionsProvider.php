<?php


namespace Pimcorecasts\Bundle\QrCode\OptionsProvider;


use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;


class DownloadSizeOptionsProvider implements SelectOptionsProviderInterface
{
    /**
     * @param array $context
     * @param Data $fieldDefinition
     * @return array
     */
    public function getOptions($context, $fieldDefinition)
    {
        $object = isset( $context['object'] ) ? $context['object'] : null;
        $result = [];
        foreach( [ 150, 300, 600 ] as $size ){
            $result[] = [
                'key' => $size . 'px',
                'value' => $size
            ];
        }

        return $result;
    }

     /**
     * Returns the value which is defined in the 'Default value' field
     * @param array $context
     * @param Data $fieldDefinition
     * @return mixed
     */
    public function getDefaultValue($context, $fieldDefinition) {
        return $fieldDefinition->getDefaultValue();
    }

    /**
     * @param array $context
     * @param Data $fieldDefinition
     * @return bool
     */
    public function hasStaticOptions($context, $fieldDefinition) {
        return true;
    }
}
