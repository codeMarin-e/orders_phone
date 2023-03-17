<?php
return [
    implode(DIRECTORY_SEPARATOR, [ base_path(), 'resources', 'views', 'components', 'admin', 'box_sidebar.blade.php']) => [
        "{{--  @HOOK_ADMIN_SIDEBAR  --}}" => "\t<x-admin.sidebar.orders_phone_option />\n",
    ],
    implode(DIRECTORY_SEPARATOR, [ base_path(), 'config', 'marinar_orders.php']) => [
        "// @HOOK_ORDERS_CONFIGS_ADDONS" => "\t\t\\Marinar\\OrdersPhone\\MarinarOrdersPhone::class, \n"
    ],
    implode(DIRECTORY_SEPARATOR, [ base_path(), 'app', 'Policies', 'CartPolicy.php']) => [
        "{{--  @HOOK_ADMIN_SIDEBAR  --}}" => implode(DIRECTORY_SEPARATOR, [__DIR__, 'HOOK_POLICY_END.php_tpl']),
    ],
];
