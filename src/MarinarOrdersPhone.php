<?php
    namespace Marinar\OrdersPhone;

    use Marinar\OrdersPhone\Database\Seeders\MarinarOrdersPhoneInstallSeeder;

    class MarinarOrdersPhone {

        public static function getPackageMainDir() {
            return __DIR__;
        }

        public static function injects() {
            return MarinarOrdersPhoneInstallSeeder::class;
        }
    }
