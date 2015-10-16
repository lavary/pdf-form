# pdf-form

A very basic class for filling out PDF forms using PHP and PDFtk Server.


## Usage


To use the class, first we need to make sure it is loaded before using it either by using an autoloader or simply using the `require` directive.

Suppose we have the following PDF form:

![raw-form](raw_form.jpg)

###Filling Out the Form

```php
<?php
require 'pdfForm.php';

$data = [
    'first_name' => 'John',
    'last_name'  => 'Smith',
    'occupation' => 'Teacher',
    'age'        => '45',
    'gender'     => 'male'
];

$pdf = new pdfForm('form.pdf', $data);

$pdf->flatten()
    ->save('output.pdf')
    ->download();
```

Data can be fetched from different sources like a database table, a JSON object or just an array as we did in above snippet.

![raw-form](filled_form.jpg)


###Creating a FDF File

If we just need to create a FDF file without filling out a form, we can only use `make_fdf()` method.

```php
<?php
require 'pdfForm.php';

$data = [
    'first_name' => 'John',
    'last_name'  => 'Smith',
    'occupation' => 'Teacher',
    'age'        => '45',
    'gender'     => 'male'
];

$pdf = new pdfForm('form.pdf', $data);

$fdf = $pdf->make_fdf();
```
The return value of `make_fdf()` is the path to the generated FDF file in the `tmp` directory. You can either get the content of the file or save it to a permanent location.

###Extracting PDF Field Information

If we just need to see what fields and field types exist in the form,  we can call the `fields()` method to get the information:

```php
<?php

require 'pdfForm.php';

$fields = new pdfForm('form.pdf')->fields();

echo $fields;

```

If there's no need to parse the output, we can pass `true` to the `fields()` method to get a human readable output:

```php
<?php

require 'pdfForm.php';

$pdf = new pdfForm('pdf-test.pdf')->fields(true);

echo $pdf;

```