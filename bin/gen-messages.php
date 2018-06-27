#!/usr/bin/env php
<?php
$entities = [ "clients", "clientsgroups", "colors", "complaints", "defectives", "deliveries", "invoiceitems", "invoices", "models", "notes", "orders", "positions", "pricelistitems", "pricelists", "prices", "productions", "products", "settings", "sizes", "trims", "uploads", "users", "usersgroups"];

$def=[
    'label' =>[
        'id' => 'ID',
        'actions' => 'Akcje',
        'eactions' => [
            'edit' => '',
            'show' => '',
            'delete' => ''
        ],
        'btn' => [
            'add' => 'Dodaj',
            'cancel' => 'Anuluj',
            'change' => 'Zmień',
            'clear' => 'Wyczyść',
            'close' => 'Zamknij',
            'confirm' => 'Zatwierdź',
            'create' => 'Zapisz',
            'default' => 'Domyślnie',
            'delete' => 'Usuń',
            'edit' => 'Edycja',
            'filter' => 'Filtruj',
            'new' => 'Dodaj',
            'remove' => 'Usuń',
            'save' => 'Zapisz',
            'submit' => 'Zapisz',
            'update' => 'Zapisz'
        ],
        'tools' => [
            'new' => 'Dodaj'
        ],
        'filters' => [

        ]

    ],
    'message' => [

    ],
    'warning' => [

    ],
    'title' => [
        'id' => 'ID',
        'actions' => 'Akcje',
        'eactions' => [
            'edit' => 'Edycja',
            'show' => 'Podgląd',
            'delete' => 'Usunięcie'
        ],
        'btn' => [
            'add' => 'Dodaj',
            'cancel' => 'Anuluj',
            'change' => 'Zmień',
            'clear' => 'Wyczyść',
            'close' => 'Zamknij',
            'confirm' => 'Zatwierdź',
            'create' => 'Zapisz',
            'default' => 'Domyślnie',
            'delete' => 'Usuń',
            'edit' => 'Edycja',
            'filter' => 'Filtruj',
            'new' => 'Dodaj',
            'remove' => 'Usuń',
            'save' => 'Zapisz',
            'submit' => 'Zapisz',
            'update' => 'Zapisz'
        ],
        'tools' => [
            'new' => 'Dodaj'
        ],
        'filters' => [

        ]
    ]
];

$messages_php=__DIR__.'/messages.php';
$trans_dir=__DIR__.'/../src/AppBundle/Resources/translations/';
$messages_yaml='messages.pl.yml';
$new_yaml='new_messages.pl.yml';
$def_yaml='def.pl.yml';

// require $trans_dir.$def_yaml;

function getEntities(){
	global $messages_php;
	$files = [];
	foreach (glob(__DIR__."/../src/AppBundle/Entity/*.php") as $file) {
		$files[] = strtolower(basename($file, '.php'));
	}
	var_dump($files);

	file_put_contents($messages_php, '$entities = [ "'. join('", "', $files) . '"];' , FILE_APPEND); 
}

function setDefault(){
	global $trans_dir, $messages_yaml, $new_yaml, $def_yaml, $entities, $def;
	$ndocs=0;
	$parsed = yaml_parse_file($trans_dir . $messages_yaml, 0, $ndocs);
	$sets = yaml_parse_file($trans_dir . $def_yaml, 0, $ndocs);

	foreach($entities as $a){
		if(array_key_exists($a, $parsed)){
			$parsed[$a]=array_replace_recursive($sets, $parsed[$a]);
		}
	}
	// var_dump($sets);
	yaml_emit_file ($trans_dir . $new_yaml, $parsed, YAML_UTF8_ENCODING );
	
}

if(count($argv) > 1 && function_exists($argv[1])){
	$argv[1]();
}

yaml_emit_file($trans_dir . $def_yaml, $def, YAML_UTF8_ENCODING );


// print_r($argv);