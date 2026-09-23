<?php

namespace App\Providers;

use App\Models\Channel;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\Group;
use App\Models\NongkrongSession;
use App\Policies\ChannelPolicy;
use App\Policies\DebtPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\GroupPolicy;
use App\Policies\NongkrongSessionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Group::class => GroupPolicy::class,
        Channel::class => ChannelPolicy::class,
        NongkrongSession::class => NongkrongSessionPolicy::class,
        Expense::class => ExpensePolicy::class,
        Debt::class => DebtPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
