<?php
	return [
		'install' => [
            'php artisan db:seed --class="\Marinar\OrdersPhone\Database\Seeders\MarinarOrdersPhoneInstallSeeder"',
		],
		'remove' => [
            'php artisan db:seed --class="\Marinar\OrdersPhone\Database\Seeders\MarinarOrdersPhoneRemoveSeeder"',
        ]
	];
