# PHP.Vdump

Another var_dump();

## usage

## v - Prints human-readable information about a variable 
v($val1, $val2 ...);


	$data = array(3) {
	  [0]=>
	  int(1)
	  [1]=>
	  int(2)
	  [2]=>
	  array(1) {
	    ["fruit"]=>
	    string(6) "banana"
	  }
	}
	in /path/to/script/01-v-sample.php on line 7
	array(0) {
	}
	 $name = string(3) "Ray"
	in /path/to/script/01-v-sample.php on line 7


## vargs - Prints human-readable information about a method calling 
vargs();

	$model->create($namae, array($namae, 'fruit' => 'banna')); // PHP_Vdaump_Test_Model::create
	public function create($name, array $food)
	array (
	  'name' => 'ray',
	  'food' => 
	  array (
	    0 => 'ray',
	    'fruit' => 'banna',
	  ),
	)in /path/to/script/02-vargs-sample.php on line 22
