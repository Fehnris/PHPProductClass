# PHPProductClass
A Class for representing a product on a website.  It implements mysqli prepared statements to get information from a Mysql database and stores the retrieved data as properties of the object.
## Extendible
The class can be extended to include more specific methods and properties for specific product types.
## Class use example
include("Product.php");

$some_product = new Product($searchArray, $dbAttributes, [mysqli conn]);

### Explanation of input variables

#### $searchArray
An associative array containing information about selecting a specific product.  To accomplish this, search fields can be specified to have the class 'match' products against search fields.  Search fields can be specified using an associative array with the following fields.

##### 'fieldName'
The name of a database table field to be used in a select query after a 'where' statement.
##### 'fieldValue'
The value to search for in 'fieldName'.
##### 'fieldType'
Can be 1 of 4 values 'b' 'd' 'i' 's'.  This value needs to match the data type of 'fieldValue'.  If 'fieldValue' stores an integer then 'fieldType' needs to be 'i'.  These values correspond to Mysqli's bind_param() function and are used internally with this method to create prepared statements.
##### Variable use example 1 (single search field)
$searchArray = array('fieldName'=>'id', 'fieldValue'=>4, 'fieldType'=>'i');
##### Variable use example 2 (multiple search fields)
Multiple search fields can be specified by creating an array containing each of the search arrays as below.

$searchArray[0] = array('fieldName'=>'id', 'fieldValue'=>4, 'fieldType'=>'i');

$searchArray[1] = array('fieldName'=>'name', 'fieldValue'=>'Albatross', 'fieldType'=>'s');
#### $dbAttributes
An associative array to contain details about a product's database table.  The class by default looks for a database table called 'Products' with a primary field called 'id'.  If products are stored in a database table with these parameters this variable can be left as a blank variable.
##### Variable use example
$dbAttributes = array('TableName'=>'Products', 'UniqueIdentifierField'=>'id', 'FieldsToReturn'=>'*'); (Default values)

#### [mysqli conn]
This variable needs to be set to a valid mysqli connection.

### Dependancies
This class can work on its own or along side another PHP class from my other repository 'PHPWebsiteClass'.  This other class can create the neccessary mysqli connection which can be used as an input to the third variable '[mysqli conn]'.
