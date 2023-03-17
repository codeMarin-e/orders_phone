<?php
    namespace Marinar\OrdersPhone\Database\Seeders;

    use App\Models\PaymentMethod;
    use Illuminate\Database\Seeder;
    use Marinar\OrdersPhone\MarinarOrdersPhone;
    use Spatie\Permission\Models\Permission;

    class MarinarOrdersPhoneRemoveSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public static function configure() {
            static::$packageName = 'marinar_orders_phone';
            static::$packageDir = MarinarOrdersPhone::getPackageMainDir();
        }

        public function run() {
            if(!in_array(env('APP_ENV'), ['dev', 'local'])) return;

            $this->autoRemove();

            $this->refComponents->info("Done!");
        }

        public function clearMe() {
            $this->refComponents->task("Clear DB", function() {
                Permission::whereIn('name', [
                    'orders.order_phone_view',
                    'orders.order_phone_create',
                ])
                ->where('guard_name', 'admin')
                ->delete();
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                return true;
            });
        }
    }
