<?php
namespace AddressBook;

/**
 * AddressBook Library
 *
 * This class is the main entry point of an Address Book instance. Any person or group is related or to be searched without this class.
 *
 * @Usage
 *<code>$aBook = new AddressBook();</code>
 *or with namespaced version
 *<code>$aBook = new AddressBook/AddressBook();</code>
 *
 */
class AddressBook
{
    /**
     * Persons Collection
     * @var array
     */
    private $persons;
    /**
     * Groups Collection
     * @var array
     */
    private $groups;
    /**
     * Relations array of Persons and Groups
     * @var array
     */
    protected $person_group_relations;
    /**
     * Used for checking the child parent relation uniqueness
     * @var string
     */
    protected $id;
    /**
     * Constructor of AddressBook Class
     */
    public function __construct()
    {
        // initialize class members
        $this->id                     = uniqid();
        $this->persons                = [];
        $this->groups                 = [];
        $this->person_group_relations = [];
    }

    /**
     *  Adds a person to the address book and returns the created Person Object
     *  @param string $first_name The Person's first name
     *  @param string $last_name  The Person's last name
     *  @return Person The created Person Object
     *  @example
     *  // create an address book<br />
     *  $aBook  = new AddressBook();<br />
     *  // add "darth vader" to the address book, and return the instance<br />
     *  $dVader = $aBook->addPerson('darth','vader');<br />
     *  // add "darth.vader@sith.com" to "darth vader"'s email list'<br />
     *  $dVader->addEmail('darth.vader@sith.com');
     *  @throws \Exception when any of the inputs are empty
     */
    public function addPerson($first_name, $last_name)
    {
        $person = $this->getPerson($first_name, $last_name);
        if ($person instanceof Person) {
            return $person;
        } else {
            $person          = new Person($this, $first_name, $last_name);
            $this->persons[] = $person;
            return $person;
        }
    }

    /**
     * Searches for the exact match of name and surname and  the Person Object if it exists, or false if it doesn't
     * @param  string $first_name First name query
     * @param  string $last_name  Last name query
     * @return Person             The related Person Object
     * @return false              If nothing is found
     * @example
     * // create an adress book <br>
     * $aBook  = new AddressBook();<br />
     * // add "darth vader" to the address book, and return the instance<br>
     * $dVader = $aBook->addPerson('darth','vader');<br />
     * // find and return the record in adressbook for the person whose first name is 'darth' and the last name is 'vader'<br>
     * // returns the $dVader instance<br>
     * $aBook->getPerson('darth','vader');<br>
     * // find and return the record in addressbook for the person whose first name is 'darth' and the last name is ''<br>
     * // returns false<br>
     * $aBook->getPerson('darth','');
     */
    public function getPerson($first_name, $last_name)
    {
        if (empty(trim($first_name)) || empty(trim($first_name))) {
            return false;
        } else {
            $persons = array_values(array_filter($this->persons, function ($person) use ($first_name, $last_name) {
                return $person->getFirstName() == $first_name && $person->getLastName() == $last_name;
            }));
            if (count($persons) == 1) {
                return $persons[0];
            } else {
                return false;
            }
        }
    }

    /**
     * Finds a person with the first name, or with the last name, or with both, or with emails starting with/equal to given input
     * @param  string $first_name The name query
     * @param  string $last_name  The surname query
     * @param  string $email      Email query
     * @return Person[]           Array of Persons
     * @example
     * // create an address book<br>
     * $aBook  = new AddressBook();<br />
     * // add 'darth vader' to the address book and return the added Person instance<br>
     * $dVader = $aBook->addPerson('darth','vader');<br />
     * // add this person an email address<br>
     * $dVader->addEmail('darth.vader@sith.com')<br>
     * // search for persons whose name is 'darth'<br>
     * // returns $dVader<br>
     * $aBook->searchPerson('darth','','');<br />
     * // search for persons whose surname is 'vader'<br>
     * // returns $dVader<br>
     * $aBook->searchPerson('','vader','');<br />
     * // search for persons whose have an email starting with 'darth.vader'
     * // retuns $dVader<br>
     * $aBook->searchPerson('','','darth.vader');<br>
     */
    public function searchPerson($first_name, $last_name, $email)
    {
        return array_filter($this->persons, function ($person) use ($first_name, $last_name, $email) {
            return (!empty(trim($last_name)) && !empty(trim($first_name)) && $person->getFirstName() == $first_name && $person->getLastName() == $last_name) ||
                (empty(trim($last_name)) && !empty(trim($first_name)) && $person->getFirstName() == $first_name) ||
                (empty(trim($first_name)) && !empty(trim($last_name)) && $person->getLastName() == $last_name) ||
                (!empty(trim($email)) && $person->searchEmail($email) == true);
        });
    }

    /**
     * Adds a new group to the address book, and returns the created Group instance
     * @param string $name The desired group name
     * @return Group The created or found group
     * @example
     * // create an address book<br />
     * $aBook  = new AddressBook();<br />
     * // add a new group named 'jedi' to the address book, and return the group instance<br>
     * $jedi = $aBook->addGroup('jedi');<br />
     * // add a person to the address book<br>
     * $obiWan = $aBook->addPerson('obi wan','kenobi');<br>
     * // add this person to the group<br>
     * $obiWan->addToGroup($jedi);<br>
     * // or<br>
     * $obiWan->addToGroup($aBook->getGroup("jedi"));<br>
     * // or<br>
     * $aBook->getPerson('obi wan','kenobi')->addToGroup($jedi);<br>
     * // or<br>
     * $aBook->getPerson('obi wan','kenobi')->addToGroup($aBook->getGroup("jedi"));<br>
     * @throws \Exception when the user string is empty
     */
    public function addGroup($name)
    {
        $group = $this->getGroup($name);
        if ($group instanceof Group) {
            return $group;
        } else {
            $group          = new Group($this, $name);
            $this->groups[] = $group;
            return $group;
        }
    }

    /**
     * Searchs for a single group and returns the found instance if it exists, or false if it doesn't
     * @param  string $name The group name which should be returned
     * @return Group        Group Object or boolean false
     * @example
     * // create an address book<br />
     * $aBook  = new AddressBook();<br />
     * // add a new group named 'jedi' to the address book, and return the group instance<br>
     * $jedi = $aBook->addGroup('jedi');<br />
     * // get this group from address book<br>
     * $found_jedi = $aBook->getGroup('jedi');<br> // $jedi
     * // get another group from address book <br>
     * $found_sith = $aBook->getGroup('sith');<br> // false
     */
    public function getGroup($name)
    {
        $groups = array_values(array_filter($this->groups, function ($group) use ($name) {
            return $group->getGroupName() == $name;
        }));
        if (count($groups) == 1) {
            return $groups[0];
        } else {
            return false;
        }
    }
}
