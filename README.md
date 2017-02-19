PHP Address Book Library
=====
Overview
-----
This is an address book library implementation with PHP. It supports adding multiple users which have name, email addresses, addresses and phone numbers, also the users can be related with user groups.
>
> For detailed API documentation, please refer to **docs/index.html**
>

Documentation
-----
### AddressBook Class
The **AddressBook** class supports these methods:

- **addUser($first_name, $last_name)**
   Adds an user to the address book
- **getUser($first_name, $last_name)**
   Finds an exact matched user with given `$first_name` and `$last_name`
- **searchUser($first_name, $last_name, $email_start)**
   Finds users with one or more of the given parameters, supports:
   - only first name search, 
   - only last name search
   - only email partial search (uses ***starts with*** logic to compare)
   - any mix of the previous filters
-  **addGroup($group_name)**
   Adds a group to the address book
-  **getGroup($group_name)**
   Gets the exact matched group from the address book
   
### Person Class
The returned `Person` instance has these methods:
-  **getFirstName()**: gets the first name of the person
-  **getLastName()**: gets the last name of the person
-  **getFullName()**: gets the concatenated result of person's first and last name
-  **getEmails()**: gets the email address array of the person
-  **getAddresses()**: gets the address array of the person
-  **getPhoneNumbers()**: gets the phone number array of the person
-  **addEmail($email)**: adds an email address to the person
-  **addAddress($address)**: adds an address to the person
-  **addPhoneNumbers($number)**: adds a phone number to the user
-  **searchEmail($email_part)**: returns true if user has an email starting with $email_path. 
-  **addToGroup($group)**: adds this user to a group defined in the addressbook. The input type is `AddressBook\Group`.
-  **getGroups()**: gets the user's groups in the form of `AddressBook\Group` object array.

### Group Class
The returned `Group` instance has these methods:
- **getGroupName()**: returns the group name
- **setGroupName($group_name)**: alters the group name
- **getMembers()**: gets the group's members in the form of `AddressBook\Person` object array.

## Features
#### Adding A Person to the Address Book
```php
use AddressBook;
$aBook = new AddressBook;
$aBook->addPerson("John","Doe");
```
#### Adding A Group to the Address Book
```php
use AddressBook;
$aBook = new AddressBook;
$aBook->addGroup("Work");
```
#### Given a Group, Finding It's Members
```php
use AddressBook;
$aBook = new AddressBook;
$work = $aBook->addGroup("Work");
$aBook->addPerson("John","Doe")->addToGroup($work);
$aBook->addPerson("Jane","Doe")->addToGroup($work);
$aBook->addPerson("June","Doe")->addToGroup($work);
$work_members = $work->getMembers();
```

#### Given a Person, Finding It's Groups
```php
use AddressBook;
$aBook = new AddressBook;
$john = $aBook->addPerson("John","Doe");
$aBook->addGroup("Work")->addMember($john);
$aBook->addGroup("Coders")->addMember($john);
$aBook->addGroup("Project1")->addMember($john);
$johns_groups = $john->getGroups();
```

#### Find person by name (can supply either first name, last name, or both).
 ```php
use AddressBook;
$aBook = new AddressBook;
$john = $aBook->addPerson("John","Doe");
$john = $aBook->addPerson("Jane","Doe");
$john = $aBook->addPerson("John","Does");
$john = $aBook->addPerson("June","Do");
$search_1 = $aBook->searchPerson("John","",""); 
// returns "John Doe" and "John Does"
$search_2 = $aBook->searchPerson("","Doe",""); 
// returns "John Doe" and "Jane Doe"
$search_3 = $aBook->searchPerson("June","Does",""); 
// returns "June Does"
$search_4 = $aBook->searchPerson("June,"Doe",""); 
// returns false
```
#### Find person by start of the email address 
```php
use AddressBook;
$aBook = new AddressBook;
$john = $aBook->addPerson("John","Doe");
$john->addEmail("john.doe@missing.com");
$john2 = $aBook->addPerson("John","Does");
$john2->addEmail("john.does@missing.com");
$search_1 = $aBook->searchPerson("","","john.doe"); 
// returns "John Doe" and "John Does"
$search_2 = $aBook->searchPerson("","","john.does"); 
// returns only "John Does"
$search_3 = $aBook->searchPerson("","","john.doe@missing.com"); 
// returns "John Doe"
$search_4 = $aBook->searchPerson(","","john.doer"); 
// returns false
```

## Design Only Question
If you want to make the e-mail address search by using parts of the string instead of comparing the start of the e-mail address, you can modify `AddressBook\Person\searchEmail` method to use 
```php
// This check enables the part of email address detection, 
// and not equal to false is added because `>= 0` will also be returning true 
// for "false" values. We only want "0"  and higher to return `true`.
return strpos($email_address, $email) >= 0 && strpos($email_address, $email) !== false;
```
instead of 
```php
// this checks if the query is the start of the email address.
return strpos($email_address, $email) === 0;
```
This will enable the lookup process to return `true` if the string also is a part of the e-mail address.


