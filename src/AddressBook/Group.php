<?php
namespace AddressBook;

/**
 * Group Object
 *
 * Should only be instantiated by an AddressBook instance. 
 *
 * @Usage
 *
 * <code>
 * $aBook = new AddressBook();
 * $jedi = $aBook->addGroup("jedi");
 * // $jedi is a `Group` instance.
 * $jedi = $aBook->getGroup("jedi");
 * </code>
 */
class Group extends AddressBook
{
	// Given Group Name
    private $group_name;
    // Reference to the related Address Book Instance
    private $parent;

    /**
     * Instance constructor with group name initializer parameter
     * @param AddressBook 	&$parent    Reference to it's creator, the AddressBook instance
     * @param string 		$group_name The given group name
     */
    function __construct(&$parent, $group_name)
    {
    	if($parent == null || empty(trim($group_name))){
    		throw new \Exception("Error Processing Request: Can't create a Group instance with this input.", 1);    		
    	}else{
    		$this->parent     = $parent;
        	$this->group_name = $group_name;	
    	}        
    }

    /**
     * Group name setter
     * @param string $group_name The group's name to be changed to
     */
    public function setGroupName($group_name)
    {
        $this->group_name = $group_name;
    }

    /**
     * Group name getter
     * @return string The group's assigned name
     */
    public function getGroupName()
    {
        return $this->group_name;
    }

    /**
     * Adds a person instance to the group
     * @param Person $person The Person Object to be included within this group
     */
    public function addMember($person)
    {
        if ($person instanceof Person) {
            $this->parent->person_group_relations[] = ["person" => $person, "group" => $this];
        }
        return false;
    }

    /**
     * Gets all the members which are in this group as Person instances array
     * @return Person[] The Person instances which are members of this group
     */
    public function getMembers()
    {
        $group = $this;
        return array_column(array_filter($this->parent->person_group_relations, function ($relation) use ($group) {
            return $relation["group"] == $group;
        }), "person");
    }

    /**
     * Printout limiter when instance called with print_r or var_dump like debug methods.
     * @return array The output template to be shown within debug commands
     */
    public function __debugInfo()
    {
        return [
            'name'  => $this->getGroupName(),
            'users' => $this->getMembers(),
        ];
    }
}
