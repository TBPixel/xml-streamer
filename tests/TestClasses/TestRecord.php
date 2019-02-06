<?php declare(strict_types=1);

namespace TBPixel\XMLStreamer\Tests\TestClasses;

use TBPixel\XMLStreamer\CreateFromSimpleXML;

final class TestRecord implements CreateFromSimpleXML
{
    /** @var int */
    public $id;

    /** @var string */
    public $firstName;

    /** @var string */
    public $lastName;

    /** @var string */
    public $email;

    /** @var string */
    public $gender;

    /** @var string */
    public $ip;

    public function __construct(int $id, string $firstName, string $lastName, string $email, string $gender, string $ip)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->gender = $gender;
        $this->ip = $ip;
    }

    public static function fromSimpleXML(\SimpleXMLElement $element)
    {
        return new static(
            (int) $element->id,
            (string) $element->first_name,
            (string) $element->last_name,
            (string) $element->email,
            (string) $element->gender,
            (string) $element->ip_address
        );
    }
}
