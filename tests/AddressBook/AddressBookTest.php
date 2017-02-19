<?php

/**
 * Required features (these need to be unit tested):
 *  - Add a person to the address book.
 *  - Add a group to the address book.
 *  - Given a group we want to easily find its members.
 *  - Given a person we want to easily find the groups the person belongs to.
 *  - Find person by name (can supply either first name, last name, or both).
 *  - Find person by email address (can supply either the exact string or a prefix string, ie. both "alexander@company.com" and "alex" should work).
 */

class Tests extends PHPUnit_Framework_TestCase
{

    public function testAddPersonToAddressBook()
    {
        $ABook = new AddressBook\AddressBook();
        $this->assertInstanceOf(
            AddressBook\Person::class,
            $ABook->addPerson("Darth", "Vader")
        );
        $this->expectException(\Exception::class);
        $ABook->addPerson("", "Skywalker");
        $this->expectException(\Exception::class);
        $ABook->addPerson("Anakin", "");
        $this->assertInstanceOf(
            AddressBook\Person::class,
            $ABook->getPerson("Darth", "Vader")
        );
    }

    public function testAddGroupToAddressBook()
    {
        $ABook = new AddressBook\AddressBook();
        $this->assertInstanceOf(
            AddressBook\Group::class,
            $ABook->addGroup("Siths")
        );
        $this->assertInstanceOf(
            AddressBook\Group::class,
            $ABook->getGroup("Siths")
        );
    }

    public function testFindGroupUsers()
    {
        $ABook = new AddressBook\AddressBook();
        $ABook->addPerson("Darth", "Vader");
        $ABook->addPerson("Obi wan", "Kenobi");
        $ABook->addGroup("Siths");
        $ABook->getPerson("Darth", "Vader")->addToGroup($ABook->getGroup("Siths"));
        $this->assertEqualsArrays(
            $ABook->getGroup("Siths")->getMembers(),
            [$ABook->getPerson("Darth", "Vader")]
        );
    }

    public function testFindUserGroups()
    {
        $ABook = new AddressBook\AddressBook();
        $ABook->addPerson("Darth", "Vader");
        $ABook->addPerson("Obi wan", "Kenobi");
        $ABook->addGroup("Lightsaber Holders");
        $ABook->addGroup("Imperial Force");
        $ABook->addGroup("Siths");
        $ABook->addGroup("Commanders");
        $ABook->getPerson("Darth", "Vader")->addToGroup($ABook->getGroup("Imperial Force"));
        $ABook->getPerson("Darth", "Vader")->addToGroup($ABook->getGroup("Siths"));
        $this->assertEqualsArrays(
            $ABook->getPerson("Darth", "Vader")->getGroups(),
            [
                $ABook->getGroup("Siths"),
                $ABook->getGroup("Imperial Force")
            ]
        );
    }

    public function testFindUsers()
    {
        $ABook = new AddressBook\AddressBook();
        $ABook->addPerson("Darth", "Vader");
        $ABook->addPerson("Obi wan", "Kenobi");
        $ABook->addPerson("John", "Doe");
        $ABook->addPerson("Jane", "Doe");
        $ABook->addPerson("Darth", "Maul");

        $this->assertEqualsArrays(
            $ABook->searchPerson("Darth", "Vader", ""),
            [$ABook->getPerson("Darth", "Vader")]
        );

        $this->assertEqualsArrays(
            $ABook->searchPerson("Obi wan", "", ""),
            [$ABook->getPerson("Obi wan", "Kenobi")]
        );

        $this->assertEqualsArrays(
            $ABook->searchPerson("Darth", "", ""),
            [$ABook->getPerson("Darth", "Vader"), $ABook->getPerson("Darth", "Maul")]
        );

        $this->assertEqualsArrays(
            [$ABook->getPerson("John", "Doe"), $ABook->getPerson("Jane", "Doe")],
            $ABook->searchPerson("", "Doe", "")
        );

        $this->assertNotEqualsArrays(
            [$ABook->getPerson("John", "Doe"), $ABook->getPerson("Jane", "Doe")],
            $ABook->searchPerson("John", "Doe", "")
        );
    }

    public function testFindUsersByEmailAddress()
    {
        $ABook = new AddressBook\AddressBook();
        $ABook->addPerson("Darth", "Vader");
        $ABook->addPerson("Obi wan", "Kenobi");
        $ABook->addPerson("John", "Doe");

        $ABook->getPerson("Darth", "Vader")->addEmail("darth.vader@gmail.com");
        $ABook->getPerson("Darth", "Vader")->addEmail("commander@deathstar.com");
        $ABook->getPerson("Obi wan", "Kenobi")->addEmail("obiwan@jedimasters.com");
        $ABook->getPerson("John", "Doe")->addEmail("obidoe@missing.com");

        $this->assertEqualsArrays(
            [
                $ABook->getPerson("Darth", "Vader"),
            ],
            $ABook->searchPerson("", "", "commander@deathstar.com")
        );

        $this->assertEqualsArrays(
            [
                $ABook->getPerson("Darth", "Vader"),
            ],
            $ABook->searchPerson("", "", "darth.vader")
        );

        $this->assertEqualsArrays(
            [
                $ABook->getPerson("Obi wan", "Kenobi"),
                $ABook->getPerson("John", "Doe"),
            ],
            array_values($ABook->searchPerson("", "", "obi"))
        );
    }

    private function assertEqualsArrays($tested, $expected)
    {
    	sort($tested);
    	sort($expected);
        return $this->assertEquals(md5(serialize(array_values($tested))), md5(serialize(array_values($expected))));
    }

    private function assertNotEqualsArrays($tested, $expected)
    {
    	sort($tested);
    	sort($expected);
        return $this->assertNotEquals(md5(serialize(array_values($tested))), md5(serialize(array_values($expected))));
    }
}
