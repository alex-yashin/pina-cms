<?php


namespace PinaCMS\Types;


use Pina\App;
use PinaCMS\ResourceTypeFactory;
use Exception;
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
        /** @var ResourceTypeFactory $factory */
        $factory = App::load(ResourceTypeFactory::class);
        $types = $factory->get();

        $rs = [];
        foreach ($types as $type) {
            try {
                $rs[] = [
                    'id' => $type,
                    'title' => $factory->makeType($type)->getTitle(),
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
        /** @var ResourceTypeFactory $factory */
        $factory = App::load(ResourceTypeFactory::class);
        return $factory->makeType($value)->getTitle();
    }

    /**
     * @param mixed $value
     * @param bool $isMandatory
     * @return mixed
     * @throws Exception
     */
    public function normalize($value, $isMandatory)
    {
        /** @var ResourceTypeFactory $factory */
        $factory = App::load(ResourceTypeFactory::class);
        $types = $factory->get();

        if (!in_array($value, $types)) {
            throw new ValidateException(__("Выберите значение"));
        }

        return $value;
    }

    public function getSize(): int
    {
        return 12;
    }

    public function getSQLType(): string
    {
        return "varchar(" . $this->getSize() . ")";
    }
}