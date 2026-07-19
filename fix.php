<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::all();
foreach($users as $user) {
    $inc = App\Models\Category::firstOrCreate(
        ['user_id' => $user->id, 'name' => 'Lain-lain', 'type' => 'income'],
        ['color' => 'bg-zinc-500', 'icon' => 'squares-2x2']
    );
    $exp = App\Models\Category::firstOrCreate(
        ['user_id' => $user->id, 'name' => 'Lain-lain', 'type' => 'expense'],
        ['color' => 'bg-zinc-500', 'icon' => 'squares-2x2']
    );
    App\Models\Transaction::where('user_id', $user->id)->where('type', 'income')->whereNull('category_id')->update(['category_id' => $inc->id]);
    App\Models\Transaction::where('user_id', $user->id)->where('type', 'expense')->whereNull('category_id')->update(['category_id' => $exp->id]);
}
echo "Migration complete\n";
