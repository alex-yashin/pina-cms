<?php


namespace PinaCMS\Types;


use PinaCMS\ResourceTypeFactory;
use PinaCMS\SQL\ResourceTypeGateway;
use Exception;
use Pina\TableDataGateway;
use Pina\Types\DirectoryType;
use Pina\Types\ValidateException;

use function Pina\__;

class ResourceTypeType extends DirectoryType
{
    /**
     * @return array
     * @throws Exception
     */
    public function getVariants()
    {
        $rs = [];
        $types = $this->makeQuery()->get();
        foreach ($types as $type) {
            try {
                $rs[] = [
                    'id' => $type['id'],
                    'title' => ResourceTypeFactory::makeClass($type['class'])->getTitle(),
                ];
            } catch (Exception $e) {
            }
        }
        return $rs;
    }

    /**
     * @param mixed $value
     * @return string
     * @throws Exception
     */
    public function format($value): string
    {
        if (empty($value)) {
            return '';
        }
        $class = $this->makeQuery()->whereId($value)->value('class');
        if (empty($class)) {
            return '';
        }
        return ResourceTypeFactory::makeClass($class)->getTitle();
    }

    /**
     * @param mixed $value
     * @param bool $isMandatory
     * @return mixed
     * @throws Exception
     */
    public function normalize($value, $isMandatory)
    {
        if (!$this->makeQuery()->whereId($value)->exists()) {
            throw new ValidateException(__("Выберите значение"));
        }

        return $value;
    }

    public function getSize()
    {
        return 11;
    }

    public function getSQLType()
    {
        return "int(" . $this->getSize() . ")";
    }

    protected function makeQuery(): TableDataGateway
    {
        return ResourceTypeGateway::instance();
    }
}