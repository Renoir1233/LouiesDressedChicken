<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\StockInTransaction;
use App\Models\StockOutTransaction;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define polymorphic morph map
        Relation::enforceMorphMap([
            'stock_in_transaction' => StockInTransaction::class,
            'stock_out_transaction' => StockOutTransaction::class,
        ]);
    }
}
