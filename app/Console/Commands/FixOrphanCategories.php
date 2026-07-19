<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;

class FixOrphanCategories extends Command
{
    protected $signature = 'fix:orphan-categories';

    protected $description = 'Create default "Lain-lain" categories for users and assign orphan transactions (null category_id)';

    public function handle(): int
    {
        if (! $this->confirm('This will create default categories and reassign orphan transactions. Continue?')) {
            return self::SUCCESS;
        }

        $users = User::withoutTrashed()->get();
        $totalCategories = 0;
        $totalTransactions = 0;

        foreach ($users as $user) {
            $inc = Category::firstOrCreate(
                ['user_id' => $user->id, 'name' => 'Lain-lain', 'type' => 'income'],
                ['color' => 'bg-zinc-500', 'icon' => 'squares-2x2']
            );
            $exp = Category::firstOrCreate(
                ['user_id' => $user->id, 'name' => 'Lain-lain', 'type' => 'expense'],
                ['color' => 'bg-zinc-500', 'icon' => 'squares-2x2']
            );

            $totalCategories += 2;

            $updated = Transaction::withoutTrashed()
                ->where('user_id', $user->id)
                ->where('type', 'income')
                ->whereNull('category_id')
                ->update(['category_id' => $inc->id]);

            $totalTransactions += $updated;

            $updated = Transaction::withoutTrashed()
                ->where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereNull('category_id')
                ->update(['category_id' => $exp->id]);

            $totalTransactions += $updated;
        }

        $this->info("Done. Created {$totalCategories} categories, reassigned {$totalTransactions} orphan transactions.");

        return self::SUCCESS;
    }
}
