<?php
namespace AddressBook;

/**
 * Person Object
 *
 * Should only be instantiated by an AddressBook instance. 
 *
 * @Usage
 *
 * <code>
 * $aBook = new AddressBook();
 * $luke = $aBook->addPerson("Luke","Skywalker");
 * // $luke is a `Person` instance.
 * $luke = $aBook->getPerson("Luke","Skywalker");
 * </code>
 */
class Person extends AddressBook
{
    /**
     * First name of the person
     * @var string
     */
    private $first_name;
    /**
     * Last name of the person
     * @var string
     */
    private $last_name;
    /**
     * Email addresses of the person
     * @var array
     */
    private $email_addresses;
    /**
     * Phone numbers of the person
     * @var array
     */
    private $phone_numbers;
    /**
     * Adresses of the person
     * @var array
     */
    private $addresses;
    /**
     * Parent instance reference
     * @var AddressBook
     */
    private $parent;

    /**
     * Constructor of the person instance
     * @param AddressBook &$parent    The parent object reference
     * @param string $first_name First name of the new person
     * @param string $last_name  Last name of the new person
     */
    function __construct(&$parent, $first_name, $last_name)
    {
        if (empty(trim($first_name)) || empty(trim($first_name)) || !$parent instanceof AddressBook) {
            throw new \Exception("Error Processing Request: Can't create a new Person with these inputs.", 1);

        } else {
            $this->parent          = $parent;
            $this->first_name      = $first_name;
            $this->last_name       = $last_name;
            $this->email_addresses = [];
            $this->phone_numbers   = [];
            $this->addresses       = [];
        }
    }

    /**
     * Getter of the private first name member
     * @return string First name of the person
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Getter of the private last name member
     * @return string Last name of the person
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Gets the full name (first name + one space character + last name) of the user
     * @return string Concatenated first and last name
     */
    public function getFullName()
    {
        return $this->first_name . " " . $this->last_name;
    }

    /**
     * Getter of the private addresses array
     * @return array Address List of the person
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Getter of the private emails array
     * @return array Email list of the person
     */
    public function getEmails()
    {
        return $this->email_addresses;
    }

    /**
     * Getter of the private phone_numbers array
     * @return array Phone numbers of the user
     */
    public function getPhoneNumbers()
    {
        return $this->phone_numbers;
    }

    /**
     * Adds an email to the person
     * @param string $email The person's email address
     */
    public function addEmail($email)
    {
        $this->email_addresses[] = $email;
    }

    /**
     * Adds an address to the person
     * @param string $address The person's address
     */
    public function addAddress($address)
    {
        $this->addresses[] = $address;
    }

    /**
     * Adds a phone number to the person
     * @param string $number The person's phone number
     */
    public function addPhoneNumbers($number)
    {
        $this->phone_numbers[] = $number;
    }

    /**
     * Searches for the person's email adress if any email saved starts with the given string
     * @param  string $email String to be searched for 
     * @return bool        If person has an email starting with the given string, returns true, otherwise false.
     */
    public function searchEmail($email)
    {
        return count(array_filter($this->email_addresses, function ($email_address) use ($email) {
            return strpos($email_address, $email) === 0;
        })) > 0;
    }

    /**
     * Adds this person to a specific group
     * @param Group $group The group instance which this person will be added
     * @return boolean true on success, otherwise false.
     */
    public function addToGroup($group)
    {
        if ($group instanceof Group) {
            $this->parent->person_group_relations[] = ["person" => $this, "group" => $group];
            return true;
        }
        return false;
    }

    /**
     * Gets the group list which this person belongs to
     * @return Group[] The group instance list of person groups
     */
    public function getGroups()
    {
        $person = $this;
        return array_column(array_filter($this->parent->person_group_relations, function ($relation) use ($person) {
            return $relation["person"] == $person;
        }), "group");
    }

    /**
     * Printout limiter when instance called with print_r or var_dump like debug methods.
     * @return array The output template to be shown within debug commands
     */
    public function __debugInfo()
    {
        return [
            'name'          => $this->getFullName(),
            'phone_numbers' => $this->getPhoneNumbers(),
            'addresses'     => $this->getAddresses(),
            'emails'        => $this->getEmails(),
            'groups'        => $this->getGroups(),
        ];
    }
}
