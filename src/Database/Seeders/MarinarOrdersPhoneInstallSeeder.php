<?php
    namespace Marinar\OrdersPhone\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Marinar\OrdersPhone\MarinarOrdersPhone;

    class MarinarOrdersPhoneInstallSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public static function configure() {
            static::$packageName = 'marinar_orders_phone';
            static::$packageDir = MarinarOrdersPhone::getPackageMainDir();
        }

        public function run() {
            if(!in_array(env('APP_ENV'), ['dev', 'local'])) return;

            $this->autoInstall();

            $this->refComponents->info("Done!");
        }

    }
